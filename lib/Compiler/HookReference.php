<?php

namespace Teak\Compiler;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Php\File;

/**
 * Class HookReference
 *
 * @package Teak\HookReference
 */
class HookReference implements CompilerInterface
{
    /**
     * @var File
     */
    protected $file;

    private $lastDocBlock = null;

    private $hookPrefix = '';

    private $hookType = null;

    private $contents;

    public $hooks = [
        'apply' => [],
        'do'    => [],
    ];

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function setHookPrefix($prefix)
    {
        $this->hookPrefix = $prefix;
    }

    public function setHookType($type)
    {
        $this->hookType = $type;
    }

    private function parse()
    {
        $tokens = $this->getTokens();
        $index  = 0;
        $lastIndex = 0;

        foreach ($tokens as $key => $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $type = $entry[0];
            $contents = $entry[1];

            if (T_DOC_COMMENT === $type) {
                $docBlockFactory = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
                $docBlock = $docBlockFactory->create($contents);

                if (!$this->isHookComment($docBlock)) {
                    continue;
                }

                $this->lastDocBlock = $docBlock;
            } elseif (T_STRING === $type && $this->isHook($contents)) {
                $hookName = $this->findNextHookName($tokens, $key);

                if ($this->isValidHook($hookName)) {
                    $hookType = $this->getHookType($contents);

                    if (!empty($this->lastDocBlock)) {
                        $lastIndex = $index;

                        $this->hooks[$hookType][$index] = [
                            'name' => $hookName,
                            'docBlock' => $this->lastDocBlock,
                            'variations' => [],
                        ];
                    } else {
                        /**
                         * Check for a repeating hook with more parameters.
                         *
                         * E.g.
                         * - namespace/action/status/{$status}
                         * - namespace/action/status/{$status}/{$id}
                         *
                         * Add the repeating hook as a variation of the first hook.
                         */
                        if (!empty(strstr($hookName, $this->hooks[$hookType][$lastIndex]['name']))) {
                            $this->hooks[$hookType][$lastIndex]['variations'][] = $hookName;
                        }
                    }

                    $index++;
                    $this->lastDocBlock = null;
                }
            }
        }
    }

    public function findNextHookName($tokens, $key)
    {
        $name = '';
        $index = 2;

        while ($index <= 4 && !$this->isValidHook($name)) {
            if (is_array($tokens[$key + $index])) {
                $name = $this->cleanToken($tokens[$key + $index][1]);
            } else {
                $name = $tokens[$key + $index];
            }

            $index ++;
        }

        // Check for a filter with variable name ("filter_name_{$suffix}")
        while (!in_array($tokens[$key + $index], [',', '\'', '"'], true)) {
            if (is_array($tokens[$key + $index])) {
                $name .= $this->cleanToken($tokens[$key + $index][1]);
            } else {
                $name .= $tokens[$key + $index];
            }

            $index++;
        }

        return $name;
    }

    public function cleanToken($token)
    {
        return str_replace(array("'", "\n", "\t"), '', $token);
    }

    /**
     * @param array $hook
     */
    public function compileHook($hook)
    {
        // Hook title
        $this->contents .= (new Heading($hook['name'], 2))->compile();

        // Hook Variations
        $this->contents .= (new Hook\Variation($hook))->compile();

        if (!$hook['docBlock']) {
            return;
        }

        $this->contents .= (new Hook($hook['docBlock']))->compile();
    }

    public function compile()
    {
        $this->parse();
        $this->contents = '';

        foreach ($this->hooks as $hookType => $hooks) {
            if (!empty($this->hookType) && $hookType !== $this->hookType) {
                continue;
            }

            foreach ($hooks as $hook) {
                $this->compileHook($hook);
            }
        }

        return $this->contents;
    }

    private function isValidHook($name)
    {
        if (empty($name)) {
            return false;
        }

        if (empty($this->hookPrefix)) {
            return true;
        }

        $strlen = mb_strlen($this->hookPrefix);
        $prefix = mb_strtolower(substr($name, 0, $strlen));

        return $prefix === $this->hookPrefix;
    }

    private function isHook($name)
    {
        $functions = array(
            'apply_filters',
            'apply_filters_ref_array',
            'apply_filters_deprecated',
            'do_action',
            'do_action_ref_array',
            'do_action_deprecated',
        );

        return in_array($name, $functions);
    }

    private function getHookType($name)
    {
        $name = explode('_', $name);
        $name = reset($name);

        switch ($name) {
            case 'apply':
                return 'filter';
            case 'do':
                return 'action';
        }

        return $name;
    }

    /**
     * @param DocBlock $docBlock
     *
     * @return bool
     */
    private function isHookComment($docBlock)
    {
        $keywords = ['Filters', 'Fires'];

        foreach ($keywords as $keyword) {
            if (substr($docBlock->getSummary(), 0, mb_strlen($keyword)) === $keyword) {
                return true;
            }
        }

        return false;
    }

    private function getTokens()
    {
        $tokens = token_get_all($this->file->getSource());

        return $tokens;
    }
}
