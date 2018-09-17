<?php

namespace Tintin\Bow;

use Bow\Config\Config;
use Bow\View\EngineAbstract;
use duncan3dc\Laravel\BladeInstance as BladeInstance;

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

        $this->template = new Tintin([
            'path' => $config['view.path'],
            'cache' => $config['view.cache'],
            'extension' => 'tintin.php'
        ]);
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
