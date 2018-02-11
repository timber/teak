<?php

namespace Teak\Compiler\Method;

use Teak\Compiler\CompilerInterface;
use Teak\Compiler\SanitizeTrait;
use Teak\Reflection\MethodReflection;

/**
 * Class Table
 */
class Table implements CompilerInterface
{
    use SanitizeTrait;

    /**
     * @var MethodReflection[]
     */
    public $methods;

    /**
     * Table constructor.
     *
     * @param \Teak\Compiler\Method\Method[] $methods
     */
    public function __construct($methods)
    {
        $this->methods = array_map(function ($method) {
            return new MethodReflection($method);
        }, $methods);
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        /**
         * Sort functions by name
         */
        usort($this->methods, function (MethodReflection $a, MethodReflection $b) {
            if ($a->getName() === $b->getName()) {
                return 0;
            }

            return ($a->getName() < $b->getName()) ? -1 : 1;
        });

        $contents = '';

        $contents .= '| Name | Type | Returns/Description |' . PHP_EOL;
        $contents .= '| --- | --- | --- |' . PHP_EOL;

        foreach ($this->methods as $method) {
            $contents .= $this->compileMethod($method);
        }

        return $contents;
    }

    /**
     * @param MethodReflection $method
     *
     * @return string
     */
    public function compileMethod($method)
    {
        $name = $method->getName();

        if ($method->isDeprecated()) {
            $name = '<del>' . $name . '</del>';
        }

        return '| [' . $name . '](#' . $this->sanitizeAnchor($method->getName()) . ')' . ' | '
            . $this->sanitizeTypeList($method->getReturnType()) . ' | '
            . $this->escapePipe($method->getReturnDescription()) . ' |' . PHP_EOL;
    }
}
