<?php

namespace App\Helpers;

/**
 * Splits Markdown into chunks that each fit within Telegram's message limit.
 *
 * Length is measured on the raw Markdown in UTF-16 code units (the unit Telegram
 * counts in). Raw Markdown is always >= the visible text after entity parsing,
 * so every chunk is safe whether it's sent as HTML or as plain text.
 * Fenced code blocks that span chunks are closed and reopened.
 */
class MarkdownSplitter
{
    /**
     * @return string[]
     */
    public static function split(string $markdown, int $limit = 4000): array
    {
        $lines = explode("\n", str_replace("\r\n", "\n", trim($markdown)));

        $chunks = [];
        $current = '';
        $fence = null; // opening fence line (e.g. "```php") when inside a code block

        foreach ($lines as $line) {
            // Hard-split lines that alone exceed the limit
            foreach (self::splitLongLine($line, $limit - 20) as $piece) {
                $candidate = $current === '' ? $piece : $current . "\n" . $piece;
                $closing = $fence !== null ? "\n```" : '';

                if ($current !== '' && self::length($candidate . $closing) > $limit) {
                    // Close current chunk (and code block if open), start a new one
                    $chunks[] = $current . ($fence !== null ? "\n```" : '');
                    $current = ($fence !== null ? $fence . "\n" : '') . $piece;
                } else {
                    $current = $candidate;
                }
            }

            if (preg_match('/^\s*```/', $line)) {
                $fence = $fence === null ? trim($line) : null;
            }
        }

        if (trim($current) !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }

    /**
     * @return string[]
     */
    private static function splitLongLine(string $line, int $limit): array
    {
        if (self::length($line) <= $limit) {
            return [$line];
        }

        $pieces = [];
        $current = '';

        foreach (preg_split('/(?<=\s)/u', $line) as $word) {
            // A single word longer than the limit: cut it by characters
            while (self::length($word) > $limit) {
                if ($current !== '') {
                    $pieces[] = $current;
                    $current = '';
                }
                $cut = self::cut($word, $limit);
                $pieces[] = $cut;
                $word = mb_substr($word, mb_strlen($cut));
            }

            if (self::length($current . $word) > $limit) {
                $pieces[] = rtrim($current);
                $current = $word;
            } else {
                $current .= $word;
            }
        }

        if ($current !== '') {
            $pieces[] = $current;
        }

        return $pieces;
    }

    /** Longest prefix of $text within $limit UTF-16 units. */
    private static function cut(string $text, int $limit): string
    {
        $out = '';
        foreach (mb_str_split($text) as $char) {
            if (self::length($out . $char) > $limit) {
                break;
            }
            $out .= $char;
        }
        return $out;
    }

    /** Length in UTF-16 code units, as counted by Telegram. */
    public static function length(string $text): int
    {
        return intdiv(strlen(mb_convert_encoding($text, 'UTF-16LE', 'UTF-8')), 2);
    }
}
