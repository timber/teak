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
    protected $parent;

    public function __construct($title, $parent = '')
    {
        $this->title = $this->sanitizeTitle($title);
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

        if (!empty($this->parent)) {
            $contents .= 'menu:' . self::NEWLINE
                . '  main:' . self::NEWLINE
                . '    parent: "' . $this->parent . '"'
                . self::NEWLINE;
        }

        $contents .= '---';
        $contents .= self::PARAGRAPH;

        return $contents;
    }
}
