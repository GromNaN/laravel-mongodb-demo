<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\FormRequest\CreatePostRequest;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MongoDB\Collection;
use MongoDB\Driver\Cursor;

class BlogController extends Controller
{
    public function __construct()
    {
        // This controller requires authentication.
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $posts = Post::where('author_id', $user->id);
        if ($nbTags = $request->query->get('nbTags')) {
            $posts->where('tags', 'size', (int) $nbTags);
        }
        $posts = $posts->latest('published_at')
            ->paginate(8);

        // Find the minimum and maximum number of tags across all posts.
        /** @var Collection $collection */
        $collection = DB::getCollection('posts');
        /** @var Cursor $result */
        $result = $collection->aggregate([
            ['$match' => ['deleted_at' => null]],
            // This stage is optional, but you can use it to reshape the documents and include only the necessary
            // fields. In this case, you can exclude all fields except the tags field.
            ['$project' => ['_id' => 0, 'tagCount' => ['$size' => '$tags']]],
            // This stage groups all documents into a single group and calculates the minimum and maximum number of
            // tags across all documents using the $min and $max aggregation operators.
            ['$group' => ['_id' => null, 'minTags' => ['$min' => '$tagCount'], 'maxTags' => ['$max' => '$tagCount']]],
        ]);
        $nbTags = $result->toArray()[0];

        return view('admin/blog/index', [
            'paginator' => $posts,
            'nbTags' => $request->query->get('nbTags', null),
            'nbTagsChoices' => range($nbTags->minTags, $nbTags->maxTags),
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

        // @fixme the entity must be saved before tags can be attached otherwise the tags will not be saved
        $post->save();

        // Split the list into individual tag names and trim whitespace
        $tagNames = array_filter(array_map('trim', explode(',', $data['tags'] ?? '')));

        // Retrieve or create the corresponding Tag models
        $tags = array_map(function ($tagName) {
            return new Tag(['name' => $tagName]);
        }, $tagNames);
        $post->tags()->delete();
        $post->tags()->saveMany($tags);

        return $post;
    }
}
