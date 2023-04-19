<?php

namespace Tintin\Bow;

use Bow\Configuration\Loader;
use Bow\View\EngineAbstract;
use Tintin\Filesystem as TintinFilesystem;
use Tintin\Tintin;

class TintinEngine extends EngineAbstract
{
    /**
     * The template instance
     *
     * @var Tintin
     */
    private Tintin $template;

    /**
     * The template name
     *
     * @var string
     */
    protected string $name = 'tintin';

    /**
     * The template config
     *
     * @var array
     */
    protected array $config;

    /**
     * BladeEngine constructor.
     *
     * @param Loader $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $loader = new TintinFilesystem([
            'path' => $config['path'],
            'cache' => $config['cache'],
            'extension' => $this->getExtension()
        ]);

        $this->template = new Tintin($loader);
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function render(string $filename, array $data = []): string
    {
        $filename = $this->checkParseFile($filename, false);

        return $this->template->render($filename, $data);
    }

    /**
     * Get the Tintin Engine instance
     *
     * @return Tintin
     */
    public function getEngine(): Tintin
    {
        return $this->template;
    }

    /**
     * Get template extension
     *
     * @return string
     */
    private function getExtension(): string
    {
        return $this->config['extension'] ?? 'tintin.php';
    }
}
