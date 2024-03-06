@foreach ($paginator as $post)
    <div>
        <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">
            <a href="{{ route('blog_post', ['slug' => $post->slug]) }}">
                {{ $post->title }}
            </a>
        </h2>
        <p>
            Published
            <time
                datetime="{{ $post->published_at->format(DATE_ATOM) }}">{{ $post->published_at->diffForHumans() }}</time>
            by {{ $post->author->name }}
        </p>
        <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
            {{ $post->summary }}
        </p>
    </div>
@endforeach
