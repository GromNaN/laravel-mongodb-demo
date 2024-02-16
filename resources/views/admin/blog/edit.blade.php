<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1>Edit article #{{ $post->id }}</h1>
                    <form action="{{ route('admin_post_update', $post) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
                            <input type="text" name="title" id="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required
                                   value="{{ $post->title }}">
                        </div>

                        <div class="mb-6">
                            <label for="summary" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Summary</label>
                            <textarea name="summary" id="summary" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                      required>{{ $post->summary }}</textarea>
                        </div>

                        <div class="mb-6">
                            <label for="content" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Content</label>
                            <textarea name="content" id="content" rows="6" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                      required>{{ $post->content }}</textarea>
                        </div>

                        <div class="mb-6">
                            <label for="published_at" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Published At</label>
                            <input type="datetime-local" name="published_at" id="published_at" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                   required value="{{ $post->published_at->format('Y-m-d\TH:i') }}">
                        </div>

                        <div class="mb-6">
                            <label for="tags" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tags</label>
                            <input type="text" name="tags" id="tags" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                   value="{{ implode(', ', $post->tags()->pluck('name')->toArray())  }}">
                        </div>

                        <button type="submit" class="bg-sky-500 hover:bg-sky-700 px-5 py-2 mx-2 text-sm leading-5 rounded-full font-semibold text-white">
                            Save
                        </button>

                        <a href="{{ route('admin_post_index') }}" class="bg-gray-500 hover:bg-gray-700 px-5 py-2 mx-2 text-sm leading-5 rounded-full font-semibold text-white">
                            Back to article list
                        </a>
                    </form>
                    <div class="mb-6 py-2.5">
                        <form action="{{ route('admin_post_delete', $post) }}" method="DELETE">
                            <button type="submit" class="bg-red-500 hover:bg-red-700 px-5 py-2 mx-2 text-sm leading-5 rounded-full font-semibold text-white">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
