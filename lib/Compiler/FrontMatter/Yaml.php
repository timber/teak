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
    protected $parent;

    public function __construct($title, $linkTitle = false, $parent = '')
    {
        $this->title = $this->sanitizeTitle($title);
        $this->linkTitle = $this->sanitizeTitle($linkTitle);
        $this->parent = $parent;
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

        if ($this->linkTitle) {
            $contents .= 'linktitle: "' . $this->linkTitle . '"' . self::NEWLINE;
        }

        $contents .= 'is_reference: true' . self::NEWLINE;

        if (!empty($this->parent)) {
            $identifier = $this->slugify($this->parent) . '-' . $this->slugify($this->title);

            $contents .= 'menu:' . self::NEWLINE
                . '  main:' . self::NEWLINE
                . '    parent: "' . $this->parent . '"' . self::NEWLINE
                . '    identifier: "' . $identifier . '"' . self::NEWLINE;
        }

        $contents .= '---';
        $contents .= self::PARAGRAPH;

        return $contents;
    }
}
