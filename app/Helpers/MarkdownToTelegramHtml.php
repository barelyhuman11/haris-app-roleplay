<?php

namespace App\Helpers;

/**
 * Converts (common) Markdown into Telegram-supported HTML.
 * Supported tags: <b>, <i>, <s>, <u>, <code>, <pre>, <a>, <blockquote>
 */
class MarkdownToTelegramHtml
{
    public static function convert(string $markdown): string
    {
        $placeholders = [];
        $store = function (string $html) use (&$placeholders): string {
            $key = "\x00" . count($placeholders) . "\x00";
            $placeholders[$key] = $html;
            return $key;
        };

        $text = str_replace("\r\n", "\n", $markdown);

        // Fenced code blocks ```lang\n...\n```
        $text = preg_replace_callback('/```([\w+-]*)\n?(.*?)```/s', function ($m) use ($store) {
            $code = self::escape(rtrim($m[2], "\n"));
            $lang = $m[1] !== '' ? ' class="language-' . self::escape($m[1]) . '"' : '';
            return $store("<pre><code{$lang}>{$code}</code></pre>");
        }, $text);

        // Inline code `...`
        $text = preg_replace_callback('/`([^`\n]+)`/', fn ($m) => $store('<code>' . self::escape($m[1]) . '</code>'), $text);

        // Links [text](url)
        $text = preg_replace_callback('/\[([^\]]+)\]\(([^)\s]+)\)/', function ($m) use ($store) {
            return $store('<a href="' . self::escape($m[2]) . '">') . $m[1] . $store('</a>');
        }, $text);

        // Escape everything else
        $text = self::escape($text);

        // Headings -> bold
        $text = preg_replace('/^#{1,6}\s+(.+)$/m', '<b>$1</b>', $text);

        // Blockquotes (">" is escaped to "&gt;")
        $text = preg_replace_callback('/(?:^&gt; ?.*(?:\n|$))+/m', function ($m) {
            $inner = preg_replace('/^&gt; ?/m', '', rtrim($m[0], "\n"));
            return "<blockquote>{$inner}</blockquote>\n";
        }, $text);

        // Horizontal rules
        $text = preg_replace('/^\s*([-*_])(\s*\1){2,}\s*$/m', '———', $text);

        // List bullets
        $text = preg_replace('/^(\s*)[-*+]\s+/m', '$1• ', $text);

        // Bold, underline-bold, strikethrough, italic
        $text = preg_replace('/\*\*(.+?)\*\*/s', '<b>$1</b>', $text);
        $text = preg_replace('/__(.+?)__/s', '<b>$1</b>', $text);
        $text = preg_replace('/~~(.+?)~~/s', '<s>$1</s>', $text);
        $text = preg_replace('/(?<![\w*])\*(?!\s)(.+?)(?<!\s)\*(?![\w*])/s', '<i>$1</i>', $text);
        $text = preg_replace('/(?<![\w_])_(?!\s)(.+?)(?<!\s)_(?![\w_])/s', '<i>$1</i>', $text);

        // Restore placeholders
        return strtr($text, $placeholders);
    }

    private static function escape(string $text): string
    {
        return str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $text);
    }
}
