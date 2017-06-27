<?php
namespace Tintin\Stacker;

use function array_key_exists;
use function array_map;
use function get_defined_vars;
use function ob_clean;
use function ob_end_clean;
use function ob_get_level;
use Tintin\Tintin;
use function var_dump;

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
     * Permet de ferme le flux courrant
     */
    public function endStack()
    {
        $stacks = array_pop($this->stacks);
        $stacks = is_array($stacks) ? $stacks : [$stacks];

        foreach ($stacks as $block) {
            if ($block !== true) {
                $this->pushes[$block] = trim($this->tintin->getCompiler()->complie(ob_get_clean()), "\n");
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
        return array_key_exists($name, $this->pushes) ? $this->pushes[$name] : null;
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