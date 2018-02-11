<?php

namespace Teak\Compiler\Class_;

use Teak\Compiler\CompilerInterface;
use Teak\Reflection\ClassReflection;

/**
 * Class ImplementsList
 */
class InterfaceList implements CompilerInterface
{
    /**
     * @var ClassReflection
     */
    public $class;

    /**
     * ClassImplementsListCompiler constructor.
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

        $interfaces = $this->class->getInterfaces();

        /** Todo: fix this */
        if ($interfaces) {
            $interfaces = array_map(function ($interface) {
                return ltrim($interface, '\\');
            }, $interfaces);

            $contents .= PHP_EOL . '*This class implements ' . implode(', ', $interfaces) . '*' . PHP_EOL;
        }

        return $contents;
    }
}
