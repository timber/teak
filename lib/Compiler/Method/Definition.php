<?php

namespace Teak\Compiler\Method;

use Teak\Compiler\CompilerInterface;
use Teak\Compiler\SanitizeTrait;
use Teak\Reflection\MethodReflection;

/**
 * Class Definition
 */
class Definition implements CompilerInterface
{
    use SanitizeTrait;

    /**
     * @var MethodReflection
     */
    protected $method;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * Definition constructor.
     *
     * @param MethodReflection $method
     */
    public function __construct($method)
    {
        $this->method = $method;
        $this->arguments = $method->getArguments();
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile()
    {
        $contents = '`';

        if (empty($this->arguments)) {
            return $contents;
        }

        $contents .= $this->method->getName() . '(';

        foreach ($this->arguments as $key => $argument) {
            if ($key > 0) {
                $contents .= ',';
            }

            if (isset($argument['types'])) {
                $contents .= ' ' . implode('|', $argument['types']);
            }

            $contents .= ' $' . $argument['name'];

            if (isset($argument['default'])) {
                $default = $argument['default'];

                if (empty($default)) {
                    $default = '\'\'';
                }

                $contents .= ' = ' . $this->singleQuotes($default);
            }
        }

        $contents .= ' )`';

        return $contents . self::PARAGRAPH;
    }
}
