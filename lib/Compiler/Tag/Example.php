<?php

namespace Teak\Compiler\Tag;

use Teak\Compiler\CompilerInterface;
use Teak\Reflection\DocBlock;

/**
 * Class Example
 */
class Example implements CompilerInterface
{
    /**
     * @var \phpDocumentor\Reflection\DocBlock\Tags\Example
     */
    protected $tag;

    /**
     * Example constructor.
     *
     * @param \phpDocumentor\Reflection\DocBlock $docBlock
     */
    public function __construct($docBlock)
    {
        $docBlock = new DocBlock($docBlock);

        $this->tag = trim($docBlock->getTag('example') ?? '');
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        if (empty($this->tag)) {
            return $contents;
        }

        $contents = trim($this->tag);

        // Remove possible code tag
        if (strpos($contents, '<code>') !== false) {
            $contents = current(array_slice(explode('</code>', $contents), -2));
            $contents = current(array_slice(explode('<code>', $contents), 1));
        }

        $contents = str_replace('```php', '**PHP**' . self::PARAGRAPH . '```php', $contents);
        $contents = str_replace('```twig', '**Twig**' . self::PARAGRAPH . '```twig', $contents);
        $contents = str_replace('```html', '**HTML**' . self::PARAGRAPH . '```html', $contents);

        $contents = str_replace('\/', '/', $contents);

        return $contents . self::PARAGRAPH;
    }
}
