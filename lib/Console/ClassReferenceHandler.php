<?php

namespace Teak\Console;

use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Trait_;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Project;
use Teak\Compiler\ClassLinkList;
use Teak\Compiler\ClassReference;
use Teak\Compiler\FrontMatter\Yaml;
use Teak\Compiler\Heading;
use Teak\Reflection\ClassReflection;

class ClassReferenceHandler
{
    protected $options;
    protected Project $project;

    public function __construct($files, $options = [])
    {
        $this->options = array_merge([
            'filePrefix'       => '',
            'frontMatterStyle' => 'YAML',
        ], $options);

        $this->project = ProjectFactory::createInstance()->create('Teak', $files);

        // Generate list of classes for linking in between classes.
        ClassLinkList::getInstance()->generate($this->project, $this->options['filePrefix']);
    }

    /**
     * @return ClassReflection[]
     */
    public function getClassList(): array
    {
        return $this->generateClassList($this->project);
    }

    /**
     * @param ClassReflection $classReflection
     *
     * @return string
     */
    public function compileClass(ClassReflection $classReflection)
    {
        $contents = '';

        if (empty($this->options['frontMatterStyle'])) {
            $contents .= (new Heading($classReflection->getName(), 1))->compile();
        } elseif ('YAML' === $this->options['frontMatterStyle']) {
            $contents = (new Yaml(
                $classReflection->reflection->getFqsen(),
                $classReflection->getName()
            ))->compile();
        }

        $classReference = new ClassReference($classReflection);
        $contents .= $classReference->compile();

        return $contents;
    }

    /**
     * @param \phpDocumentor\Reflection\Php\Project $project
     *
     * @return ClassReflection[]
     */
    protected function generateClassList($project)
    {
        $classReflections = [];
        $files = $project->getFiles();

        $classMap = [];
        $traitMap = [];

        foreach ($files as $file) {
            foreach ($file->getClasses() as $class) {
                $classMap[$class->getFqsen()->__toString()] = $class;
            }
            foreach ($file->getTraits() as $trait) {
                $traitMap[$trait->getFqsen()->__toString()] = $trait;
            }
        }

        foreach ($files as $file) {
            foreach ($file->getClasses() as $class) {
                $classReflection = new ClassReflection($class);
                $parents = $this->getParentsRecursive($class, $classMap);

                foreach ($parents as $parent) {
                    $classReflection->addParentInformation($parent);
                }

                // Add trait information
                if (method_exists($class, 'getUsedTraits')) {
                    foreach ($class->getUsedTraits() as $usedTrait) {
                        $trait = $traitMap[$usedTrait->__toString()] ?? null;
                        if ($trait) {
                            $classReflection->addTraitInformation($trait);
                        }
                    }
                }

                if ($classReflection->shouldIgnore()) {
                    continue;
                }

                $classReflections[] = $classReflection;
            }

            // Process traits
            foreach ($file->getTraits() as $trait) {
                $classReflection = new ClassReflection($trait);

                if ($classReflection->shouldIgnore()) {
                    continue;
                }

                $classReflections[] = $classReflection;
            }
        }

        return $classReflections;
    }

    protected function getParentsRecursive($class, $classMap)
    {
        $parents = [];

        if ($class->getParent()) {
            $parent = $class->getParent();
            $parentClass = $classMap[$parent->__toString()] ?? null;

            if ($parentClass) {
                $parents[] = $parentClass;

                $parents = array_merge($parents, $this->getParentsRecursive($parentClass, $classMap));
            }
        }

        return $parents;
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
