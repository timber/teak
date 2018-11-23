<?php

namespace Teak\Compiler\Class_;

use Teak\Compiler\CompilerInterface;
use Teak\Reflection\ClassReflection;

/**
 * Class ParentList
 */
class ParentList implements CompilerInterface
{
    /**
     * @var ClassReflection
     */
    public $class;

    /**
     * ClassExtendListCompiler constructor.
     *
     * @param ClassReflection $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        if (!$this->class->getParent()) {
            return $contents;
        }

        $contents .= '*This class extends `' . ltrim($this->class->getParent(), '\\') . '`*';

        return $contents . self::BREAK;
    }
}
