<?php

namespace Teak\Compiler\FrontMatter;

use Teak\Compiler\CompilerInterface;
use Teak\Compiler\SanitizeTrait;

/**
 * Class Yaml
 *
 * @link https://gohugo.io/content-management/front-matter/
 */
class Yaml implements CompilerInterface
{
    use SanitizeTrait;

    protected $title;
    protected $linkTitle;

    public function __construct($title, $linkTitle = null)
    {
        $this->title = $this->sanitizeTitle($title);
        $this->linkTitle = !empty($linkTitle) ? $this->sanitizeTitle($linkTitle) : null;
    }

    /**
     * Generate YAML front matter block
     *
     * Example:
     * ---
     * title: "Timber\Timber"
     * menu:
     *   main:
     *     parent: "reference"
     * ---
     *
     * @return string
     */
    public function compile()
    {
        $contents = '---' . PHP_EOL;

        $contents .= 'title: "' . $this->title . '"' . self::NEWLINE;

        if (!empty($this->linkTitle)) {
            $contents .= 'linktitle: "' . $this->linkTitle . '"' . self::NEWLINE;
        }

        $contents .= 'is_reference: true' . self::NEWLINE;

        $contents .= '---';
        $contents .= self::PARAGRAPH;

        return $contents;
    }
}
