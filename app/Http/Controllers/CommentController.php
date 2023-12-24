<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Subpage;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $slug, $postSlug)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
    
        // First, get the correct subpage using its slug
        // - this is necessary because the post_slug only is unique for the subpage
        $subpage = Subpage::where('slug', $slug)->firstOrFail();
    
        // Then, find the post using its slug and make sure it belongs to the subpage
        $post = Post::where('slug', $postSlug)->where('subpage_id', $subpage->id)->firstOrFail();
    
        // Now create the comment
        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = Auth::id();
        $comment->post_id = $post->id;
        $comment->save();

        $commentHtml = view('components.comment-template', [
            'profileName' => Auth::user()->name,
            'content' => $comment->content,
            'createdAt' => $comment->created_at->diffForHumans(),
            'comment' => $comment
        ])->render();
        
        $response = [
            'status' => 'success',
            'message' => 'Comment posted successfully!',
            'commentHtml' => $commentHtml 
        ];
    
        return response()->json($response);
    }


    public function toggleLike(Comment $comment)
    {
        if ($comment->isLikedByUser(auth()->user())) {
            $comment->likes()->detach(auth()->id());
        } else {
            $comment->likes()->attach(auth()->id());
        }

        return back();
    }

    public function destroy(Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $comment->delete();

        return back()->with('status', 'Comment deleted successfully.');
    }
}

