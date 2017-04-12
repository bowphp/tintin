<?php
namespace Tintin;

use Tintin\Loader\LoaderInterace;

class Tintin
{
    /**
     * @var Compiler;
     */
    private $compiler;

    /**
     * @var LoaderInterace
     */
    private $loader;

    /**
     * Tintin constructor.
     * @param LoaderInterace|null $loader
     * @param array $config
     */
    public function __construct(LoaderInterace $loader = null, $config = [])
    {
        $this->compiler = new Compiler();
        $this->loader = $loader;
    }

    /**
     * Permet de faire le rendu
     *
     * @param $data
     * @param array $params
     */
    public function render($data, array $params)
    {
        extract($params);
        echo $this->compiler->complie($data);
    }
}