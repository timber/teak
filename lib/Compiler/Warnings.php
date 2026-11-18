<?php

namespace Teak\Compiler;

/**
 * Collects non-fatal problems found while compiling, so the console commands can report them.
 */
class Warnings
{
    private static $instance;

    protected $context = '';

    protected $warnings = [];

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Set the element (e.g. method) that is currently being compiled, to be prepended to warnings.
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = (string) $context;
    }

    /**
     * @param string $message
     */
    public function add($message)
    {
        $this->warnings[] = $this->context !== '' ? $this->context . ': ' . $message : $message;
    }

    /**
     * Get all collected warnings and reset the list.
     *
     * @return string[]
     */
    public function flush()
    {
        $warnings = array_values(array_unique($this->warnings));

        $this->warnings = [];
        $this->context = '';

        return $warnings;
    }
}
