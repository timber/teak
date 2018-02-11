<?php

namespace Teak\Reflection;

use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Property;

/**
 * Class Reflection
 */
class Reflection
{
    public $reflection;

    public $docBlock;

    /**
     * Reflection constructor.
     *
     * @param Class_|Method|Property $reflection
     */
    public function __construct($reflection)
    {
        $this->reflection = $reflection;

        if ($reflection->getDocBlock()) {
            $this->docBlock = $reflection->getDocblock();
        }
    }

    public function getName() {
        return $this->reflection->getName();
    }

    public function hasDocBlock() {
        return !empty($this->getDocBlock());
    }

    public function getDocBlock() {
        return $this->docBlock;
    }

    public function hasSummary() {
        return !empty($this->getSummary());
    }

    public function getSummary() {
        if (!$this->docBlock) {
            return '';
        }

        return $this->docBlock->getSummary();
    }

    public function hasDescription() {
        return !empty($this->getDescription());
    }

    public function getDescription() {
        if (!$this->docBlock) {
            return '';
        }

        return $this->docBlock->getDescription()->render();
    }

    public function hasTag($name)
    {
        if (!$this->docBlock) {
            return false;
        }

        return $this->docBlock->hasTag($name);
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
        $tag = $this->reflection->getDocblock()->getTagsByName($name);

        if (empty($tag)) {
            return null;
        }

        return $tag[0];
    }

    public function hasParameters()
    {
        return !empty($this->getParameters());
    }

    public function getParameters()
    {
        /**
         * @var \phpDocumentor\Reflection\DocBlock\Tags\Param[] $params
         */
        $params = $this->reflection->getDocblock()->getTagsByName('param');

        return $params;
    }

    public function isDeprecated()
    {
        return $this->hasTag('deprecated');
    }

    public function getDeprecationMessage() {
        if ($this->hasTag('deprecated')) {
            return $this->getTag('deprecated')->getDescription();
        }

        return null;
    }

    public function getTitle() {
        $title = $this->reflection->getFqsen();
        $title = ltrim('\\', $title);

        return $title;
    }

    /**
     * Check whether reflection should be ignored.
     *
     * @return bool
     */
    public function shouldIgnore() {
        return !$this->docBlock
               || !$this->hasTag('api')
               || $this->hasTag('ignore')
               || $this->hasTag('internal')
               || (method_exists($this->reflection, 'getVisibility')
                   && $this->reflection->getVisibility() === 'private'
               );
    }
}
