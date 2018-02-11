<?php

namespace Teak\Compiler;

/**
 * Class Heading
 */
class Heading implements CompilerInterface
{
    use SanitizeTrait;

    protected $title;
    protected $level;

    /**
     * Heading constructor.
     *
     * @param string $title
     * @param int    $level
     */
    public function __construct($title, $level)
    {
        $this->title = $title;
        $this->level = $level;
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        if (empty($this->title)) {
            return $contents;
        }

        $contents .= str_repeat('#', $this->level);
        $contents .= ' ' . $this->escapeMarkdownChars($this->title);

        return $contents . self::PARAGRAPH;
    }
}
