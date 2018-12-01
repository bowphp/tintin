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
        $view = $this->container->make('view');

        $this->customizer($view->getTemplate()->getTemplate());
    }

    /**
     * Customize tintin action
     *
     * @param Tintin $tintin
     * @return mixed
     */
    private function customizer(Tintin $tintin)
    {
        //
    }
}
