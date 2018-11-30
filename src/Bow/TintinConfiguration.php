<?php

namespace Tintin\Bow;

use Bow\View\View;
use Bow\Configuration\Loader;
use Bow\Configuration\Configuration;
use Tintin\Tintin;

class TintinConfiguration extends Configuration
{
    /**
     * @inheritDoc
     * @throws
     */
    public function create(Loader $config)
    {
        $this->container->bind('view', function () use ($config) {
            View::pushEngine('tintin', TintinEngine::class);

            View::configure($config);

            return View::getInstance();
        });
    }

    /**
     * @inheritDoc
     * @throws
     */
    public function run()
    {
        $this->container->make('view');
    }

    /**
     * Customize tintin action
     * 
     * @param Tintin $tintin
     * @return mixed
     */
    public function customizer(Tintin $tintin)
    {
        $tintin->directive('csrf', function (array $attribues = []) {
            return \csrf_token();
        });
    }
}
