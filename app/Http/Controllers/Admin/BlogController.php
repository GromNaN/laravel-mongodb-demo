<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\FormRequest\CreatePostRequest;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function __construct()
    {
        // This controller requires authentication.
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $posts = Post::where('author_id', $user->id)
            ->latest('published_at')
            ->paginate(8);

        return view('admin/blog/index', [
            'paginator' => $posts,
        ]);
    }

    public function new()
    {
        return view('admin/blog/new');
    }

    public function create(CreatePostRequest $request)
    {
        $validatedData = $request->validated();

        $post = new Post();
        $post->author()->associate(Auth::user());
        $post->title = $validatedData['title'];
        $post->summary = $validatedData['summary'];
        $post->content = $validatedData['content'];
        $post->published_at = $validatedData['published_at'];
        $post->save();

        return redirect()->route('admin_post_index')->with('success', 'Post created successfully');
    }

    public function edit($id)
    {
        $post = $this->getPost($id);

        return view('admin/blog/edit', [
            'post' => $post,
        ]);
    }

    public function update($id, CreatePostRequest $request)
    {
        $post = $this->getPost($id);
        $validatedData = $request->validated();

        $post->title = $validatedData['title'];
        $post->summary = $validatedData['summary'];
        $post->content = $validatedData['content'];
        $post->published_at = $validatedData['published_at'];
        $post->save();

        return redirect()->route('admin_post_edit', $post)->with('success', 'Post updated successfully');
    }

    public function delete($id)
    {
        $post = $this->getPost($id);
        $post->delete();

        return redirect()->route('admin_post_index')->with('success', 'Post deleted successfully');
    }

    private function getPost($id): Post
    {
        $post = Post::findOrFail($id);

        if ($post->author_id !== Auth::user()->id) {
            abort(403);
        }

        return $post;
    }
}
