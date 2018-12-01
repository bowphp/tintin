<?php

namespace Tintin\Bow;

use Bow\Configuration\Loader;
use Bow\View\EngineAbstract;
use Tintin\Loader\Filesystem as TintinFilesystem;
use Tintin\Tintin;

class TintinEngine extends EngineAbstract
{
    /**
     * @var Tintin
     */
    private $template;

    /**
     * @var string
     */
    protected $name = 'tintin';

    /**
     * BladeEngine constructor.
     *
     * @param Loader $config
     */
    public function __construct(Loader $config)
    {
        $this->config = $config;

        $loader = new TintinFilesystem([
            'path' => $config['view.path'],
            'cache' => $config['view.cache'],
            'extension' => $this->getExtension()
        ]);

        $this->template = new Tintin($loader);
    }

    /**
     * @inheritDoc
     * @throws
     */
    public function render($filename, array $data = [])
    {
        $filename = $this->checkParseFile($filename, false);

        return $this->template->render($filename, $data);
    }

    /**
     * Get the Tintin Engine instance
     *
     * @return Tintin
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Get template extension
     *
     * @return string
     */
    private function getExtension()
    {
        return is_null($this->config['view.extension'])
            ? $this->config['view.extension']
            : 'tintin.php';
    }
}
