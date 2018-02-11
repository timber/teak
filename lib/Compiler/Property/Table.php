<?php

namespace Teak\Compiler\Property;

use Teak\Compiler\CompilerInterface;
use Teak\Compiler\SanitizeTrait;
use Teak\Reflection\PropertyReflection;

/**
 * Class Table
 */
class Table implements CompilerInterface
{
    use SanitizeTrait;

    /**
     * @var \Teak\Reflection\Reflection[]
     */
    private $properties;

    /**
     * Table constructor.
     *
     * @param \phpDocumentor\Reflection\Php\Property[] $params
     */
    public function __construct($params)
    {
        $this->properties = array_map(function ($param) {
            return new PropertyReflection($param);
        }, $params);
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        if (!is_array($this->properties) || empty($this->properties)) {
            return $contents;
        }

        $contents .= '| Name | Type | Description |' . self::NEWLINE;
        $contents .= '| --- | --- | --- |' . self::NEWLINE;

        foreach ($this->properties as $param) {
            $contents .= '| $' . $param->getName() . ' | `'
                . $this->escapePipe($param->getDocBlockType()) . '` | '
                . $this->escapePipe($param->getDescription()) . ' |' . self::NEWLINE;
        }

        $contents .= self::NEWLINE;

        return $contents;
    }
}
