<?php

namespace Teak\Console;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Teak\Compiler\FrontMatter\Yaml;
use Teak\Compiler\Heading;
use Teak\Compiler\Method\Method;
use Teak\Compiler\Method\Table;

/**
 * Console command used to extract markdown-formatted documentation from classes
 */
class FunctionReferenceGenerator extends ReferenceGenerator
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('generate:function-reference')
            ->setDescription('Generate a reference of global functions');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $files
     *
     * @return string|array
     */
    public function handleClassCollection($input, $output, $files)
    {
        $output->writeln('<info>Generate Function Reference for ' . $input->getArgument(self::ARG_FILES) . '</info>');

        $projectFactory = \phpDocumentor\Reflection\Php\ProjectFactory::createInstance();
        $project        = $projectFactory->create('Teak', $files);
        $fs             = new Filesystem();
        $returns        = [];
        $contents       = '';

        // Make sure there’s a trailing slash
        $outputFolder = rtrim($input->getOption(self::OPT_OUTPUT), '/') . '/';

        $title = !empty($input->getOption(self::OPT_FILE_TITLE))
            ? $input->getOption(self::OPT_FILE_TITLE)
            : 'Functions';

        $frontMatter = $input->getOption(self::OPT_FRONT_MATTER_STYLE);

        if (empty($frontMatter)) {
            $contents .= (new Heading($title, 1))->compile();
        } elseif ('YAML' === $frontMatter) {
            $contents .= (new Yaml($title, null, $input->getOption(self::OPT_FRONT_MATTER_PARENT)))->compile();
        }

        foreach ($project->getFiles() as $file) {
            $functions = $file->getFunctions();

            if (empty($functions)) {
                continue;
            }

            $contents .= (new Table($functions))->compile();

            foreach ($file->getFunctions() as $function) {
                $classReference = new Method($function);
                $contents .= $classReference->compile();

                $returns[] = $contents;
            }
        }

        $filename = !empty($input->getOption(self::OPT_FILE_NAME))
            ? $input->getOption(self::OPT_FILE_NAME)
            : 'functions';

        // Add prefix
        $filename = $input->getOption(self::OPT_FILE_PREFIX) .  $filename . '.md';

        $filepath = getcwd() . '/' . $outputFolder . $filename;

        try {
            $fs->dumpFile($filepath, $contents);
        } catch (IOException $e) {
            return $e->getMessage();
        }

        return count($returns) . ' functions dumped into ' . $filepath;
    }
}
