<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    public function updateStatus(Request $request, Post $post)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $post->status = $request->status;
        $post->save();

        return response()->json([
            'success' => true,
            'message' => "Post status updated to {$request->status}."
        ]);
    }
}
