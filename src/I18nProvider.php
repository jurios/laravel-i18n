<?php

namespace Kodilab\LaravelI18n;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Kodilab\LaravelI18n\Commands\Sync;
use \Illuminate\Support\Facades\Blade;
use Kodilab\LaravelI18n\Middleware\SetLocale;

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
        $assets_path = __DIR__ . '/../public/assets';
        $views_path = __DIR__ . '/../views';
        $migrations_path = __DIR__. '/../migrations';

        $this->publishes([
            $config_path => config_path('i18n.php')
        ]);

        $this->publishes([
            $assets_path => public_path('vendor/laravel-i18n/assets')
        ], 'public');

        $this->loadViewsFrom($views_path, 'i18n');

        $this->loadMigrationsFrom($migrations_path);

        $this->commands([
            Sync::class,
        ]);

        $router->aliasMiddleware('set_locale', SetLocale::class);

        Blade::directive('ajaxmodal', function () {
            return "data-toggle=\"modal\" data-target=\"#placeholderModal\"";
        });
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
