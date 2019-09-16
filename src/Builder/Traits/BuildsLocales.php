<?php


namespace Kodilab\LaravelI18n\Builder\Traits;


use Illuminate\Support\Facades\DB;
use Kodilab\LaravelI18n\Exceptions\LocaleAlreadyExists;
use Kodilab\LaravelI18n\Support\Arr;

trait BuildsLocales
{
    public static function getLocaleTable()
    {
        return config('i18n.tables.locales');
    }

    /**
     * Creates a locale
     *
     * @param array $data
     * @return mixed
     */
    public static function createLocale(array $data = [])
    {
        if (Arr::get($data, 'fallback', false)) {
            $previous_fallback = DB::table(self::getLocaleTable())->where('fallback', true)->get()->first();
        }

        $existing_locale = DB::table(self::getLocaleTable())
            ->where('iso', Arr::get($data, 'iso', null))
            ->where('region', Arr::get($data, 'region', null))
            ->get()->first();

        if (!is_null($existing_locale)) {
            throw new LocaleAlreadyExists($existing_locale->iso, $existing_locale->region);
        }

        DB::table(self::getLocaleTable())->insert($data);

        if (isset($previous_fallback)) {
            DB::table(self::getLocaleTable())->where('id', $previous_fallback->id)->update(['fallback' => false]);
        }
    }

    /**
     * Removes a locale
     *
     * @param string $name
     */
    public static function removeLocale(string $name)
    {
        $iso = explode("_", $name)[0];
        $region = isset(($splitted = explode("_", $name))[1]) ? $splitted[1] : null;

        $locale = DB::table(config('i18n.tables.locales'))
            ->where('iso', $iso)->where('region', $region)->get()->first();

        if ($locale->fallback) {
            throw new \RuntimeException('Fallback locale can not be removed');
        }

        DB::table(config('i18n.tables.locales'))->delete($locale->id);
    }
}