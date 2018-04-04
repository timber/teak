<?php

namespace Teak\Console;

use Symfony\Component\Filesystem\Filesystem;
use Teak\Compiler\FrontMatter\Yaml;
use Teak\Compiler\Heading;
use Teak\Compiler\HookReference;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command used to extract markdown-formatted documentation from classes
 */
class HookReferenceGenerator extends ReferenceGenerator
{
    // Options
    const OPT_HOOK_TYPE = 'hook_type';
    const OPT_HOOK_PREFIX = 'hook_prefix';

    protected function configure()
    {
        parent::configure();

        $this->setName('generate:hook-reference');

        $this
            ->addOption(
                self::OPT_HOOK_TYPE,
                null,
                InputOption::VALUE_OPTIONAL,
                'Hook type ("filter" or "action")',
                null
            )
            ->addOption(
                self::OPT_HOOK_PREFIX,
                null,
                InputOption::VALUE_OPTIONAL,
                'Hook prefix (to select only hooks with a certain prefix)',
                null
            );
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
        $contents       = '';

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

        // Make sure there’s a trailing slash
        $outputFolder = rtrim($outputFolder, '/') . '/';

        // Use 'hooks' as default if default is not changed
        $parent = 'reference' === $input->getOption(self::OPT_FRONT_MATTER_PARENT)
            ? 'hooks'
            : $input->getOption(self::OPT_FRONT_MATTER_PARENT);

        $title = !empty($input->getOption(self::OPT_FILE_TITLE))
            ? $input->getOption(self::OPT_FILE_TITLE)
            : $types[$type]['title'];

        $frontMatter = $input->getOption(self::OPT_FRONT_MATTER_STYLE);

        if (empty($frontMatter)) {
            $contents = (new Heading($title, 1))->compile();
        } elseif ('YAML' === $frontMatter) {
            $contents = (new Yaml($title, false, $parent))->compile();
        }

        foreach ($project->getFiles() as $file) {
            $hookReference = new HookReference($file);
            $hookReference->setHookPrefix($input->getOption(self::OPT_HOOK_PREFIX));
            $hookReference->setHookType($type);

            $contents .= $hookReference->compile();

            $returns[] = $contents;
        }

        $filename = !empty($input->getOption(self::OPT_FILE_NAME))
            ? $input->getOption(self::OPT_FILE_NAME)
            : $types[$type]['filename'];

        // Add prefix
        $filename = $input->getOption(self::OPT_FILE_PREFIX) . $filename . '.md';

        $fs->dumpFile(getcwd() . '/' . $outputFolder . $filename, $contents);

        //return '';
    }
}
