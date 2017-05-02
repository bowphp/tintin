<?php
namespace Tintin\Lexique;

trait CompileStacks
{
    private $stacks = [];

    /**
     * @param $name
     * @param null $content
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