<?php
namespace Tintin;

use function str_replace;
use Tintin\Loader\LoaderInterface;
use function var_dump;

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
        $this->stackManager = new Stacker\StackManager($this);
    }

    /**
     * Permet de faire le rendu
     *
     * @param $filename
     * @param array $params
     * @return string
     */
    public function render($filename, array $params)
    {
        extract($params);

        if (is_null($this->loader)) {
            return trim($this->compiler->complie($filename), "\n");
        }

        if (! $this->loader->fileExists($filename)) {
            $this->loader->failLoading($filename .' n\'exists pas');
        }

        $__tintin = $this;

        if (! $this->loader->isExpirated($filename)) {
            var_dump($filename);
            return require $this->loader->getCacheFileResolvedPath($filename);
        }

        $content = $this->loader->getFileContent($filename);
        $this->loader->cache($filename, trim($this->compiler->complie($content), '\n'));

        return require $this->loader->getCacheFileResolvedPath($filename);
    }

    /**
     * @return Compiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }
}