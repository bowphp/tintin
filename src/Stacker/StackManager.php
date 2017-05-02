<?php
namespace Tintin\Stacker;


use Tintin\Compiler;

class StackManager
{
    /**
     * @var array
     */
    private $stacks = [];

    /**
     * @var null
     */
    private $current_key = null;

    /**
     * @var string
     */
    private $directory;

    /**
     * StackManager constructor.
     * @param $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
        $this->compiler = new Compiler();
    }

    /**
     * Permet d'inclure un fichier à compiler
     *
     * @param string $filename
     * @param array $context
     * @return string
     */
    public function include($filename, $context = [])
    {
        extract($context);
        $out = $this->compiler->complie(file_get_contents($this->directory.'/'.trim($filename, '/')));
        file_put_contents(__DIR__.'/gabage/worker.php', $out);
        return require __DIR__.'/gabage/worker.php';
    }

    /**
     * Permet d'ouvrir le flux pour un block
     *
     * @param string $name
     * @param null $content
     */
    public function startStack($name, $content = null)
    {
        $this->current_key = $name;

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

    /**
     * Permet de ferme le flux courrant
     */
    public function endStack()
    {
        if (isset($this->stacks, $this->current_key)) {
            if ($this->stacks[$this->current_key] === true) {
                $this->stacks[$this->current_key] = ob_get_clean();
            }
        }

        var_dump($this->stacks);
    }

    /**
     * Permet de récupérer le contenu d'un stack
     *
     * @param string $name
     * @return mixed|null
     */
    public function getStack($name)
    {
        return isset($this->stacks[$name]) ? $this->stacks[$name] : null;
    }

    /**
     * Permet de récupérer le stack collector
     *
     * @return mixed|null
     */
    public function getStacks()
    {
        return $this->stacks;
    }
}