<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\Posts;
use App\Models\Permission;
use App\Http\Controllers\Controller;
use App\Services\ElasticService;
use Illuminate\Support\Facades\Log;

class PostsController extends Controller
{
    protected $elastic;

    public function index(Request $request)
    {
        $posts = Posts::latest()->paginate(10);
        if ($request->ajax()) {
            $table = view('admin.posts.list', compact('posts'))->render();
            $pagination = view('admin.component.pagination', compact('posts'))->render();
            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
            ]);
        }
        return view('admin.posts.index', compact('posts'));
    }    

    public function store(Request $request)
    {
        $post = Posts::create($request->only(['name', 'description']));

        return response()->json(['message' => 'Post created and automatically indexed', 'data' => $post]);
    }

    public function update(Request $request, $id)
    {
        $post = Posts::findOrFail($id);
        $post->update($request->only(['name', 'description']));

        return response()->json(['message' => 'Post updated and Elasticsearch synced']);
    }

    public function destroy($id)
    {
        $post = Posts::findOrFail($id);
        $post->delete();

        return response()->json(['message' => 'Post deleted from DB and Elasticsearch']);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $results = $this->elastic->search('posts', $query, ['title', 'url']);

        $hits = $results['hits']['hits'] ?? [];

        if (empty($hits)) {
            Log::info("No results for query '{$query}'");
        }

        $data = collect($hits)->pluck('_source');

        return response()->json(['data' => $data]);
    }
}
