<?php

namespace Tintin\Bow;

use Bow\View\View;
use Bow\Configuration\Loader;
use Bow\Configuration\Configuration;

class TintinConfiguration extends Configuration
{
    /**
     * @inheritDoc
     * @throws
     */
    public function create(Loader $config)
    {
        $config['view.engine'] = 'tintin';

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
}
