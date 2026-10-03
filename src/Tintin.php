<?php

namespace Tintin;

use Tintin\StackManager;
use Tintin\MacroManager;
use Tintin\LoaderInterface;
use Tintin\Exception\DirectiveNotAllowException;

class Tintin
{
    /**
     * The compiler instance
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
     * The macro manager instance
     *
     * @var MacroManager
     */
    private MacroManager $macroManager;

    /**
     * The shared data
     *
     * @var array
     */
    private array $sharedData = [];

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
     * @return void
     */
    public function pushSharedData(array $data): void
    {
        // The arrangement of values is very important
        // To refresh the old variables which are
        // a name with the new comer
        $this->sharedData = array_merge($this->sharedData, $data);
    }

    /**
     * Get define shared data
     *
     * @return array
     */
    public function getSharedData(): array
    {
        return $this->sharedData;
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
        $this->stackManager->setContext($data);

        if (is_null($this->loader)) {
            // Try to compile the plain string
            return $this->renderString($template, $data);
        }

        // Check existence of file from cache
        if (! $this->loader->exists($template)) {
            $this->loader->failLoading($template . ' not found');
        }

        // Merge passing data to the shared data
        $this->pushSharedData($data);

        // Resolve and (re)compile from the trusted $template parameter BEFORE
        // any user data reaches the local scope. Otherwise a data key named
        // __template would overwrite it through extract() and redirect which
        // cache file gets required (view-selection hijack).
        if ($this->loader->isExpired($template)) {
            $this->loader->cache(
                $template,
                $this->compiler->compile($this->loader->getFileContent($template))
            );
        }

        return $this->requireInScope(
            $this->loader->getCacheFileResolvedPath($template),
            $this->getSharedData()
        );
    }

    /**
     * Require a compiled template in an isolated scope.
     *
     * The engine's own variables ($__path, $__tintin, $__data) are defined
     * before the user data is extracted, and EXTR_SKIP prevents that data from
     * overwriting them. A data key such as __path or __tintin is therefore
     * ignored instead of hijacking the include or the engine handle that the
     * compiled template relies on.
     *
     * @param string $__path
     * @param array $__data
     * @return string
     */
    private function requireInScope(string $__path, array $__data): string
    {
        $this->obFlushAndStart();

        $__tintin = $this;

        extract($__data, EXTR_SKIP);

        require $__path;

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
     * @return string
     */
    private function executePlainRendering(string $__content, array $__data): string
    {
        $this->obFlushAndStart();

        $__tintin = $this;

        // See requireInScope(): user data must never overwrite the reserved
        // engine variables, in particular $__content (which becomes the file
        // that is required) and $__file.
        extract($__data, EXTR_SKIP);

        $__file = $this->createTmpFile($__content);

        require $__file;

        @unlink($__file);

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
    private function obFlushAndStart(): void
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
            @mkdir($tmp_dir, 0700, true);
        }

        // tempnam() atomically creates a unique, unpredictable file owned by
        // this process (mode 0600). The previous md5(microtime()) name in a
        // shared, world-readable directory allowed symlink/TOCTOU attacks and
        // briefly exposed rendered output (possibly secrets) to local users.
        $file = tempnam($tmp_dir, 'tintin_');

        if ($file === false) {
            $file = $tmp_dir . '/' . bin2hex(random_bytes(16)) . '.php';
        }

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
