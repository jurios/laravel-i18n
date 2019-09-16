<?php

namespace Kodilab\LaravelI18n;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Kodilab\LaravelI18n\Commands\Editor;
use Kodilab\LaravelI18n\Commands\Generator;
use Kodilab\LaravelI18n\Commands\Generators\Config;
use Kodilab\LaravelI18n\Commands\Generators\ModelTranslation;
use Kodilab\LaravelI18n\Commands\Install;
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
    public function boot(\Illuminate\Routing\Router $router)
    {
        $config_path = __DIR__ . '/../config/config.php';
        $factories_path = __DIR__ . '/../database/factories';
        $views_path = __DIR__ . '/../resources/views';

        $this->publishes([
            $factories_path => database_path('factories')
        ], 'laravel-i18n-config');

        $this->loadViewsFrom($views_path, 'i18n');

        $this->commands([
            Install::class,
            Sync::class,
            Editor::class,
            ModelTranslation::class,
            Config::class,
            Generator::class
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
