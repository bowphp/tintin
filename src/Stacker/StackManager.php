<?php

namespace Tintin\Stacker;

use Tintin\Tintin;

class StackManager
{
    /**
     * @var array
     */
    private $stacks = [];

    /**
     * @var array
     */
    private $pushes = [];

    /**
     * @var Tintin
     */
    private $tintin;

    /**
     * StackManager constructor.
     * @param Tintin $tintin
     */
    public function __construct(Tintin $tintin)
    {
        $this->tintin = $tintin;
    }

    /**
     * Include a file to compile
     *
     * @param string $filename
     * @param array $context
     * @return string
     */
    public function includeFile($filename, $context = [])
    {
        return $this->tintin->render($filename, $context);
    }

    /**
     * Open the stream for a #block
     *
     * @param string $name
     * @param string $content
     */
    public function startStack($name, $content = '')
    {
        if (is_null($content)) {
            if (ob_start()) {
                $this->stacks[] = $name;
            }
        } else {
            $this->pushes[$name] = $content;
        }
    }

    /**
     * Closes the current flow
     */
    public function endStack()
    {
        $stacks = array_pop($this->stacks);

        $stacks = (array) $stacks;

        foreach ($stacks as $block) {
            if ($block !== true) {
                $content = $this->tintin->getCompiler()->complie(ob_get_clean());

                $this->pushes[$block] = trim($content, "\n");
            }
        }
    }

    /**
     * Allows you to retrieve the contents of a stack
     *
     * @param string $name
     * @return mixed|null
     */
    public function getStack($name)
    {
        return array_key_exists($name, $this->pushes) ? $this->pushes[$name] : null;
    }

    /**
     * Collect the collector stack
     *
     * @return mixed|null
     */
    public function getStacks()
    {
        return $this->stacks;
    }
}
