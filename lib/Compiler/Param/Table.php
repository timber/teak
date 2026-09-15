<?php

namespace Teak\Compiler\Param;

use Teak\Compiler\CompilerInterface;
use Teak\Compiler\SanitizeTrait;

/**
 * Class Table
 *
 * @package Teak\Markdown
 */
class Table implements CompilerInterface
{
    use SanitizeTrait;

    /**
     * @var \phpDocumentor\Reflection\DocBlock\Tag[]
     */
    private $params;

    /**
     * Table constructor.
     *
     * @param \phpDocumentor\Reflection\DocBlock\Tag[] $params
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        if (!is_array($this->params) || empty($this->params)) {
            return $contents;
        }

        $contents = '<div class="table-responsive">';
        $contents .= self::PARAGRAPH;
        $contents .= '| Name | Type | Description |' . self::NEWLINE;
        $contents .= '| --- | --- | --- |' . self::NEWLINE;

        foreach ($this->params as $param) {
            if ($param instanceof \phpDocumentor\Reflection\DocBlock\Tags\InvalidTag) {
                $contents .= (new InvalidTag($param))->compile();
                continue;
            }

            $description = $param->getDescription();

            // Detect params that are arrays
            if ('{' === substr($description, 0, 1)) {
                $description = (new ArrayDefinition())->compileDescription($description);
            }

            $contents .= '| $' . $param->getVariableName() . ' | '
                . $this->sanitizeTypeList($param->getType()) . ' | '
                . $this->sanitizeTextForTable($description) . ' |' . self::NEWLINE;
        }

        $contents .= self::NEWLINE;
        $contents .= '</div>';

        return $contents . self::PARAGRAPH;
    }
}
