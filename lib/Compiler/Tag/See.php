<?php

namespace Teak\Compiler\Tag;

use Teak\Compiler\ClassLinkList;
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

        $linkList      = ClassLinkList::getInstance()->get();
        $referencePath = ClassLinkList::getInstance()->getReferencePath();
        $title         = ltrim($this->tag, "\\");
        $method        = false;
        $link          = false;

        if (false !== strpos($title, '::')) {
            list($class, $method) = explode('::', $title);
        } else {
            $class = $title;
        }

        if ($linkList && in_array($class, array_keys($linkList), true)) {
            if ($referencePath) {
                $link = rtrim($referencePath, '/') . '/' . $linkList[$class]['filename'] . '/';
            } else {
                $link = '../' . $linkList[$class]['filename'] . '/';
            }

            if ($method) {
                $link .= '#' . rtrim($method, '()');
            }
        }

        $contents = '**see** ';

        if ($link) {
            $contents .= '[' . $title . '](' . $link . ')';
        } else {
            $contents .= $title;
        }

        return $contents . self::PARAGRAPH;
    }
}
