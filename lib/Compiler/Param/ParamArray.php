<?php

namespace Teak\Compiler\Param;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Teak\Compiler\CompilerInterface;
use Teak\Compiler\SanitizeTrait;

/**
 * Class ParamArray
 *
 * Used for parameters that describe arrays.
 *
 * @link https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/php/#1-1-parameters-that-are-arrays
 */
class ParamArray implements CompilerInterface
{
    use SanitizeTrait;

    /**
     * @var
     */
    protected $docBlock;

    /**
     * ParamArray constructor.
     *
     * @param \phpDocumentor\Reflection\DocBlock $docBlock
     */
    public function __construct($docBlock)
    {
        $this->docBlock = $docBlock;
    }

    /**
     * Compile a list of params.
     *
     * We can’t use Markdown format here, because when it’s used inside a table, all the line breaks would also break
     * the table.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        if (!empty($this->docBlock->getSummary())) {
            $contents .= $this->docBlock->getSummary();
            $contents .= '<br><br>';
        }

        $params = $this->docBlock->getTagsByName('param');

        if (empty($params)) {
            return $contents;
        }

        $contents .= '<ul>';

        /**
         * @var Param $param
         */
        foreach ($params as $param) {
            $contents .= '<li>**$' . $param->getVariableName() . '**<br>';
            $contents .= $this->sanitizeTypeList($param->getType());

            $description = $param->getDescription();
            $description = trim($description);
            $description = ltrim($description, '=>');
            $description = trim($description);

            if (!empty($description)) {
                $contents .= ' ' . $description;
            }

            $contents .= '</li>';
        }

        $contents .= '</ul>';

        return $contents;
    }
}
