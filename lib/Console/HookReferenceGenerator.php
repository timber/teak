<?php

namespace Teak\Console;

use Symfony\Component\Filesystem\Exception\IOException;
use Teak\Compiler\ClassLinkList;
use Teak\Compiler\FrontMatter\Yaml;
use Teak\Compiler\Heading;
use Teak\Compiler\HookReference;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
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
    const OPT_CLASS_REFERENCE_PATH = 'class_reference_path';

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('generate:hook-reference')
            ->setDescription('Generate a reference of WordPress hooks');

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
            )
            ->addOption(
                self::OPT_CLASS_REFERENCE_PATH,
                null,
                InputOption::VALUE_OPTIONAL,
                'Path to the class reference relative to the document root.',
                null
            );
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
        $output->writeln('<info>Generate '
         . ucfirst($input->getOption(self::OPT_HOOK_TYPE))
         . ' Hook Reference for ' . $input->getArgument(self::ARG_FILES)
         . '</info>'
        );

        $projectFactory = \phpDocumentor\Reflection\Php\ProjectFactory::createInstance();
        $project = $projectFactory->create('Teak', $files);

        $fs             = new Filesystem();
        $returns        = [];
        $contents       = '';

        $types = [
            'filter' => [
                'title' => 'Filter Hooks',
                'filename' => 'filters',
            ],
            'action' => [
                'title' => 'Action Hooks',
                'filename' => 'actions',
            ],
        ];

        // Get options
        $type          = $input->getOption(self::OPT_HOOK_TYPE);
        $outputFolder  = $input->getOption(self::OPT_OUTPUT);
        $filePrefix    = $input->getOption(self::OPT_FILE_PREFIX);
        $referencePath = $input->getOption(self::OPT_CLASS_REFERENCE_PATH);

        // Make sure there’s a trailing slash
        $outputFolder = rtrim($outputFolder, '/') . '/';

        // Generate list of classes for linking in between classes.
        ClassLinkList::getInstance()->generate($project, $filePrefix);
        ClassLinkList::getInstance()->setReferencePath($referencePath);

        $title = !empty($input->getOption(self::OPT_FILE_TITLE))
            ? $input->getOption(self::OPT_FILE_TITLE)
            : $types[$type]['title'];

        $frontMatter = $input->getOption(self::OPT_FRONT_MATTER_STYLE);

        if (empty($frontMatter)) {
            $contents = (new Heading($title, 1))->compile();
        } elseif ('YAML' === $frontMatter) {
            $contents = (new Yaml($title, null))->compile();
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
        $filename = $filePrefix . $filename . '.md';

        $filepath = $outputFolder . $filename;

        try {
            $fs->dumpFile(getcwd() . '/' . $filepath, $contents);
        } catch (IOException $e) {
            return $e->getMessage();
        }

        return 'Created ' . $filepath;
    }
}
