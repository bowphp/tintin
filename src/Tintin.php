<?php

namespace Tintin;

use Closure;
use Tintin\Exception\DirectiveNotAllowException;
use Tintin\Loader\LoaderInterface;

class Tintin
{
    /**
     * The tintin parse instance
     *
     * @var Compiler;
     */
    private $compiler;

    /**
     * The loader interface instance
     *
     * @var LoaderInterface
     */
    private $loader;

    /**
     * The stack manager instance
     *
     * @var Stacker\StackManager
     */
    private $stackManager;

    /**
     * The shared data
     *
     * @var array
     */
    private $__data = [];

    /**
     * Tintin constructor.
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader = null)
    {
        $this->loader = $loader;
        
        $this->compiler = new Compiler;

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
     * Push shared data
     *
     * @param array $data
     */
    public function pushSharedData(array $data)
    {
        // The arrangement of values is very important
        // To refresh the old variables which are
        // a name with the new comer
        $this->__data = array_merge($this->__data, $data);
    }

    /**
     * Get define shared data
     *
     * @return array
     */
    public function getSharedData()
    {
        return $this->__data;
    }

    /**
     * Make template rendering
     *
     * @param string $template
     * @param array $data
     *
     * @return string
     * @throws
     */
    public function render($template, array $data = [])
    {
        if (is_null($this->loader)) {
            return $this->renderString($template, $data);
        }

        if (! $this->loader->exists($template)) {
            $this->loader->failLoading($template .' not found');
        }

        $this->pushSharedData($data);

        $__tintin = $this;

        extract($this->getSharedData());

        /**
         * Load template when is not a cached file
         */
        if (! $this->loader->isExpired($template)) {
            $this->obFlushAndStar();

            require $this->loader->getCacheFileResolvedPath($template);
            
            return $this->obGetContent();
        }

        /**
         * Put the template into cache
         */
        $content = $this->loader->getFileContent($template);

        $this->loader->cache(
            $template,
            $this->compiler->compile($content)
        );

        $this->obFlushAndStar();

        require $this->loader->getCacheFileResolvedPath($template);

        return $this->obGetContent();
    }

    /**
     * Compile simple template code
     *
     * @param string $template
     * @param array $data
     *
     * @return string
     */
    public function renderString($template, array $data = [])
    {
        return $this->executePlainRendering(
            trim($this->compiler->compile($template)),
            array_merge($data, ['__tintin' => $this])
        );
    }

    /**
     * Execute plain rendering code
     *
     * @param string $content
     * @param array $data
     *
     * @return string
     */
    private function executePlainRendering($content, $data)
    {
        $this->obFlushAndStar();

        extract($data);

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
     *
     * @return void
     */
    private function obFlushAndStar()
    {
        ob_start();
    }

    /**
     * Create tmp compile file
     *
     * @param string $content
     *
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

    /**
     * Push more directive in template system
     *
     * @param string $name
     * @param callable $handler
     * @param boolean $broken
     *
     * @return mixed
     * @throws DirectiveNotAllowException
     */
    public function directive($name, $handler, $broken = false)
    {
        $this->compiler->pushDirective($name, $handler, $broken);
    }
}
