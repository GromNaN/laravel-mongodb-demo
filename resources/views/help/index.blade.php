@extends('layouts.app')

@section('title', 'Help')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    {{-- Forum Rules --}}
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
            Forum Rules
        </div>
        <div class="p-6 text-sm leading-relaxed text-gray-700">
            @if(config('forum.rules'))
                {!! nl2br(e(config('forum.rules'))) !!}
            @else
                <ol class="list-decimal list-inside space-y-2">
                    <li>Be respectful and courteous to all members at all times.</li>
                    <li>No spam, advertising, or self-promotional posts.</li>
                    <li>No hateful, abusive, threatening, or illegal content.</li>
                    <li>Stay on topic within each forum and thread.</li>
                    <li>No impersonating other members or administrators.</li>
                    <li>Use the search function before creating a new topic to avoid duplicates.</li>
                    <li>Do not bump threads unnecessarily.</li>
                    <li>The staff reserves the right to edit, delete, or move any post or topic without notice.</li>
                    <li>Repeated violations may result in a ban.</li>
                </ol>
                <p class="mt-3 text-gray-500">These rules may be updated at any time. It is your responsibility to stay informed.</p>
            @endif
        </div>
    </div>

    {{-- BBCode Reference --}}
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
            BBCode Reference
        </div>
        <div class="p-4">
            <p class="text-sm text-gray-600 mb-4">
                BBCode is a simplified markup language that you can use to format your posts.
                The following tags are available:
            </p>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-200">
                            <th class="text-left px-4 py-2 font-semibold text-gray-700 w-1/3">BBCode</th>
                            <th class="text-left px-4 py-2 font-semibold text-gray-700 w-1/3">Result</th>
                            <th class="text-left px-4 py-2 font-semibold text-gray-700 w-1/3">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[b]bold text[/b]</td>
                            <td class="px-4 py-2.5"><strong>bold text</strong></td>
                            <td class="px-4 py-2.5 text-gray-600">Makes text bold</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[i]italic text[/i]</td>
                            <td class="px-4 py-2.5"><em>italic text</em></td>
                            <td class="px-4 py-2.5 text-gray-600">Makes text italic</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[u]underlined[/u]</td>
                            <td class="px-4 py-2.5"><span class="underline">underlined</span></td>
                            <td class="px-4 py-2.5 text-gray-600">Underlines text</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[s]strikethrough[/s]</td>
                            <td class="px-4 py-2.5"><span class="line-through">strikethrough</span></td>
                            <td class="px-4 py-2.5 text-gray-600">Strikes through text</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[color=red]colored[/color]</td>
                            <td class="px-4 py-2.5"><span class="text-red-600">colored</span></td>
                            <td class="px-4 py-2.5 text-gray-600">Colors the text (use color names or hex)</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[size=14]big[/size]</td>
                            <td class="px-4 py-2.5"><span class="text-base">big</span></td>
                            <td class="px-4 py-2.5 text-gray-600">Changes font size (in pixels)</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[url]https://...[/url]</td>
                            <td class="px-4 py-2.5"><a href="#" class="text-blue-600 underline">https://...</a></td>
                            <td class="px-4 py-2.5 text-gray-600">Creates a hyperlink</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[url=https://...]Link text[/url]</td>
                            <td class="px-4 py-2.5"><a href="#" class="text-blue-600 underline">Link text</a></td>
                            <td class="px-4 py-2.5 text-gray-600">Creates a named hyperlink</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[img]https://...[/img]</td>
                            <td class="px-4 py-2.5 text-gray-500 italic text-xs">(displays image)</td>
                            <td class="px-4 py-2.5 text-gray-600">Embeds an image</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[quote]text[/quote]</td>
                            <td class="px-4 py-2.5">
                                <div class="border-l-4 border-gray-300 pl-2 text-gray-500 italic text-xs">text</div>
                            </td>
                            <td class="px-4 py-2.5 text-gray-600">Creates a quote block</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[quote=Username]text[/quote]</td>
                            <td class="px-4 py-2.5">
                                <div class="border-l-4 border-gray-300 pl-2 text-gray-500 italic text-xs"><strong>Username</strong> wrote: text</div>
                            </td>
                            <td class="px-4 py-2.5 text-gray-600">Named quote block</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[code]code here[/code]</td>
                            <td class="px-4 py-2.5">
                                <code class="bg-gray-100 border border-gray-200 px-1 rounded text-xs font-mono">code here</code>
                            </td>
                            <td class="px-4 py-2.5 text-gray-600">Displays preformatted code</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[list][*]item 1[*]item 2[/list]</td>
                            <td class="px-4 py-2.5">
                                <ul class="list-disc list-inside text-xs">
                                    <li>item 1</li>
                                    <li>item 2</li>
                                </ul>
                            </td>
                            <td class="px-4 py-2.5 text-gray-600">Bulleted list</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-700">[list=1][*]item 1[*]item 2[/list]</td>
                            <td class="px-4 py-2.5">
                                <ol class="list-decimal list-inside text-xs">
                                    <li>item 1</li>
                                    <li>item 2</li>
                                </ol>
                            </td>
                            <td class="px-4 py-2.5 text-gray-600">Numbered list</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- FAQ --}}
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
            Frequently Asked Questions
        </div>
        <div class="p-6 space-y-4">
            @foreach([
                ['How do I create a new topic?', 'Navigate to the forum where you want to post, then click the "New Topic" button at the top of the topic list. You must be logged in to post.'],
                ['How do I reply to a topic?', 'Open the topic you want to reply to, then click "Post Reply" at the top or bottom of the post list. You can also use the Quick Reply box at the bottom.'],
                ['How do I edit my post?', 'Click the "Edit" link that appears below your post. You may only edit your own posts, unless you are a moderator.'],
                ['How do I change my avatar?', 'Go to your profile settings (click your username in the header) and navigate to the Personality tab.'],
                ['Why can\'t I post?', 'Either you are not logged in, the forum requires a minimum post count, or posting has been disabled by the administrator.'],
                ['How do I report a post?', 'Click the "Report" link below a post. Provide a reason and submit the report to the moderation team.'],
                ['What are sticky topics?', 'Sticky topics are pinned to the top of the forum list by moderators because they contain important information.'],
                ['What does "closed" mean?', 'A closed topic cannot receive new replies. Only moderators can close or reopen topics.'],
            ] as [$q, $a])
                <div>
                    <div class="font-semibold text-sm text-gray-800 mb-1">{{ $q }}</div>
                    <div class="text-sm text-gray-600 leading-relaxed">{{ $a }}</div>
                </div>
            @endforeach
        </div>
    </div>

</div>

@endsection
