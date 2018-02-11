<?php

namespace Teak\Compiler\Tag;

use Teak\Compiler\CompilerInterface;
use Teak\Reflection\MethodReflection;

/**
 * Class Return_
 */
class Return_ implements CompilerInterface
{
    /**
     * @var MethodReflection
     */
    protected $method;

    /**
     * Return_ constructor.
     *
     * @param MethodReflection $method
     */
    public function __construct($method)
    {
        $this->method = $method;
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '';

        if (!$this->method->hasTag('return')) {
            return $contents;
        }

        $contents .= '**Returns:** `' . $this->method->getReturnType() . '` '
                . $this->method->getReturnDescription();

        return $contents . self::PARAGRAPH;
    }
}
