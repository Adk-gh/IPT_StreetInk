<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class ArtUploadController extends Controller
{
    /**
     * Display a single post
     */
    public function show($id)
    {
        try {
            $post = Post::with(['user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'post' => $post,
                'user' => $post->user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }
    }

    /**
     * Delete a post
     */
    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);

            // Delete the associated image file if it exists
            if ($post->image_url) {
                $filePath = public_path('storage/' . $post->image_url);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Artwork deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete artwork: ' . $e->getMessage()
            ], 500);
        }
    }
}
