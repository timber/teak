<?php

namespace Teak\Compiler;

use Teak\Compiler\Param\Table as ParamTable;
use Teak\Compiler\Tag\Deprecated;
use Teak\Compiler\Tag\Description;
use Teak\Compiler\Tag\Example;
use Teak\Compiler\Tag\Link;
use Teak\Compiler\Tag\See;
use Teak\Compiler\Tag\Since;
use Teak\Compiler\Tag\Summary;

/**
 * Class Hook
 */
class Hook implements CompilerInterface
{
    /**
     * @var \phpDocumentor\Reflection\DocBlock
     */
    private $docBlock;

    /**
     * DocBlock constructor.
     *
     * @param \phpDocumentor\Reflection\DocBlock $docBlock
     */
    public function __construct($docBlock = null)
    {
        $this->docBlock = $docBlock;
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        // Summary
        $contents .= (new Summary($this->docBlock))->compile();

        // Deprecation notice
        $contents .= (new Deprecated($this->docBlock))->compile();

        // Description
        $contents .= (new Description($this->docBlock))->compile();

        // See tag
        $contents .= (new See($this->docBlock))->compile();

        // Link tag
        $contents .= (new Link($this->docBlock))->compile();

        // Since tag
        $contents .= (new Since($this->docBlock))->compile();

        // Parameter table
        if ($this->docBlock->hasTag('param')) {
            $contents .= (new ParamTable($this->docBlock->getTagsByName('param')))->compile();
        }

        // Code example
        $contents .= (new Example($this->docBlock))->compile();

        return $contents;
    }
}
