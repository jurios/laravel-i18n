<?php


namespace Kodilab\LaravelI18n\Providers;


use Illuminate\Support\ServiceProvider;
use Kodilab\LaravelI18n\i18n\i18NManager;

class i18nServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('i18n', function ($app) {
            return new i18NManager($this->app['config']);
        });
    }
}