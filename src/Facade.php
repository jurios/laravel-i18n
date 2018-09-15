<?php


namespace Kodilab\LaravelI18n;


/**
 * @method static mixed routes()
 *
 */
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
    public static function routes(array $options = [])
    {
        static::$app->make('router')->post('login_i18n', 'Auth\LoginController@loginWithCallback')->name('login');
    }
}
