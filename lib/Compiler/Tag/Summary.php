<?php

namespace Teak\Compiler\Tag;

use Teak\Compiler\CompilerInterface;
use Teak\Reflection\DocBlock;

/**
 * Class Summary
 */
class Summary implements CompilerInterface
{
    /**
     * @var string
     */
    protected $summary;

    public function __construct($docBlock)
    {
        $docBlock = new DocBlock($docBlock);
        $this->summary = trim($docBlock->getSummary());
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        if (empty($this->summary)) {
            return $contents;
        }

        $contents .= '<p class="summary">' . $this->summary . '</p>' . self::PARAGRAPH;

        return $contents;
    }
}
