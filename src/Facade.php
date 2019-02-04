<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;


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
    public static function editorRoutes($views = [])
    {
        static::$app->make('router')->middleware('callback')->prefix('i18n')->name('i18n.')->group(
            function () use ($views) {

                $action = 'I18nDashboardController@dashboard';
                static::$app->make('router')
                    ->get('/', '\Kodilab\LaravelI18n\Controllers\\' . $action)
                    ->name('dashboard');

                static::addCustomView($views, $action);

                $action = 'I18nLocalesController@index';
                static::$app->make('router')
                    ->get('locales', '\Kodilab\LaravelI18n\Controllers\I18nLocalesController@index')
                    ->name('locales.index');
                static::addCustomView($views, $action);

                $action = 'I18nLocalesController@create';
                static::$app->make('router')
                    ->get('locales/new', '\Kodilab\LaravelI18n\Controllers\I18nLocalesController@create')
                    ->name('locales.create');
                static::addCustomView($views, $action);

                $action = 'I18nLocalesController@store';
                static::$app->make('router')
                    ->post('locales', '\Kodilab\LaravelI18n\Controllers\I18nLocalesController@store')
                    ->name('locales.store');
                static::addCustomView($views, $action);

                $action = 'I18nLocalesController@edit';
                static::$app->make('router')
                    ->get('locales/{locale}/edit', '\Kodilab\LaravelI18n\Controllers\I18nLocalesController@edit')
                    ->name('locales.edit');
                static::addCustomView($views, $action);

                $action = 'I18nLocalesController@update';
                static::$app->make('router')
                    ->patch('locales/{locale}', '\Kodilab\LaravelI18n\Controllers\I18nLocalesController@update')
                    ->name('locales.update');
                static::addCustomView($views, $action);

                $action = 'I18nLocalesController@enable_dialog';
                static::$app->make('router')
                    ->get('locales/{locale}/enable/dialog',
                        '\Kodilab\LaravelI18n\Controllers\I18nLocalesController@enable_dialog')
                    ->name('locales.enable.dialog');
                static::addCustomView($views, $action);

                $action = 'I18nLocalesController@enable';
                static::$app->make('router')
                    ->patch('locales/{locale}/enable',
                        '\Kodilab\LaravelI18n\Controllers\I18nLocalesController@enable')
                    ->name('locales.enable');
                static::addCustomView($views, $action);

                $action = 'I18nLocalesController@disable_dialog';
                static::$app->make('router')
                    ->get('locales/{locale}/disable/dialog',
                        '\Kodilab\LaravelI18n\Controllers\I18nLocalesController@disable_dialog')
                    ->name('locales.disable.dialog');
                static::addCustomView($views, $action);

                $action = 'I18nLocalesController@destroy_dialog';
                static::$app->make('router')
                    ->get('locales/{locale}/destroy/dialog',
                        '\Kodilab\LaravelI18n\Controllers\I18nLocalesController@destroy_dialog')
                    ->name('locales.destroy.dialog');
                static::addCustomView($views, $action);

                $action = 'I18nLocalesController@destroy';
                static::$app->make('router')
                    ->delete('locales/{locale}/destroy',
                        '\Kodilab\LaravelI18n\Controllers\I18nLocalesController@destroy')
                    ->name('locales.destroy');
                static::addCustomView($views, $action);

                $action = 'I18nLocalesController@disable';
                static::$app->make('router')
                    ->patch('locales/{locale}/disable',
                        '\Kodilab\LaravelI18n\Controllers\I18nLocalesController@disable')
                    ->name('locales.disable');
                static::addCustomView($views, $action);

                $action = 'I18nTranslationsController@index';
                static::$app->make('router')
                    ->get('locales/{locale}/translations',
                        '\Kodilab\LaravelI18n\Controllers\I18nTranslationsController@index')
                    ->name('locales.translations.index');
                static::addCustomView($views, $action);

                $action = 'I18nTranslationsController@update';
                static::$app->make('router')
                    ->patch('locales/{locale}/translations/{md5}',
                        '\Kodilab\LaravelI18n\Controllers\I18nTranslationsController@update')
                    ->name('locales.translations.update');
                static::addCustomView($views, $action);

                $action = 'I18nTranslationsController@info';
                static::$app->make('router')
                    ->get('locales/{locale}/translations/{md5}/info',
                        '\Kodilab\LaravelI18n\Controllers\I18nTranslationsController@info')
                    ->name('locales.translations.info');
                static::addCustomView($views, $action);

                $action = 'I18nSettingsController@languages';
                static::$app->make('router')
                    ->get('settings/locales',
                        '\Kodilab\LaravelI18n\Controllers\I18nSettingsController@languages')
                    ->name('settings.locales.index');
                static::addCustomView($views, $action);

            }
        );
    }

    static private function addCustomView($views, $action)
    {
        if (isset($views[$action]))
        {
            static::$app->make('config')->set('i18n.views.' .$action, $views[$action]);
        }
    }

    public static function localeChangerRoutes()
    {
        static::$app->make('router')->middleware('callback')->prefix('i18n')->name('i18n.')->group(function () {

            static::$app->make('router')->get('/setLocale/{locale}', function (Locale $locale) {

                \Illuminate\Support\Facades\Request::session()->put('locale', $locale);

                return redirect()->back();
            })->name('setLocale');
        });
    }

    public static function generateModelI18nTable(string $model, array $translatable_attributes)
    {
        Schema::create($model . config('i18n.tables.model_translations_suffix', '_i18n'), function (Blueprint $table)
        use ($model, $translatable_attributes) {
            $table->increments('id');
            $table->unsignedInteger('resource_id');
            $table->unsignedInteger('locale_id');

            foreach ($translatable_attributes as $attribute => $data_type)
            {
                $table->$data_type($attribute);
            }

            $table->foreign('resource_id')->references('id')->on($model)
                ->onDelete('cascade');

            $table->foreign('locale_id')->references('id')->on(config('i18n.tables.locales'))
                ->onDelete('cascade');
        });
    }

    public static function dropIfExistsModelI18nTable(string $model)
    {
        Schema::dropIfExists($model . config('i18n.tables.model_translations_suffix', '_i18n'));
    }
}
