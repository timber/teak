<?php

namespace Teak\Console;

use Symfony\Component\Filesystem\Filesystem;
use Teak\Compiler\ClassReference;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Teak\Compiler\FrontMatter\Yaml;
use Teak\Reflection\ClassReflection;

/**
 * Console command used to extract markdown-formatted documentation from classes
 */
class ClassReferenceGenerator extends ReferenceGenerator
{
    protected function configure()
    {
        parent::configure();

        $this->setName('generate:class-reference');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $files
     */
    protected function handleClassCollection($input, $output, $files)
    {
        $projectFactory = \phpDocumentor\Reflection\Php\ProjectFactory::createInstance();
        $project = $projectFactory->create('Teak', $files);

        // Make sure there’s a trailing slash
        $outputFolder = rtrim($input->getOption(self::OPT_OUTPUT), '/') . '/';

        $fs = new Filesystem();

        $returns = [];

        foreach ($project->getFiles() as $file) {
            foreach ($file->getClasses() as $class) {
                $classReflection = new ClassReflection($class);

                if ($classReflection->shouldIgnore()) {
                    continue;
                }

                $contents = '';

                if ('YAML' === $input->getOption(self::OPT_FRONT_MATTER_STYLE)) {
                    $contents = (new Yaml(
                        $class->getFqsen(),
                        $class->getName(),
                        $input->getOption(self::OPT_FRONT_MATTER_PARENT)
                    ))->compile();
                }

                $classReference = new ClassReference($class);
                $contents .= $classReference->compile();

                $filename = $input->getOption(self::OPT_FILE_PREFIX) . mb_strtolower($class->getName()) . '.md';

                $fs->dumpFile(getcwd() . '/' . $outputFolder . $filename, $contents);

                $returns[] = $contents;
            }
        }

        // return '';
    }
}
