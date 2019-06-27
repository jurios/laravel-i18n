<?php


namespace Kodilab\LaravelI18n;


use Kodilab\LaravelI18n\Models\Locale;

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

    public static function createLocale(array $data = [])
    {
        return factory(Locale::class)->create([
            'iso'                               => isset($data['iso']) ? $data['iso'] : null,
            'region'                            => isset($data['region']) ? $data['region'] : null,
            'description'                       => isset($data['description']) ? $data['description'] : null,
            'laravel_locale'                    => isset($data['laravel_locale']) ? $data['laravel_locale'] : null,
            'currency_number_decimals'          => isset($data['currency_number_decimals']) ? $data['currency_number_decimals'] : null,
            'currency_decimals_punctuation'     => isset($data['currency_decimals_punctuation']) ? $data['currency_decimals_punctuation'] : null,
            'currency_thousands_separator'      => isset($data['currency_thousands_separator']) ? $data['currency_thousands_separator'] : null,
            'currency_symbol'                   => isset($data['currency_symbol']) ? $data['currency_symbol'] : null,
            'currency_symbol_position'          => isset($data['currency_symbol_position']) ? $data['currency_symbol_position'] : 'after',
            'carbon_locale'                     => isset($data['carbon_locale']) ? $data['carbon_locale'] : null,
            'carbon_tz'                         => isset($data['carbon_tz']) ? $data['carbon_tz'] : null,
            'enabled'                           => isset($data['enabled']) ? $data['enabled'] : false,
            'fallback'                          => isset($data['fallback']) ? $data['fallback'] : false
        ]);
    }

    public static function removeLocale(string $reference)
    {
        /** @var Locale $locale */
        $locale = Locale::getLocale($reference);

        if ($locale->isFallback()) {
            throw new \Exception('Fallback locale can not be deleted');
        }

        $locale->delete();
    }
}