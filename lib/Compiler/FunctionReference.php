<?php

namespace Teak\Compiler;

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Php\Function_;
use Teak\Compiler\Tag\Summary;
use Teak\Reflection\MethodReflection;

class FunctionReference implements CompilerInterface
{
    protected $function;

    /**
     * FunctionReference constructor.
     *
     * @param Function_ $function
     */
    public function __construct($function)
    {
        $this->function = new MethodReflection($function);
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        $contents .= (new Summary($this->function->getDocBlock()))->compile();
        $contents .= (new Description($this->function->getDocBlock()))->compile();



        return $contents;
    }
}
