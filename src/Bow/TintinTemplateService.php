<?php

namespace Tintin\Bow;

use Bow\View\View;
use Bow\Config\Config;
use Bow\Application\Service;

class TintinTemplateService extends Service
{
    /**
     * @inheritDoc
     * @throws
     */
    public function make(Config $config)
    {
        View::pushEngine('tintin', TintinEngine::class);

        View::configure($config);
    }

    /**
     * @inheritDoc
     * @throws
     */
    public function start()
    {
        View::getInstance()->setEngine('tintin');
    }
}
