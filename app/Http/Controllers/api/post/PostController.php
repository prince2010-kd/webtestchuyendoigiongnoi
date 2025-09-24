<?php

namespace App\Http\Controllers\api\post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index() {
        $posts = Post::all(); // lấy tất cả bài viết
        return response()->json([
            'status' => true,
            'data' => $posts
        ]);
    }

    // GET /api/posts/{slug}
    public function getDetail($slug)
    {
        $post = Post::where('url', $slug)->first();

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Bài viết không tồn tại'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $post
        ]);
    }
}
