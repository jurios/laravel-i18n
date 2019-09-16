<?php


namespace Kodilab\LaravelI18n;


use Kodilab\LaravelI18n\Models\Locale;

class Facade extends \Illuminate\Support\Facades\Facade
{
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
                    ->get('/sync', 'I18nController@sync')
                    ->name('sync');

                static::$app->make('router')
                    ->get('/locales/{locale}/translations', 'TranslationController@index')
                    ->name('locales.translations.index');

                static::$app->make('router')
                    ->patch('/locales/{locale}/translations/update', 'TranslationController@update')
                    ->name('locales.translations.update');


            });
    }
}