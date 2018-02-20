<?php

namespace Teak\Console;

use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

        $this->setName('generate:function-reference');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $files
     */
    public function handleClassCollection($input, $output, $files)
    {
        $projectFactory = \phpDocumentor\Reflection\Php\ProjectFactory::createInstance();
        $project = $projectFactory->create('Teak', $files);

        // Make sure there’s a trailing slash
        $outputFolder = rtrim($input->getOption(self::OPT_OUTPUT), '/') . '/';

        $fs = new Filesystem();

        $returns = [];
        $contents = '';

        $contents .= (new Heading('Functions', 1))->compile();

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

        $filename = $input->getOption(self::OPT_FILE_PREFIX) . 'functions.md';

        $fs->dumpFile(getcwd() . '/' . $outputFolder . $filename, $contents);

        // return $returns;
    }
}
