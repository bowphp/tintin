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
        $this->app->capsule('view', function () {
            View::pushEngine('tintin', TintinEngine::class);

            View::configure($config);

            return View::getInstance();
        });
    }

    /**
     * @inheritDoc
     * @throws
     */
    public function start()
    {
        $this->app->capsule('view')->setEngine('tintin');
    }
}
