<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="">Article list</h1>

                    <a href="{{ route('admin_post_new') }}" class="bg-orange-500 hover:bg-orange-700 px-5 py-2 mx-2 text-sm leading-5 rounded-full font-semibold text-white">
                        Create a new article
                    </a>

                    <form class="max-w-sm mx-auto">
                        <label for="nbTags" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Filter by number of tags :</label>
                        <select onchange="window.location.href = this.value" id="nbTags" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5">
                            <option value="{{ route('admin_post_index') }}" {{ $nbTags ? '' : 'selected' }}>&nbsp;</option>
                            @foreach($nbTagsChoices as $number)
                                <option
                                    value="{{ route('admin_post_index', ['nbTags' => $number]) }}" {{ $nbTags == $number ? 'selected' : '' }}>{{ $number }}</option>
                            @endforeach
                        </select>
                    </form>

                    <table class="border-collapse table-auto w-full">
                        <thead>
                        <tr>
                            <th scope="col" class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Title</th>
                            <th scope="col" class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Published</th>
                            <th scope="col" class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Tags</th>
                            <th scope="col" class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($paginator as $post)
                            <tr>
                                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                    <a href="{{ route('blog_post', [$post->slug]) }}" class="font-medium text-blue-600 hover:underline">
                                        {{ $post->title }}
                                    </a>
                                </td>
                                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">{{ $post->published_at }}</td>
                                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">{{ implode(', ', array_column($post->tags->toArray(), 'name')) }}</td>
                                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                    <a href="{{ route('admin_post_edit', [$post]) }}" class="bg-lime-500 hover:bg-lime-700 px-5 py-2 text-sm leading-5 rounded-full font-semibold text-white">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" align="center">No posts found</td>
                            </tr>
                        @endforelse
                        <tfoot>
                            <tr>
                                <td colspan="3">
                                    {{ $paginator->links() }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
