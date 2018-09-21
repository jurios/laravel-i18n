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
        static::$app->make('router')->middleware('callback')->prefix('i18n')->name('i18n.')->group(function() {

            static::$app->make('router')->get('/', function () {
                    return redirect()->route('i18n.languages.index');
                })->name('dashboard');

            static::$app->make('router')
                ->get('languages', '\Kodilab\LaravelI18n\Controllers\I18nLanguagesController@index')
                ->name('languages.index');

            static::$app->make('router')
                ->get('languages/{language}/default/dialog', '\Kodilab\LaravelI18n\Controllers\I18nLanguagesController@mark_default_dialog')
                ->name('languages.default.dialog');

            static::$app->make('router')
                ->patch('languages/{language}/default', '\Kodilab\LaravelI18n\Controllers\I18nLanguagesController@mark_default')
                ->name('languages.default');

            static::$app->make('router')
                ->get('languages/{language}/translations', '\Kodilab\LaravelI18n\Controllers\I18nTranslationsController@index')
                ->name('languages.translations');

            static::$app->make('router')
                ->patch('languages/{language}/translations/{md5}', '\Kodilab\LaravelI18n\Controllers\I18nTranslationsController@update')
                ->name('languages.translations.update');

            static::$app->make('router')
                ->get('languages/{language}/translations/{md5}/info', '\Kodilab\LaravelI18n\Controllers\I18nTranslationsController@info')
                ->name('languages.translations.info');

        });
    }
}
