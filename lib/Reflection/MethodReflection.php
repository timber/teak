<?php

namespace Teak\Reflection;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Class MethodReflection
 */
class MethodReflection extends Reflection
{
    /**
     * FunctionReflection constructor.
     *
     * @param Method|Function_ $reflection
     */
    public function __construct($reflection)
    {
        parent::__construct($reflection);
    }

    public function getReturnType()
    {
        if ($this->hasTag('return')) {
            /**
             * @var Return_ $return_tag
             */
            $return_tag = $this->getTag('return');

            return $return_tag->getType();
        }

        return null;
    }

    public function getReturnDescription()
    {
        if (!$this->hasTag('return')) {
            return null;
        }

        return trim($this->getTag('return')->getDescription());
    }

    /**
     * Build an array of arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        $args = [];
        $arguments = $this->reflection->getArguments();

        if (!empty($arguments)) {
            foreach ($arguments as $argument) {
                $definition = [
                    'name' => $argument->getName(),
                ];

                $type = $argument->getType();

                // If type is default mixed, then check if the parameter provides a better type.
                if ( $type instanceof Mixed_ ) {
                    foreach ($this->getParameters() as $parameter) {
                        if (
                            $parameter->getVariableName() === $argument->getName()
                            && $parameter->getType()
                        ) {
                            $type = $parameter->getType();
                        }
                    }
                }

                $definition['types'] = [ $type ];

                /**
                 * Get parameter default.
                 *
                 * A default can be an empty string, which is still a valid default. So we need to explicitly check if
                 * the default is null.
                 */
                if (null !== $argument->getDefault()) {
                    $definition['default'] = $argument->getDefault();
                }

                $args[] = $definition;
            }
        }

        return $args;
    }
}
