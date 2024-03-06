<x-app-layout>
<article class="bg-gray-100 py-8">
    <div class="container mx-auto">
        <div class="flex justify-center">
            <div class="w-full md:w-8/12 lg:w-8/12 xl:w-8/12">
                <div class="bg-white shadow-md rounded-md">
                    <div class="p-6">
                        <h1 class="text-3xl font-bold mb-2">{{ $post->title }}</h1>
                        <p class="text-gray-600">Published <time>{{ $post->published_at->diffForHumans() }}</time> by {{ $post->author->name }}</p>
                    </div>
                    <div class="p-6">
                        <x-markdown>{{ $post->content }}</x-markdown>
                        <h5 class="text-lg font-bold mb-2">Tags</h5>
                        <ul class="list-disc list-inside">
                            @foreach($post->tags as $tag)
                                <li>{{ $tag->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="bg-white shadow-md rounded-md mt-8">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold">Leave a comment</h2>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('blog_post_comment_new', $post) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="content" class="block text-gray-700 font-bold mb-2">Comment:</label>
                                <textarea name="content" id="content" rows="5" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-500"></textarea>
                            </div>

                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Submit Comment</button>
                        </form>
                    </div>
                </div>

                <h2 class="text-2xl font-bold mt-8">Comments</h2>
                @foreach($post->comments as $comment)
                    <div class="bg-white shadow-md rounded-md mt-4">
                        <div class="p-6">
                            <strong>{{ $comment->author->name }}</strong> commented on <time>{{ $comment->published_at }}</time>
                        </div>
                        <div class="p-6">
                            <x-markdown>{{ $comment->content }}</x-markdown>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</article>
</x-app-layout>
