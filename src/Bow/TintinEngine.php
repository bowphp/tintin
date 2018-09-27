<?php

namespace Tintin\Bow;

use Bow\Config\Config;
use Bow\View\EngineAbstract;
use Tintin\Loader\Filesystem as TintinFilesystem;
use Tintin\Tintin;

class TintinEngine extends EngineAbstract
{
    /**
     * @var BladeInstance
     */
    private $template;

    /**
     * @var string
     */
    protected $name = 'tintin';

    /**
     * BladeEngine constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        $loader = new TintinFilesystem([
            'path' => $config['view.path'],
            'cache' => $config['view.cache'],
            'extension' => is_null($config['view.extension']) ? $config['view.extension'] : 'tintin.php'
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
     * Get the BladeEngine instance
     *
     * @return BladeInstance
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
