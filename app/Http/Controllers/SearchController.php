<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use MongoDB\Driver\Exception\RuntimeException;

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
            'keywords' => ['nullable', 'string', 'max:200'],
            'forum_id' => ['nullable', 'string'],
            'author' => ['nullable', 'string', 'max:25'],
            'search_in' => ['nullable', 'in:all,subjects,posts'],
        ]);

        $keywords = trim($request->input('keywords', ''));
        $forumId = $request->input('forum_id');
        $author = trim($request->input('author', ''));
        $searchIn = $request->input('search_in', 'all');

        if ($keywords === '' && $author === '') {
            $forums = Forum::orderBy('position')->get();

            return view('search.index', compact('forums'));
        }

        $forumNames = Forum::pluck('name', '_id')->toArray();

        $rawResults = $keywords !== ''
            ? $this->atlasSearch($keywords, $searchIn, $forumId, $author, $forumNames)
            : $this->authorSearch($author, $forumId, $forumNames);

        $perPage = 25;
        $page = max(1, (int) $request->input('page', 1));
        $paginated = new LengthAwarePaginator(
            $rawResults->slice(($page - 1) * $perPage, $perPage)->values(),
            $rawResults->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        $forums = Forum::orderBy('position')->get();
        $results = $paginated;
        $query = $keywords;

        return view('search.results', compact('results', 'forums', 'query'));
    }

    /** Full-text search using MongoDB Atlas Search $search aggregation. Falls back to regex when Atlas is unavailable. */
    private function atlasSearch(
        string $keywords,
        string $searchIn,
        ?string $forumId,
        string $author,
        array $forumNames,
    ): Collection {
        // Skip Atlas Search in test environment: newly inserted documents are not immediately indexed.
        if (app()->environment('testing')) {
            return $this->regexSearch($keywords, $searchIn, $forumId, $author, $forumNames);
        }

        try {
            return $this->atlasSearchPipeline($keywords, $searchIn, $forumId, $author, $forumNames);
        } catch (RuntimeException $e) {
            // Atlas Search not available (e.g. plain MongoDB without mongot) — fall back to regex.
            return $this->regexSearch($keywords, $searchIn, $forumId, $author, $forumNames);
        }
    }

    private function atlasSearchPipeline(
        string $keywords,
        string $searchIn,
        ?string $forumId,
        string $author,
        array $forumNames,
    ): Collection {
        $results = collect();

        if (in_array($searchIn, ['all', 'subjects'])) {
            $pipeline = $this->buildAtlasPipeline('topics', $keywords, $forumId, $author, 'subject');
            $topics = Topic::raw(fn ($col) => $col->aggregate($pipeline));

            foreach ($topics as $topic) {
                $firstPost = $topic->posts()->orderBy('posted', 'asc')->first();
                if ($firstPost) {
                    $results->push($this->topicResult($topic, $firstPost, $forumNames));
                }
            }
        }

        if (in_array($searchIn, ['all', 'posts'])) {
            $pipeline = $this->buildAtlasPipeline('posts', $keywords, $forumId, $author, 'message');
            $posts = Post::raw(fn ($col) => $col->aggregate($pipeline));

            foreach ($posts as $post) {
                $topic = $post->topic;
                if ($topic) {
                    $results->push($this->postResult($post, $topic, $forumNames));
                }
            }
        }

        return $results
            ->unique(fn ($item) => $item->post_id)
            ->sortByDesc(fn ($item) => $item->posted)
            ->values();
    }

    /** Regex fallback when Atlas Search is not available. */
    private function regexSearch(
        string $keywords,
        string $searchIn,
        ?string $forumId,
        string $author,
        array $forumNames,
    ): Collection {
        $results = collect();
        $pattern = '/'.preg_quote($keywords, '/').'/i';

        if (in_array($searchIn, ['all', 'subjects'])) {
            $q = Topic::where('subject', 'regexp', $pattern);
            if ($forumId) {
                $q->where('forum_id', $forumId);
            }
            if ($author !== '') {
                $q->where('poster', $author);
            }

            foreach ($q->orderBy('posted', 'desc')->limit(50)->get() as $topic) {
                $firstPost = $topic->posts()->orderBy('posted', 'asc')->first();
                if ($firstPost) {
                    $results->push($this->topicResult($topic, $firstPost, $forumNames));
                }
            }
        }

        if (in_array($searchIn, ['all', 'posts'])) {
            $q = Post::where('message', 'regexp', $pattern);
            if ($forumId) {
                $q->where('forum_id', $forumId);
            }
            if ($author !== '') {
                $q->where('poster', $author);
            }

            foreach ($q->orderBy('posted', 'desc')->limit(50)->get() as $post) {
                $topic = $post->topic;
                if ($topic) {
                    $results->push($this->postResult($post, $topic, $forumNames));
                }
            }
        }

        return $results
            ->unique(fn ($item) => $item->post_id)
            ->sortByDesc(fn ($item) => $item->posted)
            ->values();
    }

    /** Search by author only (no full-text), used when keywords are empty. */
    private function authorSearch(string $author, ?string $forumId, array $forumNames): Collection
    {
        $results = collect();

        $postQuery = Post::where('poster', $author);
        if ($forumId) {
            $postQuery->where('forum_id', $forumId);
        }

        foreach ($postQuery->orderBy('posted', 'desc')->limit(100)->get() as $post) {
            $topic = $post->topic;
            if ($topic) {
                $results->push($this->postResult($post, $topic, $forumNames));
            }
        }

        return $results->unique(fn ($item) => $item->post_id)->values();
    }

    /** Build an Atlas Search aggregation pipeline with optional post-filters. */
    private function buildAtlasPipeline(
        string $collection,
        string $keywords,
        ?string $forumId,
        string $author,
        string $searchField,
    ): array {
        $searchStage = [
            '$search' => [
                'index' => 'default',
                'text' => [
                    'query' => $keywords,
                    'path' => $searchField,
                ],
            ],
        ];

        $pipeline = [$searchStage];

        // Post-filters applied after the $search stage.
        $filters = [];
        if ($forumId) {
            $filters[] = ['forum_id' => $forumId];
        }
        if ($author !== '') {
            $filters[] = ['poster' => $author];
        }
        if ($filters) {
            $pipeline[] = ['$match' => array_merge(...$filters)];
        }

        $pipeline[] = ['$limit' => 50];

        return $pipeline;
    }

    private function topicResult(Topic $topic, Post $firstPost, array $forumNames): object
    {
        return (object) [
            'topic_id' => $topic->id,
            'id' => $topic->id,
            'post_id' => $firstPost->id,
            'subject' => $topic->subject,
            'poster' => $topic->poster,
            'poster_id' => $topic->poster_id,
            'forum_id' => $topic->forum_id,
            'forum_name' => $forumNames[$topic->forum_id] ?? '',
            'posted' => $topic->posted,
            'num_replies' => $topic->num_replies,
            'num_views' => $topic->num_views,
            'message' => $firstPost->message,
        ];
    }

    private function postResult(Post $post, Topic $topic, array $forumNames): object
    {
        return (object) [
            'topic_id' => $topic->id,
            'id' => $topic->id,
            'post_id' => $post->id,
            'subject' => $topic->subject,
            'poster' => $post->poster,
            'poster_id' => $post->poster_id,
            'forum_id' => $post->forum_id,
            'forum_name' => $forumNames[$post->forum_id] ?? '',
            'posted' => $post->posted,
            'num_replies' => null,
            'num_views' => null,
            'message' => $post->message,
        ];
    }
}
