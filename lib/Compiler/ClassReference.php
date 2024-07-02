<?php

namespace Teak\Compiler;

use Teak\Compiler\Class_\ApiTable;
use Teak\Compiler\Method\Method;
use Teak\Compiler\Tag\Description;
use Teak\Compiler\Tag\Example;
use Teak\Reflection\ClassReflection;

/**
 * Class ClassReference
 */
class ClassReference implements CompilerInterface
{
    /**
     * @var ClassReflection
     */
    public $class;

    /**
     * @var \phpDocumentor\Reflection\Php\Class_
     */
    public $reflection;

    /**
     * ClassReferenceCompiler constructor.
     *
     * @param \phpDocumentor\Reflection\Php\Class_ $class
     */
    public function __construct(ClassReflection $classReflection)
    {
        $this->reflection = $classReflection->reflection;
        $this->class = $classReflection;
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        $description = (new Description($this->class->getDocBlock()))->compile();

        $contents .= $description;

        if (!empty($description)) {
            /**
             * Mark text content until here as the description of the page.
             *
             * @link https://gohugo.io/content/summaries#user-defined-manual-summary-split
             */
            $contents .= '<!--more-->' . self::PARAGRAPH;
        }

        /**
         * TODO: test this out
         */
        if ($this->class->isDeprecated()) {
            $contents .= '### ~~' . $this->class->getTitle() . '~~' . self::PARAGRAPH .
                         '> **DEPRECATED** ' . $this->class->getTag('deprecated') . self::PARAGRAPH;
        }

        // Code example
        $contents .= (new Example($this->class->getDocBlock()))->compile();

        // Class properties and methods table
        $contents .= (new ApiTable($this->class))->compile();

        $contents .= (new Heading('Class Methods', 2))->compile();

        foreach ($this->class->getMethods() as $method) {
            $method = new Method($method);
            $method->setCurrentClass($this->class);
            $contents .= $method->compile();
        }

        return $contents;
    }
}
