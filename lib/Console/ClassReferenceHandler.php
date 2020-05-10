<?php

namespace Teak\Console;

use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\ProjectFactory;
use Teak\Compiler\ClassLinkList;
use Teak\Compiler\ClassReference;
use Teak\Compiler\FrontMatter\Yaml;
use Teak\Compiler\Heading;
use Teak\Reflection\ClassReflection;

class ClassReferenceHandler
{
    protected $options;
    protected $project;

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

    public function getClassList()
    {
        return $this->generateClassList($this->project);
    }

    /**
     * @param Class_ $class
     *
     * @return string
     */
    public function compile($class)
    {
        $contents = '';

        if (empty($this->options['frontMatterStyle'])) {
            $contents .= (new Heading($class->getName(), 1))->compile();
        } elseif ('YAML' === $this->options['frontMatterStyle']) {
            $contents = (new Yaml(
                $class->getFqsen(),
                $class->getName()
            ))->compile();
        }

        $classReference = new ClassReference($class);
        $contents .= $classReference->compile();

        return $contents;
    }

    /**
     * @param \phpDocumentor\Reflection\Php\Project $project
     *
     * @return Class_[]
     */
    protected function generateClassList($project)
    {
        $classes = [];

        foreach ($project->getFiles() as $file) {
            foreach ($file->getClasses() as $class) {
                $classReflection = new ClassReflection($class);

                if ($classReflection->shouldIgnore()) {
                    continue;
                }

                $classes[] = $class;
            }
        }

        return $classes;
    }
}
