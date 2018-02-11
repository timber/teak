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

        // Escape '\'
        $title = str_replace('\\', '\\\\', $title);

        return $title;
    }

    /**
     * Replace pipe character with HTML character.
     *
     * @param string $text
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
}
