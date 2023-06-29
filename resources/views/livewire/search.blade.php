<div>
    <div class="px-4 space-y-4 mt-8">
        <form method="get">
            <input class="border-solid border border-gray-300 p-2 w-full md:w-1/4"
                   type="text" placeholder="Search Posts" wire:model="term"/>
        </form>
        <div wire:loading>Searching posts...</div>
        <div wire:loading.remove>
            <!--
                notice that $term is available as a public
                variable, even though it's not part of the
                data array
            -->
            @if ($term == "")
                <div class="text-gray-500 text-sm">
                    Enter a term to search for posts.
                </div>
            @else
                @if($posts->isEmpty())
                    <div class="text-gray-500 text-sm">
                        No matching result was found.
                    </div>
                @else
                    @foreach($posts as $post)
                        <div>
                            <h3 class="text-lg text-gray-900 text-bold">{{$post->title}}</h3>
                            <p class="text-gray-500 text-sm">{{$post->published_at}}</p>
                            <p class="text-gray-500">{{$post->summary}}</p>
                        </div>
                    @endforeach
                    <div class="px-4 mt-4">
                        {{$posts->links()}}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
