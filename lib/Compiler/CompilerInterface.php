<?php

namespace Teak\Compiler;

/**
 * Interface CompilerInterface
 *
 * @package Teak\Markdown
 */
interface CompilerInterface
{
    const NEWLINE = PHP_EOL;
    const PARAGRAPH = PHP_EOL . PHP_EOL;
    const BREAK = '  ' . PHP_EOL;
    const DIVIDER = '---' . PHP_EOL . PHP_EOL;

    /**
     * @return string
     */
    public function compile();
}
