<?php

namespace Teak\Compiler\Tag;

use Teak\Compiler\CompilerInterface;
use Teak\Reflection\DocBlock;

/**
 * Class Deprecated
 */
class Deprecated implements CompilerInterface
{
    /**
     * @var \phpDocumentor\Reflection\DocBlock\Tags\Deprecated
     */
    protected $tag;

    /**
     * Deprecated constructor.
     *
     * @param \phpDocumentor\Reflection\DocBlock $docBlock
     */
    public function __construct($docBlock)
    {
        $docBlock = new DocBlock($docBlock);
        $this->tag = $docBlock->getTag('deprecated');
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        if (empty($this->tag)) {
            return $contents;
        }

        $contents .= '**DEPRECATED** since ' . $this->tag . self::PARAGRAPH;

        return $contents;
    }
}
