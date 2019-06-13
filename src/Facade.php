<?php


namespace Kodilab\LaravelI18n;


class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return I18nManager::class;
    }

    public static function editorRoutes(array $options = [])
    {
        static::$app->make('router')
            ->prefix('i18n')
            ->name('i18n.')
            ->namespace('I18n')
            ->group(function () {

                static::$app->make('router')
                    ->get('/', 'DashboardController@dashboard')
                    ->name('dashboard');

                static::$app->make('router')
                    ->resource('/locales', 'LocaleController');

                static::$app->make('router')
                    ->get('/locales/{locale}/translations', 'TranslationController@index')
                    ->name('locales.translations.index');
            });
    }
}