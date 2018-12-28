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
     * @param array $data
     * @param array $context
     * @return string
     */
    public function includeFile($filename, $data = [], $context = [])
    {
        $this->tintin->pushSharedData(array_merge($context, $data));

        $data = $this->tintin->getSharedData();

        return $this->tintin->render($filename, $data);
    }

    /**
     * Open the stream for a #block
     *
     * @param string $name
     * @param string $content
     */
    public function startStack($name, $content = null)
    {
        if (is_null($content)) {
            ob_start();
        }

        $this->stacks[] = $name;
        
        $this->pushes[$name] = $content;
    }

    /**
     * Closes the current flow
     */
    public function endStack()
    {
        $stacks = array_pop($this->stacks);

        $stacks = (array) $stacks;

        foreach ($stacks as $block) {
            $content = $this->pushes[$block];

            if (is_null($content)) {
                $content = $this->tintin->getCompiler()->compile(ob_get_clean());
            }

            $this->pushes[$block] = trim($content, "\n");
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
        if (array_key_exists($name, $this->pushes)) {
            return $this->tintin->renderString(
                $this->pushes[$name],
                ['__tintin' => $this->tintin]
            );
        }

        return null;
    }

    /**
     * Collect the collector stack
     *
     * @return array
     */
    public function getStacks()
    {
        return $this->stacks;
    }
}
