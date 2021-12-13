<?php

namespace Tintin\Laravel;

use Illuminate\Support\ServiceProvider;
use Tintin\Loader\Filesystem as TintinFilesystem;

class TintinServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->loadConfiguration();
        $this->registerViewFinder();
        $this->registerViewLoader();
    }

    /**
     * Register the view finder implementation.
     * @return void
     */
    public function registerViewFinder()
    {
        $this->app->singleton('view.finder', function ($app) {
            return new TintinFilesystem([
                'path' => $app['config']['view.path'],
                'extension' => $app['config']['view.extension'],
                'cache' => $app['config']['view.cache']
            ]);
        });
    }

    /**
     * Register the view load implementation.
     * @return void
     */
    public function registerViewLoader()
    {
        $this->app->singleton('view', function ($app) {
            return new Tintin($app['view.finder']);
        });
    }

    /**
     * Load the configuration files and allow them to be published.
     * @return void
     */
    protected function loadConfiguration()
    {
        $config_path = __DIR__ . '/../../config/tintin.php';

        if (!$this->isLumen()) {
            $this->publishes([
                $config_path => config_path('view.php')
            ], 'config');
        }

        $this->mergeConfigFrom($config_path, 'view');
    }

    /**
     * Check if we are running Lumen or not.
     * @return bool
     */
    protected function isLumen()
    {
        return strpos($this->app->version(), 'Lumen') !== false;
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return ['view'];
    }
}
