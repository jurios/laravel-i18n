<?php

namespace Kodilab\LaravelI18n;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return I18n::class;
    }

    /**
     * Register the routes for an application.
     *
     * @param  array  $options
     * @return void
     */
    public static function routes()
    {
        static::$app->make('router')->prefix('i18n')->name('i18n.')->group(function() {

            static::$app->make('router')->get('/', function () {
                    return redirect()->route('i18n.languages.index');
                })->name('dashboard');

            static::$app->make('router')
                ->get('languages', '\Kodilab\LaravelI18n\Controllers\I18nLanguagesController@index')
                ->name('languages.index');

            static::$app->make('router')
                ->patch('languages/{language}/enable', '\Kodilab\LaravelI18n\Controllers\I18nLanguagesController@enable')
                ->name('languages.enable');

            static::$app->make('router')
                ->get('languages/{language}/enable/dialog', '\Kodilab\LaravelI18n\Controllers\I18nLanguagesController@enable_dialog')
                ->name('languages.enable.dialog');

            static::$app->make('router')
                ->patch('languages/{language}/disable', '\Kodilab\LaravelI18n\Controllers\I18nLanguagesController@disable')
                ->name('languages.disable');

            static::$app->make('router')
                ->get('languages/{language}/disable/dialog', '\Kodilab\LaravelI18n\Controllers\I18nLanguagesController@disable_dialog')
                ->name('languages.disable.dialog');



            static::$app->make('router')
                ->get('languages/{language}/translations', '\Kodilab\LaravelI18n\Controllers\I18nTranslationsController@index')
                ->name('languages.translations');

            static::$app->make('router')
                ->post('languages/{language}/translations/{md5}', '\Kodilab\LaravelI18n\Controllers\I18nTranslationsController@update')
                ->name('languages.translations.update');

        });
    }
}
