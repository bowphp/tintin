<?php

namespace Tintin;

use Tintin\StackManager;
use Tintin\MacroManager;
use Tintin\LoaderInterface;
use Tintin\Exception\DirectiveNotAllowException;

class Tintin
{
    /**
     * The tintin parse instance
     *
     * @var Compiler
     */
    private Compiler $compiler;

    /**
     * The loader interface instance
     *
     * @var LoaderInterface
     */
    private ?LoaderInterface $loader;

    /**
     * The stack manager instance
     *
     * @var StackManager
     */
    private StackManager $stackManager;

    /**
     * The stack manager instance
     *
     * @var MacroManager
     */
    private MacroManager $macroManager;

    /**
     * The shared data
     *
     * @var array
     */
    private array $__data = [];

    /**
     * Tintin constructor.
     *
     * @param ?LoaderInterface $loader
     */
    public function __construct(?LoaderInterface $loader = null)
    {
        $this->loader = $loader;
        $this->compiler = new Compiler();
        $this->stackManager = new StackManager($this);
        $this->macroManager = new MacroManager($this);
    }

    /**
     * Get stack manager
     *
     * @return StackManager
     */
    public function getStackManager(): StackManager
    {
        return $this->stackManager;
    }

    /**
     * Get macro manager
     *
     * @return MacroManager
     */
    public function getMacroManager(): MacroManager
    {
        return $this->macroManager;
    }

    /**
     * Get loader
     *
     * @return ?LoaderInterface
     */
    public function getLoader(): ?LoaderInterface
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
    public function getSharedData(): array
    {
        return $this->__data;
    }

    /**
     * Make template rendering
     *
     * @param string $template
     * @param array $data
     * @return string
     * @throws
     */
    public function render($template, array $data = []): string
    {
        $__template = $template;

        if (is_null($this->loader)) {
            // Try to compile the plain string
            return $this->renderString($__template, $data);
        }

        // Check existence of file from cache
        if (! $this->loader->exists($__template)) {
            $this->loader->failLoading($__template . ' not found');
        }

        // Merge passing data to the shared data
        $this->pushSharedData($data);

        $__tintin = $this;

        // Extract the shared data
        extract($this->getSharedData());

        // Check if the file cache is not expire
        // If cache is not still alive we load template
        // and create the new cache for
        if (! $this->loader->isExpired($__template)) {
            $this->obFlushAndStar();

            require $this->loader->getCacheFileResolvedPath($__template);

            return $this->obGetContent();
        }

        // Put the template into cache
        $content = $this->loader->getFileContent($__template);

        $this->loader->cache(
            $__template,
            $this->compiler->compile($content)
        );

        $this->obFlushAndStar();

        require $this->loader->getCacheFileResolvedPath($__template);

        return $this->obGetContent();
    }

    /**
     * Compile simple template code
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    public function renderString(string $template, array $data = []): string
    {
        $__template = $template;
        return $this->executePlainRendering(
            trim($this->compiler->compile($__template)),
            array_merge($data, ['__tintin' => $this])
        );
    }

    /**
     * Execute plain rendering code
     *
     * @param string $content
     * @param array $data
     * @return string
     */
    private function executePlainRendering(string $content, array $data): string
    {
        $this->obFlushAndStar();

        extract($data);

        $parts = preg_split("/\n|\r\n/", $content);
        $parts = array_map(fn ($value) => trim($value), $parts);

        $filename = $this->createTmpFile(implode("\n\t", $parts));

        require $filename;

        @unlink($filename);

        return $this->obGetContent();
    }

    /**
     * Clean buffer
     *
     * @return string
     */
    private function obGetContent(): string
    {
        return (string) ob_get_clean();
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
     * @return string
     */
    private function createTmpFile(string $content): string
    {
        $tmp_dir = sys_get_temp_dir() . '/__tintin';

        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir, 0777);
        }

        $file = $tmp_dir . '/' . md5(microtime(true)) . '.php';

        file_put_contents($file, $content);

        return $file;
    }

    /**
     * Get the compiler
     *
     * @return Compiler
     */
    public function getCompiler(): Compiler
    {
        return $this->compiler;
    }

    /**
     * Push more directive in template system
     *
     * @param string $name
     * @param callable $handler
     * @param boolean $broken
     * @return mixed
     *
     * @throws DirectiveNotAllowException
     */
    public function directive(string $name, callable $handler, bool $broken = false)
    {
        $this->compiler->pushDirective($name, $handler, $broken);
    }
}
