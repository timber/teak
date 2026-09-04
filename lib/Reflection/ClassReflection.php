<?php

namespace Teak\Reflection;

/**
 * Class ClassReflection
 */
class ClassReflection extends Reflection
{
    private array $parentMethods = [];
    private array $parentProperties = [];
    private array $traitMethods = [];
    private array $traitProperties = [];

    /**
     * ClassReflection constructor.
     *
     * @param \phpDocumentor\Reflection\Php\Class_|\phpDocumentor\Reflection\Php\Trait_ $reflection
     */
    public function __construct($reflection)
    {
        parent::__construct($reflection);
    }

    public function addParentInformation($parentClass)
    {
        $methods = array_map(function ($method) {
            return $method->getName();
        }, $this->parentMethods);
        $parent_methods = array_filter($parentClass->getMethods(), function ($parent_method) use ($methods) {
            return !in_array($parent_method->getName(), $methods);
        });

        $this->parentMethods = array_merge($this->parentMethods, $parent_methods);
        $this->parentProperties = $parentClass->getProperties();
    }

    public function addTraitInformation($trait)
    {
        $methods = array_map(function ($method) {
            return $method->getName();
        }, $this->traitMethods);
        $trait_methods = array_filter($trait->getMethods(), function ($trait_method) use ($methods) {
            return !in_array($trait_method->getName(), $methods);
        });

        $this->traitMethods = array_merge($this->traitMethods, $trait_methods);
        $this->traitProperties = array_merge($this->traitProperties, $trait->getProperties());
    }

    public function hasMethods()
    {
        if (method_exists($this, 'getMethods')) {
            return !empty($this->getMethods());
        }

        return false;
    }

    public function getMethods()
    {
        $methods = $this->reflection->getMethods();

        // Add trait methods first (so they can be overridden by class/parent methods)
        if (!empty($this->traitMethods)) {
            $method_names = array_map(function ($method) {
                return $method->getName();
            }, $methods);
            $trait_methods = array_filter($this->traitMethods, function ($trait_method) use ($method_names) {
                return !in_array($trait_method->getName(), $method_names);
            });

            $methods = array_merge($methods, $trait_methods);
        }

        if (method_exists($this->reflection, 'getParent') && $this->reflection->getParent() && !empty($this->parentMethods)) {
            // Filter out parent methods that are already defined in the child class.
            $method_names = array_map(function ($method) {
                return $method->getName();
            }, $methods);
            $parent_methods = array_filter($this->parentMethods, function ($parent_method) use ($method_names) {
                return !in_array($parent_method->getName(), $method_names);
            });

            $methods = array_merge($methods, $parent_methods);
        }

        $methods = array_filter($methods, function ($item) {
            return ! (new Reflection($item))->shouldIgnore();
        });

        // Sort methods by name.
        usort($methods, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        return $methods;
    }

    public function getProperties()
    {
        $properties = $this->reflection->getProperties();

        // Add trait properties
        if (!empty($this->traitProperties)) {
            $properties = array_merge($properties, $this->traitProperties);
        }

        if (method_exists($this->reflection, 'getParent') && $this->reflection->getParent() && !empty($this->parentProperties)) {
            $properties = array_merge($properties, $this->parentProperties);
        }

        $properties = array_filter($properties, function ($item) {
            return ! (new Reflection($item))->shouldIgnore();
        });

        // Sort properties by name.
        usort($properties, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        return $properties;
    }

    public function getParent()
    {
        if (method_exists($this->reflection, 'getParent')) {
            return $this->reflection->getParent();
        }
        return null;
    }

    public function getInterfaces()
    {
        if (method_exists($this->reflection, 'getInterfaces')) {
            return $this->reflection->getInterfaces();
        }
        return [];
    }

    public function getParentMethods(): array
    {
        return $this->parentMethods;
    }

    public function getParentProperties(): array
    {
        return $this->parentProperties;
    }

    public function getUsedTraits(): array
    {
        if (method_exists($this->reflection, 'getUsedTraits')) {
            return $this->reflection->getUsedTraits();
        }
        return [];
    }
}
