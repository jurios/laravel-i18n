<?php


namespace Kodilab\LaravelI18n\Builder\Traits;


use Illuminate\Support\Facades\DB;
use Kodilab\LaravelI18n\Exceptions\LocaleAlreadyExists;
use Kodilab\LaravelI18n\i18n\i18n;
use Kodilab\LaravelI18n\Support\Arr;

trait BuildsLocales
{
    public static function getLocaleTable()
    {
        return config('i18n.tables.locales', 'locales');
    }

    /**
     * Creates a locale
     *
     * @param array $data
     * @throws LocaleAlreadyExists
     */
    public static function createLocale(array $data = [])
    {
        $existing_locale = DB::table(self::getLocaleTable())
            ->where('language', Arr::get($data, 'language', null))
            ->where('region', Arr::get($data, 'region', null))
            ->get()->first();

        if (!is_null($existing_locale)) {
            throw new LocaleAlreadyExists($existing_locale->language, $existing_locale->region);
        }

        DB::table(self::getLocaleTable())->insert($data);
    }

    /**
     * Removes a locale
     *
     * @param string $reference
     */
    public static function removeLocale(string $reference)
    {
        $language = explode("_", $reference)[0];
        $region = isset(($splitted = explode("_", $reference))[1]) ? $splitted[1] : null;

        $locale = DB::table(config('i18n.tables.locales', 'locales'))
            ->where('language', $language)->where('region', $region)->get()->first();

        if (i18n::generateReference($locale->language, $locale->region) === config('app.fallback_locale')) {
            throw new \RuntimeException('Fallback locale can not be removed');
        }

        DB::table(config('i18n.tables.locales', 'locales'))->delete($locale->id);
    }
}