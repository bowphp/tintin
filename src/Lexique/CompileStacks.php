<?php

namespace Tintin\Lexique;

trait CompileStacks
{
    /**
     * @var array
     */
    private $stacks = [];

    /**
     * Block collector
     *
     * @param string $name
     * @param string $content
     */
    protected function pushBlock($name, $content = null)
    {
        if (is_null($content)) {
            if (ob_start()) {
                $this->stacks[$name] = true;
            }
        } else {
            if (is_string($content)) {
                $this->stacks[$name] = $content;
            }
        }
    }
}
