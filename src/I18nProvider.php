<?php

namespace Kodilab\LaravelI18n;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Kodilab\LaravelI18n\Commands\Sync;

class I18nProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/config.php';

        $assetsPath = __DIR__ . '/../views/assets';

        $this->publishes([
            $configPath => config_path('i18n.php')
        ]);

        $this->publishes([
            $assetsPath => public_path('vendor/i18n')
        ], 'public');

        $this->loadMigrationsFrom(__DIR__. '/../migrations');

        $this->commands([
            Sync::class,
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/config.php';

        $this->mergeConfigFrom(
            $configPath, 'i18n'
        );

        $this->app->bind(Linguist::class, function () {
            return new Linguist(
                new Filesystem,
                array_merge($this->app['config']['view.paths'], [$this->app['path']])
            );
        });
    }
}
