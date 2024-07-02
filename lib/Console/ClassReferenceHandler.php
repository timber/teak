<?php

namespace Teak\Console;

use phpDocumentor\Reflection\Php\Class_;
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
    public function compile_class(ClassReflection $classReflection)
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

        $class_map = [];

        foreach ($files as $file) {
            foreach ($file->getClasses() as $class) {
                $class_map[$class->getFqsen()->__toString() ] = $class;
            }
        }

        foreach ($files as $file) {
            foreach ($file->getClasses() as $class) {
                $classReflection = new ClassReflection($class);

                if ($classReflection->getParent()) {
                    $parent = $class_map[ $classReflection->getParent()->__toString() ] ?? null;

                    if ($parent) {
                        $classReflection->setParentInformation($parent);
                    }
                }

                if ($classReflection->shouldIgnore()) {
                    continue;
                }

                $classReflections[] = $classReflection;
            }
        }

        return $classReflections;
    }

    public function getProject() : Project
    {
        return $this->project;
    }
}
