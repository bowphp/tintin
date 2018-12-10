<?php

namespace Tintin\Laravel;

use Illuminate\View\ViewServiceProvider;
use Tintin\Loader\Filesystem as TintinFilesystem;

class TintinServiceProvider extends ViewServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        //
    }
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->loadConfiguration();
    }

    /**
     * Register the view finder implementation.
     *
     * @return void
     */
    public function registerViewFinder()
    {
        $this->app->bind('view.finder', function ($app) {
            return new TintinFilesystem([
                'path' => $app['config']['view.paths'],
                'extension' => $app['config']['app.extension'],
                'cache' => '/path/to/the/cache/directory'
            ]);
        });
    }

    /**
     * Load the configuration files and allow them to be published.
     *
     * @return void
     */
    protected function loadConfiguration()
    {
        $config_path = __DIR__ . '/../config/tintin.php';

        if ( ! $this->isLumen()) {
            $this->publishes([$config_path => config_path('tintin.php')], 'config');
        }

        $this->mergeConfigFrom($config_path, 'tintin');
    }

    /**
     * Check if we are running Lumen or not.
     *
     * @return bool
     */
    protected function isLumen()
    {
        return strpos($this->app->version(), 'Lumen') !== false;
    }
}