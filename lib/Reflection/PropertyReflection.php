<?php

namespace Teak\Reflection;

use phpDocumentor\Reflection\Php\Property;

/**
 * Class PropertyReflection
 *
 * @package Teak\Reflection
 */
class PropertyReflection extends Reflection
{
    /**
     * PropertyReflection constructor.
     *
     * @param Property $reflection
     */
    public function __construct($reflection)
    {
        parent::__construct($reflection);
    }

    public function getDocBlockType()
    {
        if ($this->hasTag('var')) {
            return $this->getTag('var')->getType();
        }

        return null;
    }

    public function getDescription()
    {
        if ($this->hasTag('var')) {
            $description = $this->getTag('var')->getDescription();

            return $description;
        }

        return null;
    }
}
