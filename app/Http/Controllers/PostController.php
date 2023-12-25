<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Subpage;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;




class PostController extends Controller
{
     // Show a form for creating a new post in a subpage
     public function create(Subpage $subpage)
     {
         return view('posts.create', compact('subpage'));
     }
 
     public function store(Request $request, $slug)
     {
         $subpage = Subpage::where('slug', $slug)->firstOrFail();
     
         $request->validate([
             'title' => 'required|max:255',
             'content' => 'required',
         ]);
     
         $postSlug = Str::slug($request->title);
     
         // Ensure uniqueness of the post slug within the subpage
         $postSlugCount = Post::where('subpage_id', $subpage->id)
                              ->where('slug', 'LIKE', $postSlug . '%')
                              ->count();
     
         $post = new Post();
         $post->title = $request->title;
         $post->content = $request->content;
         $post->user_id = Auth::id();
         $post->subpage_id = $subpage->id;
         $post->slug = $postSlugCount > 0 ? "{$postSlug}-{$postSlugCount}" : $postSlug;
     
         $post->save();
     
         $postHtml = view('components.blog-template', [
                'profileName' => Auth::user()->name,
                'title' => $post->title,
                'content' => $post->content,
                'createdAt' => Carbon::parse($post->created_at),
                'post' => $post,
                'showSubpageName' => true, 
                'subpageName' => $post->subpage->name,
                'subpage_slug' => $post->subpage->slug,
                'post_slug' => $post->slug
            ])->render();
     
         // Return a JSON response with the rendered HTML
         $response = [
             'status' => 'success',
             'message' => 'Post created successfully.',
             'postHtml' => $postHtml
         ];
     
         return response()->json($response);
     }
     


    public function toggleLike($slug, $post_slug)
    {
        // Get the correct subpage using its slug
        $subpage = Subpage::where('slug', $slug)->firstOrFail();

        // Get the post by its slug and make sure it belongs to the correct subpage
        $post = Post::where('slug', $post_slug)->where('subpage_id', $subpage->id)->firstOrFail();

        // Toggle the like for the correct post
        $like = $post->likes()->where('user_id', auth()->id())->first();

        if ($like) {
            $like->delete();
        } else {
            $post->likes()->create(['user_id' => auth()->id()]);
        }

        // Calculate the updated likes count
        $updatedLikesCount = $post->likes()->count();

        // Return the updated likes count as JSON
        return response()->json([
            'likesCount' => $updatedLikesCount
        ]);
    }


    
    public function destroy($slug, $postSlug)
    {
         // First, get the correct subpage using its slug
        // - this is necessary because the post_slug only is unique for the subpage
        $subpage = Subpage::where('slug', $slug)->firstOrFail();

        // Then, get the post by its slug and make sure it belongs to the correct subpage
        $post = Post::where('slug', $postSlug)->where('subpage_id', $subpage->id)->firstOrFail();
    
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
    
        $post->delete();
    
        return back()->with('status', 'Post deleted successfully.');
    }




}