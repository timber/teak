<?php

namespace Teak\Console;

use Symfony\Component\Filesystem\Exception\IOException;
use Teak\Compiler\ClassLinkList;
use Teak\Compiler\ClassReference;
use Teak\Compiler\FrontMatter\Yaml;
use Teak\Compiler\Heading;
use Teak\Reflection\ClassReflection;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command used to extract markdown-formatted documentation from classes
 */
class ClassReferenceGenerator extends ReferenceGenerator
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('generate:class-reference')
            ->setDescription('Generate a class reference');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $files
     *
     * @return string|array
     */
    protected function handleClassCollection($input, $output, $files)
    {
        $output->writeln('<info>Generate Class Reference for ' . $input->getArgument(self::ARG_FILES) . '</info>');

        $projectFactory = \phpDocumentor\Reflection\Php\ProjectFactory::createInstance();
        $project = $projectFactory->create('Teak', $files);

        $fs      = new Filesystem();
        $returns = [];

        // Get options.
        $filePrefix = $input->getOption(self::OPT_FILE_PREFIX);

        // Make sure there’s a trailing slash
        $outputFolder = rtrim($input->getOption(self::OPT_OUTPUT), '/') . '/';

        // Generate list of classes for linking in between classes.
        ClassLinkList::getInstance()->generate($project, $filePrefix);

        foreach ($this->generateClassList($project) as $class) {
            $contents    = '';
            $frontMatter = $input->getOption(self::OPT_FRONT_MATTER_STYLE);

            if (empty($frontMatter)) {
                $contents .= (new Heading($class->getName(), 1))->compile();
            } elseif ('YAML' === $frontMatter) {
                $contents = (new Yaml(
                    $class->getFqsen(),
                    $class->getName()
                ))->compile();
            }

            $classReference = new ClassReference($class);
            $contents .= $classReference->compile();

            $filename = $filePrefix . mb_strtolower(str_replace("\\", '-', ltrim($class->getFqsen(), "\\"))) . '.md';
            $filepath = $outputFolder . $filename;

            try {
                $fs->dumpFile(getcwd() . '/' . $filepath, $contents);
            } catch (IOException $e) {
                $returns[] = $e->getMessage();
            }

            $returns[] = 'Created ' . $filepath;
        }

        return $returns;
    }

    /**
     * @param \phpDocumentor\Reflection\Php\Project $project
     *
     * @return array
     */
    public function generateClassList($project)
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
