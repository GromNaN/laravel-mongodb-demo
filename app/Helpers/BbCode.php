<?php

namespace App\Helpers;

class BbCode
{
    /** Allowed URL schemes for [url] and [img] tags. */
    private const SAFE_URL_SCHEMES = ['http', 'https', 'ftp', 'ftps'];

    public static function parse(string $text): string
    {
        // Escape all HTML entities first to prevent XSS from raw input.
        $text = e($text);

        $patterns = [
            '/\[b\](.*?)\[\/b\]/si'          => '<strong>$1</strong>',
            '/\[i\](.*?)\[\/i\]/si'          => '<em>$1</em>',
            '/\[u\](.*?)\[\/u\]/si'          => '<span style="text-decoration:underline">$1</span>',
            '/\[s\](.*?)\[\/s\]/si'          => '<del>$1</del>',
            '/\[code\](.*?)\[\/code\]/si'    => '<pre class="bg-gray-100 p-2 rounded text-sm overflow-x-auto"><code>$1</code></pre>',
            '/\[size=(\d+)\](.*?)\[\/size\]/si' => '<span style="font-size:$1px">$2</span>',
            '/\[quote=(.*?)\](.*?)\[\/quote\]/si' => '<blockquote class="border-l-4 border-gray-300 pl-4 my-2 text-gray-600"><cite>$1 wrote:</cite><br>$2</blockquote>',
            '/\[quote\](.*?)\[\/quote\]/si'  => '<blockquote class="border-l-4 border-gray-300 pl-4 my-2 text-gray-600">$1</blockquote>',
            '/\[list\](.*?)\[\/list\]/si'    => '<ul class="list-disc list-inside">$1</ul>',
            '/\[list=1\](.*?)\[\/list\]/si'  => '<ol class="list-decimal list-inside">$1</ol>',
            '/\[\*\](.*?)(?=\[\*\]|\[\/list\])/si' => '<li>$1</li>',
        ];

        $text = preg_replace(array_keys($patterns), array_values($patterns), $text);

        // Handle [url], [img], and [color] with callbacks to sanitize values.
        $text = preg_replace_callback(
            '/\[url=(.*?)\](.*?)\[\/url\]/si',
            fn ($m) => self::safeLink($m[1], $m[2]),
            $text,
        );

        $text = preg_replace_callback(
            '/\[url\](.*?)\[\/url\]/si',
            fn ($m) => self::safeLink($m[1], $m[1]),
            $text,
        );

        $text = preg_replace_callback(
            '/\[img\](.*?)\[\/img\]/si',
            fn ($m) => self::safeImg($m[1]),
            $text,
        );

        $text = preg_replace_callback(
            '/\[color=([a-zA-Z0-9#]+)\](.*?)\[\/color\]/si',
            fn ($m) => '<span style="color:' . htmlspecialchars($m[1], ENT_QUOTES) . '">' . $m[2] . '</span>',
            $text,
        );

        return nl2br($text);
    }

    /** Alias kept for view compatibility. */
    public static function toHtml(string $text): string
    {
        return self::parse($text);
    }

    public static function strip(string $text): string
    {
        return preg_replace('/\[.*?\]/', '', $text);
    }

    private static function isSafeUrl(string $url): bool
    {
        $scheme = strtolower(parse_url(trim($url), PHP_URL_SCHEME) ?? '');

        return in_array($scheme, self::SAFE_URL_SCHEMES, strict: true);
    }

    private static function safeLink(string $href, string $label): string
    {
        if (! self::isSafeUrl($href)) {
            return e($label);
        }

        return '<a href="' . $href . '" class="text-blue-600 hover:underline" rel="nofollow noopener noreferrer">' . $label . '</a>';
    }

    private static function safeImg(string $src): string
    {
        if (! self::isSafeUrl($src)) {
            return '';
        }

        return '<img src="' . $src . '" alt="" class="max-w-full">';
    }
}
