<?php

namespace Tintin\Bow;

use Bow\View\View;
use Bow\Configuration\Loader;
use Bow\Configuration\Configuration;

class TintinConfiguration extends Configuration
{
    /**
     * {@inheritdoc}
     * @throws
     */
    public function create(Loader $config): void
    {
        $this->container->bind('view', function () use ($config) {
            View::pushEngine('tintin', TintinEngine::class);
            View::configure($config['view']);

            return View::getInstance();
        });
    }

    /**
     * {@inheritdoc}
     * @throws
     */
    public function run(): void
    {
        $this->container->make('view');
    }
}
