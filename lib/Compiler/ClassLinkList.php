<?php

namespace Teak\Compiler;

use Teak\Reflection\ClassReflection;

class ClassLinkList
{
    private static $instance = null;

    protected $classList;

    protected $referencePath = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function set($classList)
    {
        $this->classList = $classList;
    }

    public function setReferencePath($path)
    {
        $this->referencePath = $path;
    }

    public function get()
    {
        return $this->classList;
    }

    public function getReferencePath()
    {
        return $this->referencePath;
    }

    /**
     * @param \phpDocumentor\Reflection\Php\Project $project
     * @param string                                $filePrefix
     *
     * @return array
     */
    public function generate($project, $filePrefix)
    {
        $classes = [];

        foreach ($project->getFiles() as $file) {
            foreach ($file->getClasses() as $class) {
                $classReflection = new ClassReflection($class);

                if ($classReflection->shouldIgnore()) {
                    continue;
                }

                $classes[ltrim($class->getFqsen()->__toString(), "\\")] = [
                    'path'     => $file->getPath(),
                    'filename' => $filePrefix . mb_strtolower(str_replace("\\", '-', ltrim($class->getFqsen(), "\\"))),
                ];
            }
        }

        $this->set($classes);

        return $classes;
    }
}
