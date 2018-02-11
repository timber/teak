<?php

namespace Teak\Compiler\Tag;

use Teak\Compiler\CompilerInterface;
use Teak\Reflection\DocBlock;

/**
 * Class See
 */
class See implements CompilerInterface
{
    /**
     * @var \phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     */
    protected $tag;

    /**
     * See constructor.
     *
     * @param \phpDocumentor\Reflection\DocBlock $docBlock
     */
    public function __construct($docBlock)
    {
        $docBlock = new DocBlock($docBlock);
        $this->tag = $docBlock->getTag('see');
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile() {
        $contents = '';

        if (empty($this->tag)) {
            return $contents;
        }

        $contents = '**see** ';

        $title = ltrim($this->tag, "\\");
        $link  = $title;

        $link = str_replace(
            array("\\", '()', '_', '::'),
            array('-', '', '-', '.md#'),
            mb_strtolower($link)
        );

        // Special case for timber.md
        // TODO: Move this out of Compiler somehow
        $link = str_replace(
            ['timber-timber'],
            ['timber'],
            $link
        );

        /**
         * Use a relative reference for Hugo
         *
         * @todo Move this out of Compiler somehow
         * @link https://gohugo.io/content-management/cross-references/
         */
        $contents .= '[' . $title . '](' . $link . ')';

        return $contents . self::PARAGRAPH;
    }
}
