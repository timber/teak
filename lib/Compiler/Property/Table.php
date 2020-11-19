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

        $contents .= '<div class="table-properties">';
        $contents .= self::PARAGRAPH;

        $contents .= '| Name | Type | Description |' . self::NEWLINE;
        $contents .= '| --- | --- | --- |' . self::NEWLINE;

        foreach ($this->properties as $param) {
            $contents .= sprintf(
                '| <span class="property-name">$%1$s</span> | '
                 . '<span class="property-type">%2$s</span> | '
                 . '<span class="property-description">%3$s</span> |' . self::NEWLINE,
                $param->getName(),
                $this->sanitizeTypeList($param->getDocBlockType()),
                $this->sanitizeTextForTable($param->getDescription())
            );
        }

        $contents .= self::NEWLINE;
        $contents .= '</div>';
        $contents .= self::PARAGRAPH;

        return $contents;
    }
}
