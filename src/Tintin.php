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
    public function render($filename, array $params)
    {
        ob_start();

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
            require $this->loader->getCacheFileResolvedPath($filename);
            
            return ob_get_clean();
        }

        $content = $this->loader->getFileContent($filename);

        $this->loader->cache(
            $filename,
            $this->compiler->complie($content)
        );

        require $this->loader->getCacheFileResolvedPath($filename);

        return ob_get_clean();
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

        require $file;

        return ob_get_clean();
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
