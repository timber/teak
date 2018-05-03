<?php

namespace Teak\Compiler\Hook;

use Teak\Compiler\CompilerInterface;

class Variation implements CompilerInterface
{
    /**
     * @var array Hook definition.
     */
    protected $hook;

    /**
     * Variation constructor.
     *
     * @param array $hook Hook definition.
     */
    public function __construct($hook)
    {
        $this->hook = $hook;
    }

    /**
     * Compile.
     *
     * @return string
     */
    public function compile() {
        $contents = '';

        if (empty($this->hook['variations'])) {
            return $contents;
        }

        $contents .= '- ' . $this->hook['name'] . self::NEWLINE;

        foreach ($this->hook['variations'] as $variation) {
            $contents .= '- ' . $variation . self::NEWLINE;
        }

        return $contents . self::NEWLINE;
    }
}
