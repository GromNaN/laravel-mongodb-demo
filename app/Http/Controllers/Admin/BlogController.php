<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\FormRequest\CreatePostRequest;
use App\Models\Post;
use App\Models\Tag;
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
        $this->hydratePost($post, $validatedData);
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
        $this->hydratePost($post, $validatedData);
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

    private function hydratePost(Post $post, array $data): Post
    {
        $post->title = $data['title'];
        $post->summary = $data['summary'];
        $post->content = $data['content'];
        $post->published_at = $data['published_at'];

        // Split the list into individual tag names and trim whitespace
        $tagNames = array_filter(array_map('trim', explode(',', $data['tags'] ?? '')));

        // Retrieve or create the corresponding Tag models
        $tagIds = array_map(function ($tagName) {
            return Tag::firstOrCreate(['name' => $tagName])->id;
        }, $tagNames);
        $post->tags()->sync($tagIds);

        return $post;
    }
}
