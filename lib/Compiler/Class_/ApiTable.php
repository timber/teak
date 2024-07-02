<?php

namespace Teak\Compiler\Class_;

use Teak\Compiler\CompilerInterface;
use Teak\Compiler\Method\Table as MethodsTable;
use Teak\Compiler\Property\Table as PropertiesTable;
use Teak\Compiler\Heading;
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
     * ClassApiTableCompiler constructor.
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

        if ($this->class->hasTag('ignore')) {
            return $contents;
        }

        $properties = $this->class->getProperties();
        $methods = $this->class->getMethods();

        if (!empty($properties) || !empty($methods) || $this->class->getParent() || $this->class->getInterfaces()) {
            $contents .= (new Heading('Overview', 2))->compile();
        }

        if ($this->class->getParent() || $this->class->getInterfaces()) {
            $contents .= (new ParentList($this->class))->compile();
            $contents .= (new InterfaceList($this->class))->compile();
            $contents .= self::NEWLINE;
        }

        if (!empty($properties)) {
            $contents .= (new Heading('Properties', 3))->compile();

            $paramsTable = new PropertiesTable($properties);
            $contents .= $paramsTable->compile();
        }

        if (!empty($methods)) {
            $contents .= (new Heading('Methods', 3))->compile();

            $methodTable = new MethodsTable($methods);
            $contents .= $methodTable->compile();
        }

        $contents .= PHP_EOL;

        return $contents;
    }
}
