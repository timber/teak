<?php

namespace Teak\Console;

use phpDocumentor\Reflection\File\LocalFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ReferenceGenerator
 */
abstract class ReferenceGenerator extends Command
{
    // Arguments
    const ARG_FILES = 'files';

    // Options
    const OPT_OUTPUT = 'output';
    const OPT_IGNORE = 'ignore';
    const OPT_FILE_NAME = 'file_name';
    const OPT_FILE_PREFIX = 'file_prefix';
    const OPT_FILE_TITLE = 'file_title';
    const OPT_FRONT_MATTER_STYLE = 'front_matter_style';

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $classCollection
     *
     * @return string|array Output message.
     */
    abstract protected function handleClassCollection($input, $output, $classCollection);

    protected function configure()
    {
        $this
            ->setDescription('Generate a reference')
            ->addArgument(
                self::ARG_FILES,
                InputArgument::REQUIRED,
                'Source directory or single PHP file'
            )
            ->addOption(
                self::OPT_OUTPUT,
                'o',
                InputOption::VALUE_REQUIRED,
                'Output destination.'
            )
            ->addOption(
                self::OPT_IGNORE,
                'i',
                InputOption::VALUE_REQUIRED,
                'List of directories to ignore',
                null
            )
            ->addOption(
                self::OPT_FILE_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'File Name (the .md extension is appended automatically)',
                null
            )
            ->addOption(
                self::OPT_FILE_PREFIX,
                null,
                InputOption::VALUE_REQUIRED,
                'File Prefix',
                null
            )
            ->addOption(
                self::OPT_FILE_TITLE,
                null,
                InputOption::VALUE_REQUIRED,
                'File Title (Heading 1 in the Markdown document). Only applicable to hooks and functions reference.',
                null
            )
            ->addOption(
                self::OPT_FRONT_MATTER_STYLE,
                null,
                InputOption::VALUE_REQUIRED,
                'Front Matter type. Currently, only "YAML" is supported (Will output a Heading 1 if not provided).',
                null
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $files = $this->getFiles($input);
        $message = $this->handleClassCollection($input, $output, $files);

        if (is_array($message)) {
            $message = implode(PHP_EOL, $message);
        }

        $output->writeln($message);

        return 0;
    }

    /**
     * Gets files as an array.
     *
     * @param InputInterface $input
     *
     * @return array|string|string[]|null
     */
    public function getFiles(InputInterface $input)
    {
        $files  = $input->getArgument(self::ARG_FILES);
        $ignore = explode(',', $input->getOption(self::OPT_IGNORE) ?: '');

        if (is_dir($files)) {
            $files = $this->getFilesInFolder($files, $ignore);
        } else {
            // Convert to array
            $files = [ $files ];
        }

        sort($files);

        // For the Reflection, all file paths need to be converted to LocalFile instances.
        $files = array_map(function($file) {
            return new LocalFile($file);
        }, $files);

        return $files;
    }

    protected function getFilesInFolder($dir, $ignore, $files = [])
    {
        /**
         * @var \SplFileInfo $f
         */
        foreach (new \FilesystemIterator($dir) as $f) {
            if ($f->isFile() && ! $f->isLink() && '.' !== substr($f->getFilename(), 0, 1)) {
                $files[] = $f->getRealPath();
            } elseif ($f->isDir()) {
                $files = $this->getFilesInFolder($f->getRealPath(), $ignore, $files);
            }
        }

        return $files;
    }
}
