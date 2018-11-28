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
     * Get stack manager
     * 
     * @return Stacker\StackManager
     */
    public function getStackManager()
    {
        return $this->stackManager;
    }

    /**
     * Get loader
     * 
     * @return LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Make template rendering
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

        // if (! $this->loader->fileExists($filename)) {
        //     $this->loader->failLoading($filename .' not found');
        // }

        $__tintin = $this;

        extract($params);

        /**
         * Load template when is not a cached file
         */
        if (! $this->loader->isExpirated($filename)) {
            $this->obFlushAndStar();

            require $this->loader->getCacheFileResolvedPath($filename);
            
            return $this->obGetContent();
        }

        /**
         * Put the template into cache
         */
        $content = $this->loader->getFileContent($filename);

        $this->loader->cache(
            $filename,
            $this->compiler->complie($content)
        );

        $this->obFlushAndStar();

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
        $this->obFlushAndStar();

        extract($params);

        $filename = $this->createTmpFile($content);

        require $filename;

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
        $data = ob_get_clean();

        return $data;
    }

    /**
     * Flush OB buffer and start new OB buffering
     */
    private function obFlushAndStar()
    {
        ob_start();
    }

    /**
     * Create tmp compile file
     *
     * @param string $content
     * @return string
     */
    private function createTmpFile($content)
    {
        $tmp_dir = sys_get_temp_dir().'/__tintin';

        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir, 0777);
        }

        $file = $tmp_dir.'/'.md5(microtime(true)).'.php';

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
