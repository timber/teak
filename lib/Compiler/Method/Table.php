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

        $contents = '<div class="table-methods table-responsive">';
        $contents .= self::NEWLINE;
        $contents .= '| Name | Return Type | Summary/Returns |' . self::NEWLINE;
        $contents .= '| --- | --- | --- |' . self::NEWLINE;

        foreach ($this->methods as $method) {
            $contents .= $this->compileMethod($method);
        }

        $contents .= '</div>';
        $contents .= self::PARAGRAPH;

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
            $name = '~~' . $name . '~~';
        }

        // Add parenthesis to mark it as a function
        $name .= '()';

        $return = sprintf(
            '| <span class="method-name">[%1$s](#%2$s)</span> | '
                . '<span class="method-type">%3$s</span> | ',
            $name,
            $this->sanitizeAnchor($method->getName()),
            $this->sanitizeTypeList($method->getReturnType())
        );

        $return .= '<span class="method-description">';

        if (!empty($method->getSummary())) {
            $return .= $this->sanitizeTextForTable($method->getSummary());
        }

        if (!empty($method->getReturnDescription())) {
            $return .= '<br><br><span class="method-return"><span class="method-return-label">Returns:</span> '
                . $this->sanitizeTextForTable($method->getReturnDescription())
                . '</span>';
        }

        $return .= '</span> |';

        return $return . self::NEWLINE;
    }
}
