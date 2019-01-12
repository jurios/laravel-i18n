<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Session;


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
    public static function routes($scope = null)
    {

        if (is_null($scope) || $scope === 'editor')
        {
            static::$app->make('router')->middleware('callback')->prefix('i18n')->name('i18n.')->group(function () {

                static::$app->make('router')->get('/', function () {
                    return redirect()->route('i18n.languages.index');
                })->name('dashboard');

                static::$app->make('router')
                    ->get('languages', '\Kodilab\LaravelI18n\Controllers\I18nLanguagesController@index')
                    ->name('languages.index');

                static::$app->make('router')
                    ->get('languages/{language}/enable/dialog', '\Kodilab\LaravelI18n\Controllers\I18nLanguagesController@enable_dialog')
                    ->name('languages.enable.dialog');

                static::$app->make('router')
                    ->patch('languages/{language}/enable', '\Kodilab\LaravelI18n\Controllers\I18nLanguagesController@enable')
                    ->name('languages.enable');

                static::$app->make('router')
                    ->get('languages/{language}/disable/dialog', '\Kodilab\LaravelI18n\Controllers\I18nLanguagesController@disable_dialog')
                    ->name('languages.disable.dialog');

                static::$app->make('router')
                    ->patch('languages/{language}/disable', '\Kodilab\LaravelI18n\Controllers\I18nLanguagesController@disable')
                    ->name('languages.disable');

                static::$app->make('router')
                    ->get('languages/{language}/translations', '\Kodilab\LaravelI18n\Controllers\I18nTranslationsController@index')
                    ->name('languages.translations.index');

                static::$app->make('router')
                    ->patch('languages/{language}/translations/{md5}', '\Kodilab\LaravelI18n\Controllers\I18nTranslationsController@update')
                    ->name('languages.translations.update');

                static::$app->make('router')
                    ->get('languages/{language}/translations/{md5}/info', '\Kodilab\LaravelI18n\Controllers\I18nTranslationsController@info')
                    ->name('languages.translations.info');

                static::$app->make('router')
                    ->get('settings/languages', '\Kodilab\LaravelI18n\Controllers\I18nSettingsController@languages')
                    ->name('settings.languages.index');

            });
        }

        if (is_null($scope) || $scope === 'public')
        {
            static::$app->make('router')->middleware('callback')->prefix('i18n')->name('i18n.')->group(function () {

                static::$app->make('router')->get('/setLocale/{locale}', function (Locale $locale) {

                    Session::put('locale', $locale);

                    return redirect()->back();
                })->name('setLocale');
            });
        }
    }

    public static function generateModelI18nTable(string $model, array $translatable_attributes)
    {
        Schema::create($model . config('i18n.tables.model_translations_suffix', '_i18n'), function (Blueprint $table)
        use ($model, $translatable_attributes) {
            $table->increments('id');
            $table->unsignedInteger('resource_id');
            $table->unsignedInteger('language_id');

            foreach ($translatable_attributes as $attribute => $data_type)
            {
                $table->$data_type($attribute);
            }

            $table->foreign('resource_id')->references('id')->on($model)
                ->onDelete('cascade');

            $table->foreign('language_id')->references('id')->on(config('i18n.tables.languages'))
                ->onDelete('cascade');
        });
    }

    public static function dropIfExistsModelI18nTable(string $model)
    {
        Schema::dropIfExists($model . config('i18n.tables.model_translations_suffix', '_i18n'));
    }
}
