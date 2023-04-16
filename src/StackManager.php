<?php

namespace Tintin;

use Tintin\Tintin;

class StackManager
{
    /**
     * The stack collector
     *
     * @var array
     */
    private array $stacks = [];

    /**
     * The stack rendered collector
     *
     * @var array
     */
    private array $pushes = [];

    /**
     * The Tintin instance
     *
     * @var Tintin
     */
    private Tintin $tintin;

    /**
     * StackManager constructor.
     *
     * @param Tintin $tintin
     *
     * @return void
     */
    public function __construct(Tintin $tintin)
    {
        $this->tintin = $tintin;
    }

    /**
     * Include a file to compile.
     * The all logic is base on data structure algorithm call stack
     *
     * @param string $filename
     * @param array $data
     * @param array $context
     * @return string
     */
    public function includeFile(string $filename, array $data = [], array $context = []): string
    {
        $this->tintin->pushSharedData(array_merge($context, $data));

        $data = $this->tintin->getSharedData();

        return $this->tintin->render($filename, $data);
    }

    /**
     * Include a file to compile if the condition match.
     * The all logic is base on data structure algorithm call stack
     *
     * @param string $filename
     * @param array $data
     * @param array $context
     * @return string
     */
    public function includeFileIf(
        bool $condition,
        string $filename,
        array $data = [],
        array $context = []
    ): string {
        if ($condition) {
            return $this->includeFile($filename, $data, $context);
        }

        return "";
    }

    /**
     * Open the stream for a %block
     *
     * @param string $name
     * @param string $content
     * @return void
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
     *
     * @return void
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
     * @param string $default
     * @return mixed
     */
    public function getStack(string $name, ?string $default = null)
    {
        if (array_key_exists($name, $this->pushes)) {
            return $this->tintin->renderString(
                $this->pushes[$name],
                ['__tintin' => $this->tintin]
            );
        }

        return $default;
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
