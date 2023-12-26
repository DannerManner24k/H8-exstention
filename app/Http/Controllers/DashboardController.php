<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post; 

class DashboardController extends Controller
{
    public function index()
    {
    // Get the currently authenticated user
    $user = auth()->user();

    // Retrieve all posts from subpages to which the user is subscribed
    $posts = Post::whereHas('subpage.subscribers', function ($query) use ($user) {
        $query->where('user_id', $user->id);
    })->latest()->get(); // 'latest()' will order the posts by creation date, newest first

    // Pass the posts to the view
    return view('dashboard', compact('posts'));
    }
}
