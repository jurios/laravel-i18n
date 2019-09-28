<?php

namespace Kodilab\LaravelI18n;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Kodilab\LaravelI18n\Commands\Editor;
use Kodilab\LaravelI18n\Commands\Generator;
use Kodilab\LaravelI18n\Commands\Generators\Config;
use Kodilab\LaravelI18n\Commands\Generators\Factories;
use Kodilab\LaravelI18n\Commands\Generators\Locale;
use Kodilab\LaravelI18n\Commands\Generators\Migrations;
use Kodilab\LaravelI18n\Commands\Generators\Translatable;
use Kodilab\LaravelI18n\Commands\Install;
use Kodilab\LaravelI18n\Commands\Sync;
use Kodilab\LaravelI18n\i18n\i18NManager;

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
        $this->commands([
            Install::class,
            Sync::class,
            Translatable::class,
            Config::class,
            Migrations::class,
            Locale::class,
            Factories::class,
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
