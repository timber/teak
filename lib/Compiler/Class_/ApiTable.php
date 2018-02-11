<?php

namespace Teak\Compiler\Class_;

use Teak\Compiler\CompilerInterface;
use Teak\Compiler\Method\Table as MethodsTable;
use Teak\Compiler\Property\Table as PropertiesTable;
use Teak\Reflection\ClassReflection;
use Teak\Reflection\Reflection;

/**
 * Class ApiTable
 */
class ApiTable extends Reflection implements CompilerInterface
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
     * ClassApiTableCompiler constructor.
     *
     * @param \phpDocumentor\Reflection\Php\Class_ $class
     */
    public function __construct($class)
    {
        $this->class = new ClassReflection($class);

        $this->reflection = $class;
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        if ($this->class->hasTag('ignore')) {
            return $contents;
        }

        $properties = $this->class->getProperties();
        $methods = $this->class->getMethods();

        if (!empty($properties) || !empty($methods)) {
            $contents .= '## Overview' . self::PARAGRAPH;
        }

        if (!empty($properties)) {
            $contents .= '### Properties' . self::PARAGRAPH;

            $paramsTable = new PropertiesTable($properties);
            $contents .= $paramsTable->compile();
        }

        if (!empty($methods)) {
            $contents .= '### Methods' . self::PARAGRAPH;

            $methodTable = new MethodsTable($methods);
            $contents .= $methodTable->compile();
        }

        $contents .= PHP_EOL;

        return $contents;
    }
}
