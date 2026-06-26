<?php

namespace App\Helpers;

class BbCode
{
    public static function parse(string $text): string
    {
        $text = e($text);

        $patterns = [
            '/\[b\](.*?)\[\/b\]/si'          => '<strong>$1</strong>',
            '/\[i\](.*?)\[\/i\]/si'          => '<em>$1</em>',
            '/\[u\](.*?)\[\/u\]/si'          => '<span style="text-decoration:underline">$1</span>',
            '/\[s\](.*?)\[\/s\]/si'          => '<del>$1</del>',
            '/\[code\](.*?)\[\/code\]/si'    => '<pre class="bg-gray-100 p-2 rounded text-sm overflow-x-auto"><code>$1</code></pre>',
            '/\[url=(.*?)\](.*?)\[\/url\]/si' => '<a href="$1" class="text-blue-600 hover:underline" rel="nofollow">$2</a>',
            '/\[url\](.*?)\[\/url\]/si'      => '<a href="$1" class="text-blue-600 hover:underline" rel="nofollow">$1</a>',
            '/\[img\](.*?)\[\/img\]/si'      => '<img src="$1" alt="" class="max-w-full">',
            '/\[color=(.*?)\](.*?)\[\/color\]/si' => '<span style="color:$1">$2</span>',
            '/\[size=(\d+)\](.*?)\[\/size\]/si'   => '<span style="font-size:$1px">$2</span>',
            '/\[quote=(.*?)\](.*?)\[\/quote\]/si' => '<blockquote class="border-l-4 border-gray-300 pl-4 my-2 text-gray-600"><cite>$1 wrote:</cite><br>$2</blockquote>',
            '/\[quote\](.*?)\[\/quote\]/si'  => '<blockquote class="border-l-4 border-gray-300 pl-4 my-2 text-gray-600">$1</blockquote>',
            '/\[list\](.*?)\[\/list\]/si'    => '<ul class="list-disc list-inside">$1</ul>',
            '/\[list=1\](.*?)\[\/list\]/si'  => '<ol class="list-decimal list-inside">$1</ol>',
            '/\[\*\](.*?)(?=\[\*\]|\[\/list\])/si' => '<li>$1</li>',
        ];

        $text = preg_replace(array_keys($patterns), array_values($patterns), $text);
        $text = nl2br($text);

        return $text;
    }

    public static function strip(string $text): string
    {
        return preg_replace('/\[.*?\]/', '', $text);
    }
}
