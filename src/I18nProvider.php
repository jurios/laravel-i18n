<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Support\ServiceProvider;

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

        $this->publishes([
            $configPath => config_path('i18n.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../migrations');
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
    }
}
