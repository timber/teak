<?php

namespace Teak\Compiler\Tag;

use Teak\Compiler\CompilerInterface;
use Teak\Reflection\DocBlock;

/**
 * Class Description
 */
class Description implements CompilerInterface
{
    /**
     * @var string
     */
    protected $description;

    public function __construct($docBlock)
    {
        $docBlock          = new DocBlock($docBlock);
        $this->description = trim($docBlock->getDescription());
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        if (empty($this->description)) {
            return $contents;
        }

        $contents .= trim($this->description);

        return $contents . self::PARAGRAPH;
    }
}
