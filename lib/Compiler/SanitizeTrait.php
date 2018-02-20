<?php

namespace Teak\Compiler;

/**
 * Trait SanitizeTrait
 *
 * Helper class to sanitize strings for Markdown.
 */
trait SanitizeTrait
{
    /**
     * Sanitize a title for the frontend.
     *
     * @param string $title
     *
     * @return mixed|string
     */
    public function sanitizeTitle($title)
    {
        // Remove leading backslash
        $title = ltrim($title, '\\');

        // Escape '\', and add a zero-width-space (non-visible here in code)
        $title = str_replace('\\', '\\\\​', $title);

        return $title;
    }

    /**
     * Replace pipe character with HTML character.
     *
     * @param string $text
     *
     * @return mixed
     */
    public function escapePipe($text)
    {
        $text = str_replace('|', '&#124;', $text);

        return $text;
    }

    public function sanitizeTypeList($list)
    {
        if (empty($list)) {
            return '';
        }

        $list = explode('|', $list);
        $list = implode('` or `', $list);

        return '`' . $list . '`';
    }

    public function singleQuotes($text)
    {
        return str_replace('"', '\'', $text);
    }

    public function wrapWithCodeTicks($text)
    {
        return '`' . $text . '`';
    }

    public function sanitizeAnchor($link)
    {
        $link = str_replace('__', '', $link);

        return $link;
    }

    public function removeLineBreaks($text)
    {
        return str_replace(PHP_EOL, ' ', $text);
    }

    public function escapeMarkdownChars($text)
    {
        return str_replace(
            ['_', '*'],
            ["\_", "\*"],
            $text
        );
    }

    public function sanitizeTextForTable($text)
    {
        $text = $this->removeLineBreaks($text);
        $text = $this->escapePipe($text);

        return $text;
    }

    /**
     * Slugifies text.
     *
     * @link https://stackoverflow.com/a/2955878/1059980
     *
     * @param string $text Text to slugify
     *
     * @return string
     */
    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
