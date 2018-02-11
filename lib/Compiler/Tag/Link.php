<?php

namespace Teak\Compiler\Tag;

use Teak\Compiler\CompilerInterface;
use Teak\Reflection\DocBlock;

/**
 * Class Link
 */
class Link implements CompilerInterface
{
    /**
     * @var \phpDocumentor\Reflection\DocBlock\Tags\Link
     */
    protected $tag;

    /**
     * Link constructor.
     *
     * @param \phpDocumentor\Reflection\DocBlock $docBlock
     */
    public function __construct($docBlock)
    {
         $docBlock = new DocBlock($docBlock);
        $this->tag = $docBlock->getTag('link');
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

        $contents .= '**link** ';
        $contents .= "<{$this->tag}>";

        return $contents . self::PARAGRAPH;
    }
}
