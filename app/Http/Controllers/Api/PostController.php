<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostManagement\CreatePost;
use App\Http\Requests\Admin\PostManagement\UpdatePost;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{


    public function store(CreatePost $request)
    {
        $user = auth()->user();
        try {
            $post = Post::create([
                'title' => $request->title,
                'content' => $request->content,
                'user_id' => $user->id,
            ]);
            return response()->json([
                'message' => 'Post created successfully',
                'data' => $post,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdatePost $request, $id)
    {
        $user = auth()->user();
        try {
            $post = Post::where('user_id', $user->id)->find($id);
            if (!$post) {
                return response()->json([
                    'message' => 'Post not found',
                ], 404);
            }
            $post->update($request->validated());
            return response()->json([
                'message' => 'Post updated successfully',
                'data' => $post,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $post = auth()->user()->posts()->find($id);
            if (!$post) {
                return response()->json([
                    'message' => 'Post not found',
                ], 404);
            }
            $post->delete();
            return response()->json([
                'message' => 'Post deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function index()
    {
        try {
            $page = request()->get('page', 1);
            $limit = request()->get('limit', 10);
            $q = request()->get('q', null);
            $posts = Post::with('user')
                ->where('title', 'like', "%$q%")
                ->orWhere('content', 'like', "%$q%")
                ->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'message' => 'Posts retrieved successfully',
                'data' => $posts->items(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'total_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total_data' => $posts->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
