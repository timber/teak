<?php

namespace Teak\Reflection;

/**
 * Class DocBlock
 */
class DocBlock
{
    /**
     * @var \phpDocumentor\Reflection\DocBlock
     */
    public $reflection;

    /**
     * Reflection constructor.
     *
     * @param \phpDocumentor\Reflection\DocBlock $reflection
     */
    public function __construct($reflection)
    {
        $this->reflection = $reflection;
    }

    public function hasTag($name)
    {
        return $this->reflection->hasTag($name);
    }

    /**
     * @param $name
     *
     * @return \phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     */
    public function getTag($name)
    {
        /**
         * @var array $tag
         */
        $tag = $this->reflection->getTagsByName($name);

        if (empty($tag)) {
            return null;
        }

        return $tag[0];
    }

    public function getDescription()
    {
        return $this->reflection->getDescription();
    }

    public function getSummary()
    {
        return $this->reflection->getSummary();
    }
}
