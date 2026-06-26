<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $forums = Forum::orderBy('position')->get();

        if (! $request->hasAny(['keywords', 'author', 'forum_id'])) {
            return view('search.index', compact('forums'));
        }

        return $this->results($request);
    }

    public function results(Request $request): View
    {
        $request->validate([
            'keywords'  => ['nullable', 'string', 'max:200'],
            'forum_id'  => ['nullable', 'string'],
            'author'    => ['nullable', 'string', 'max:25'],
            'search_in' => ['nullable', 'in:all,subjects,posts'],
        ]);

        $query = $request->input('keywords', '');

        $forumId = $request->input('forum_id');
        $author = $request->input('author');
        $searchIn = $request->input('search_in', 'all');

        if (empty($query) && empty($author)) {
            $forums = Forum::orderBy('position')->get();
            return view('search.index', compact('forums'));
        }

        $results = collect();

        $forumNames = Forum::pluck('name', '_id')->toArray();

        if (in_array($searchIn, ['all', 'subjects'])) {
            $topicQuery = Topic::where('subject', 'regexp', '/' . preg_quote($query, '/') . '/i');

            if ($forumId) {
                $topicQuery->where('forum_id', $forumId);
            }

            if ($author) {
                $topicQuery->where('poster', 'regexp', '/' . preg_quote($author, '/') . '/i');
            }

            $topics = $topicQuery->orderBy('posted', 'desc')->limit(50)->get();

            foreach ($topics as $topic) {
                $firstPost = Post::where('topic_id', (string) $topic->_id)
                    ->orderBy('posted', 'asc')
                    ->first();

                if ($firstPost) {
                    $results->push((object) [
                        'topic_id'    => (string) $topic->_id,
                        'id'          => (string) $topic->_id,
                        'post_id'     => (string) $firstPost->_id,
                        'subject'     => $topic->subject,
                        'poster'      => $topic->poster,
                        'poster_id'   => $topic->poster_id,
                        'forum_id'    => $topic->forum_id,
                        'forum_name'  => $forumNames[$topic->forum_id] ?? '',
                        'posted'      => $topic->posted,
                        'num_replies' => $topic->num_replies,
                        'num_views'   => $topic->num_views,
                        'message'     => $firstPost->message,
                    ]);
                }
            }
        }

        if (in_array($searchIn, ['all', 'posts'])) {
            $postQuery = Post::where('message', 'regexp', '/' . preg_quote($query, '/') . '/i');

            if ($forumId) {
                $postQuery->where('forum_id', $forumId);
            }

            if ($author) {
                $postQuery->where('poster', 'regexp', '/' . preg_quote($author, '/') . '/i');
            }

            $posts = $postQuery->orderBy('posted', 'desc')->limit(50)->get();

            foreach ($posts as $post) {
                $topic = Topic::find($post->topic_id);
                if ($topic) {
                    $results->push((object) [
                        'topic_id'    => (string) $topic->_id,
                        'id'          => (string) $topic->_id,
                        'post_id'     => (string) $post->_id,
                        'subject'     => $topic->subject,
                        'poster'      => $post->poster,
                        'poster_id'   => $post->poster_id,
                        'forum_id'    => $post->forum_id,
                        'forum_name'  => $forumNames[$post->forum_id] ?? '',
                        'posted'      => $post->posted,
                        'num_replies' => null,
                        'num_views'   => null,
                        'message'     => $post->message,
                    ]);
                }
            }
        }

        $results = $results->unique(fn ($item) => $item->post_id)
            ->sortByDesc(fn ($item) => $item->posted)
            ->values();

        $perPage = 25;
        $page = $request->input('page', 1);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $results->slice(($page - 1) * $perPage, $perPage)->values(),
            $results->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        $forums = Forum::orderBy('position')->get();

        $results = $paginated;

        return view('search.results', compact('results', 'forums', 'query'));
    }
}
