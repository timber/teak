<?php

namespace Teak\Console;

use Symfony\Component\Filesystem\Exception\IOException;
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

        // Get options.
        $filePrefix = $input->getOption(self::OPT_FILE_PREFIX);
        $frontMatterStyle = $input->getOption(self::OPT_FRONT_MATTER_STYLE);

        $classReferenceHandler = new ClassReferenceHandler($files, [
            'filePrefix' => $filePrefix,
            'frontMatterStyle' => $frontMatterStyle
        ]);

        $fs      = new Filesystem();
        $returns = [];

        // Make sure there’s a trailing slash.
        $outputFolder = rtrim($input->getOption(self::OPT_OUTPUT), '/') . '/';

        foreach ($classReferenceHandler->getClassList() as $class) {
            $contents = $classReferenceHandler->compile($class);

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


}
