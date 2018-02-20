<?php

namespace Teak\Console;

use Symfony\Component\Filesystem\Filesystem;
use Teak\Compiler\FrontMatter\Yaml;
use Teak\Compiler\HookReference;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command used to extract markdown-formatted documentation from classes
 */
class HookReferenceGenerator extends ReferenceGenerator
{
    protected function configure()
    {
        parent::configure();

        $this->setName('generate:hook-reference');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $files
     */
    public function handleClassCollection($input, $output, $files)
    {
        $projectFactory = \phpDocumentor\Reflection\Php\ProjectFactory::createInstance();
        $fs             = new Filesystem();
        $returns        = [];

        $types = array(
            'filter' => [
                'title' => 'Filter Hooks',
                'filename' => 'filters',
            ],
            'action' => [
                'title' => 'Action Hooks',
                'filename' => 'actions',
            ],
        );

        $project = $projectFactory->create('Teak', $files);

        // Get options
        $type         = $input->getOption(self::OPT_HOOK_TYPE);
        $outputFolder = $input->getOption(self::OPT_OUTPUT);
        $filename     = $input->getOption(self::OPT_FILENAME);

        // Make sure there’s a trailing slash
        $outputFolder = rtrim($outputFolder, '/') . '/';

        $title = $types[$type]['title'];

        if (empty($filename)) {
            $filename = $types[$type]['filename'];
        }

        // Use 'hooks' as default if default is not changed
        $parent = 'reference' === $input->getOption(self::OPT_PARENT)
            ? 'hooks'
            : $input->getOption(self::OPT_PARENT);

        $contents = (new Yaml($title, false, $parent))->compile();

        foreach ($project->getFiles() as $file) {
            $hookReference = new HookReference($file);
            $hookReference->setHookPrefix('timber');
            $hookReference->setHookType($type);

            $contents .= $hookReference->compile();

            $returns[] = $contents;
        }

        $filename = $input->getOption(self::OPT_FILE_PREFIX) . $filename . '.md';
        $fs->dumpFile(getcwd() . '/' . $outputFolder . $filename, $contents);

        return '';
    }
}
