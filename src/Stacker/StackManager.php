<?php
namespace Tintin\Stacker;

use function ob_end_clean;
use function ob_get_contents;
use Tintin\Tintin;
use function var_dump;

class StackManager
{
    /**
     * @var array
     */
    private $stacks = [];

    /**
     * @var null
     */
    private $current_key;

    /**
     * StackManager constructor.
     * @param Tintin $tintin
     */
    public function __construct(Tintin $tintin)
    {
        $this->tintin = $tintin;
    }

    /**
     * Permet d'inclure un fichier à compiler
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
                $this->stacks[$name] = $this->tintin->getCompiler()->complie($content);
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
                $content = ob_get_contents();
                ob_end_clean();
                $this->stacks[$this->current_key] = trim($this->tintin->getCompiler()->complie($content), "\n");
            }
        }
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