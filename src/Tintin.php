<?php
namespace Tintin;

use Tintin\Loader\LoaderInterface;

class Tintin
{
    /**
     * @var Compiler;
     */
    private $compiler;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * Tintin constructor.
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader = null)
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