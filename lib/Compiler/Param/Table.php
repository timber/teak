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
            $description = $param->getDescription();

            // Detect params that are arrays
            if ('{' === substr($description, 0, 1)) {
                // Remove hash strings
                $description = trim($description, '{}');

                // Tell DocBlock to parse @type tags as @params. Also allows typed array elements.
                $description = preg_replace(
                    '/(^|\R)([ \t]*)@type([ \t]+)([^\s]+)([ \t]+)(?!\$)([A-Za-z_][A-Za-z0-9_]*)(?=[ \t]|$)/',
                    '$1$2@param$3$4$5\$$6',
                    $description
                );
                $description = str_replace('@type', '@param', $description);

                // Parse as DocBlock
                $docBlockFactory = \phpDocumentor\Reflection\DocBlockFactory::createInstance();

                // Use special format for parameter arrays.
                $paramArray = new ParamArray($docBlockFactory->create($description));
                $description = $paramArray->compile();
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
