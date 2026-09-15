<?php

declare(strict_types=1);

namespace Teak\Compiler\Tag;

use Teak\Compiler\CompilerInterface;
use Teak\Compiler\Param\ArrayDefinition;
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

        $arrayDefinition = new ArrayDefinition();
        $type = $arrayDefinition->normalizeArrayShapeType($this->method->getReturnType());
        $description = $this->method->getReturnDescription();
        $arrayShapeDescription = $arrayDefinition->compileArrayShape($this->method->getReturnType());

        if ('' !== $arrayShapeDescription) {
            $description = trim($description . self::PARAGRAPH . $arrayShapeDescription);
        }

        $contents .= '**Returns:** `' . $type . '` '
            . $description;

        return $contents . self::PARAGRAPH;
    }
}
