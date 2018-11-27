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
     * @var Stacker\StackManager
     */
    private $stackManager;

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
     *
     * @throws
     */
    public function render($filename, array $params = [])
    {
        if (is_null($this->loader)) {
            return $this->renderString($filename, $params);
        }

        if (! $this->loader->fileExists($filename)) {
            $this->loader->failLoading($filename .' n\'exists pas');
        }

        $__tintin = $this;
        
        ob_start();

        extract($params);

        if (! $this->loader->isExpirated($filename)) {
            require $this->loader->getCacheFileResolvedPath($filename);
            
            return $this->obGetContent();
        }

        $content = $this->loader->getFileContent($filename);

        $this->loader->cache(
            $filename,
            $this->compiler->complie($content)
        );

        require $this->loader->getCacheFileResolvedPath($filename);

        return $this->obGetContent();
    }

    /**
     * Compile simple template code
     *
     * @param string $data
     * @param array $params
     * @return string
     */
    public function renderString($data, array $params = [])
    {
        ob_start();

        return $this->executePlainRendering(
            trim($this->compiler->complie($data)),
            $params
        );
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
        extract($params);

        require $filename = $this->createTmpFile($content);

         @unlink($filename);

        return $this->obGetContent();
    }

    /**
     * Clean buffer
     *
     * @return string
     */
    private function obGetContent()
    {
        $data = ob_get_contents();

        ob_end_flush();

        return $data;
    }

    /**
     * Create tmp compile file
     *
     * @param string $content
     * @return string
     */
    private function createTmpFile($content)
    {
        $file = sys_get_temp_dir().'/'.md5(microtime(true)).'.php';

        file_put_contents($file, $content);

        return $file;
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
