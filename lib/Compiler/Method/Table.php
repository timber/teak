<?php

namespace Teak\Compiler\Method;

use phpDocumentor\Reflection\Php\Function_;
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
     * @param \phpDocumentor\Reflection\Php\Method[]|Function_[] $methods
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

        $contents .= '| Name | Return Type | Summary/Returns |' . self::NEWLINE;
        $contents .= '| --- | --- | --- |' . self::NEWLINE;

        foreach ($this->methods as $method) {
            $contents .= $this->compileMethod($method);
        }

        return $contents . self::NEWLINE;
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

        $return = '| [' . $name . '](#' . $this->sanitizeAnchor($method->getName()) . ')' . ' | '
            . $this->sanitizeTypeList($method->getReturnType()) . ' | ';

        if (!empty($method->getSummary())) {
        	$return .= $this->sanitizeTextForTable($method->getSummary()) . '<br><br>';
        }

        if (!empty($method->getReturnDescription())) {
	        $return .= '*Returns:* '
	                   . $this->sanitizeTextForTable($method->getReturnDescription())
	                   . ' |';
        }


        return $return . self::NEWLINE;
    }
}
