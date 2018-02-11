<?php

namespace Teak\Compiler\Tag;

use Teak\Compiler\CompilerInterface;
use Teak\Reflection\DocBlock;

/**
 * Class Since
 */
class Since implements CompilerInterface
{
    /**
     * @var \phpDocumentor\Reflection\DocBlock\Tags\Since
     */
    protected $tag;

    /**
     * See constructor.
     *
     * @param \phpDocumentor\Reflection\DocBlock $docBlock
     */
    public function __construct($docBlock)
    {
        $docBlock = new DocBlock($docBlock);
        $this->tag = $docBlock->getTag('since');
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile() {
        $contents = '';

        if (empty($this->tag)) {
            return $contents;
        }

        $contents .= '**since** ' . $this->tag;

        return $contents . self::PARAGRAPH;
    }
}
