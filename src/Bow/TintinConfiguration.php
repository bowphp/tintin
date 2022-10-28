<?php

namespace Tintin\Bow;

use Bow\View\View;
use Bow\Configuration\Loader;
use Bow\Configuration\Configuration;
use Tintin\Tintin;

class TintinConfiguration extends Configuration
{
    /**
     * {@inheritdoc}
     * @throws
     */
    public function create(Loader $config)
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
    public function run()
    {
        $view = $this->container->make('view');

        $this->onRunning($view->getTemplate()->getTemplate());
    }

    /**
     * Customize tintin action
     *
     * @param Tintin $tintin
     * @return mixed
     */
    public function onRunning(Tintin $tintin)
    {
        //
    }
}
