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
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader = null)
    {
        $this->compiler = new Compiler;

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
        if (is_null($this->loader)) {
            return $this->executePlainRendering(
                trim($this->compiler->complie($filename)),
                $params
            );
        }

        extract($params);

        if (! $this->loader->fileExists($filename)) {
            $this->loader->failLoading($filename .' n\'exists pas');
        }

        $__tintin = $this;

        if (! $this->loader->isExpirated($filename)) {
            return require $this->loader->getCacheFileResolvedPath($filename);
        }

        $content = $this->loader->getFileContent($filename);

        $this->loader->cache(
            $filename, trim($this->compiler->complie($content), '\n')
        );

        return require $this->loader->getCacheFileResolvedPath($filename);
    }

    /**
     * Execute plain rendering code
     * 
     * @param string $content
     * @param array $params
     * @return string
     */
    private function executePlainRendering($content, $params)
    {
        $file = sys_get_temp_dir().'/'.time().'.php';

        file_put_contents($file, $content);

        extract($params);

        $r = require $file;

        @unlink($file);

        return $r;
    }

    /**
     * Get the compiler
     * 
     * @return Compiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }
}