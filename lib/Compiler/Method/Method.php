<?php

namespace Teak\Compiler\Method;

use phpDocumentor\Reflection\Php\Function_;
use Teak\Compiler\ClassList;
use Teak\Compiler\CompilerInterface;
use Teak\Compiler\Tag\Deprecated;
use Teak\Compiler\Tag\Description;
use Teak\Compiler\Tag\Link;
use Teak\Compiler\Tag\Return_;
use Teak\Compiler\Param\Table;
use Teak\Compiler\Tag\Example;
use Teak\Compiler\Tag\See;
use Teak\Compiler\Tag\Since;
use Teak\Compiler\Tag\Summary;
use Teak\Compiler\Heading;
use Teak\Reflection\MethodReflection;

/**
 * Class Method
 */
class Method implements CompilerInterface
{
    /**
     * @var MethodReflection
     */
    public $method;

    /**
     * ClassMethodListCompiler constructor.
     *
     * @param \phpDocumentor\Reflection\Php\Method|Function_ $method
     */
    public function __construct($method)
    {
        $this->method = new MethodReflection($method);
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        $name = $this->method->getName();

        if ($this->method->isDeprecated()) {
            $name = '<del>' . $this->method->getName() . '</del>';
        }

        $contents .= (new Heading($name, 3))->compile();

        // Summary
        $contents .= (new Summary($this->method->getDocBlock()))->compile();

        // Deprecated tag
        $contents .= (new Deprecated($this->method->getDocBlock()))->compile();

        // Description
        $contents .= (new Description($this->method->getDocBlock()))->compile();

        // See tag
        $contents .= (new See($this->method->getDocBlock()))->compile();

        // Link tag
        $contents .= (new Link($this->method->getDocBlock()))->compile();

        // Since tag
        $contents .= (new Since($this->method->getDocBlock()))->compile();

        // Function definition
        $contents .= (new Definition($this->method))->compile();

        // Return Tag
        $contents .= (new Return_($this->method))->compile();

        if ($this->method->hasParameters()) {
            $paramsTable = new Table($this->method->getParameters());
            $contents    .= $paramsTable->compile();
        }

        // Code Example
        $contents .= (new Example($this->method->getDocBlock()))->compile();

        $contents .= self::DIVIDER;

        return $contents;
    }
}
