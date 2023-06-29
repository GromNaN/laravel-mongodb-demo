<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     *
     * @see https://github.com/symfony/demo/blob/main/src/Controller/BlogController.php#L51
     */
    public function index()
    {
        $latestPosts = Post::paginate(10)->sortBy('published_at');

        return view('blog/index', [
            'paginator' => $latestPosts,
        ]);
    }

    public function postShow($slug)
    {
        $post = Post::findBySlug($slug);

        return view('blog/post', [
            'post' => $post,
        ]);
    }

    public function commentNew($id, Request $request)
    {
        $post = Post::find($id);

        $validatedData = $request->validate([
            'content' => 'required',
        ]);

        $comment = new Comment();
        $comment->content = $validatedData['content'];
        $comment->published_at = new \DateTimeImmutable();
        $comment->author()->associate(Auth::user());
        $comment->post()->associate($post);
        $comment->save();

        return redirect()->back()->with('success', 'Thank you for your comment!');
    }

    public function search(Request $request)
    {
        $term = $request->input('term');

        return view('blog/search', [
            'term' => $term,
        ]);
    }
}
