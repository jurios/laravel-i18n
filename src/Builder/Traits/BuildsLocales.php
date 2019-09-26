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
     * @throws \Exception
     */
    public static function createLocale(array $data = [])
    {
        DB::beginTransaction();

        try {
            if (isset($data['fallback']) && $data['fallback'] === true) {
                DB::table(self::getLocaleTable())->where('fallback', true)->update([
                    'fallback' => false
                ]);
            }

            $existing_locale = DB::table(self::getLocaleTable())
                ->where('language', Arr::get($data, 'language', null))
                ->where('region', Arr::get($data, 'region', null))
                ->get()->first();

            if (!is_null($existing_locale)) {
                throw new LocaleAlreadyExists($existing_locale->language, $existing_locale->region);
            }

            DB::table(self::getLocaleTable())->insert($data);

        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }

        DB::commit();
    }

    /**
     * Removes a locale
     *
     * @param string $reference
     */
    public static function removeLocale(string $reference)
    {
        $language = i18n::getLanguage($reference);
        $region = i18n::getRegion($reference);

        $locale = DB::table(config('i18n.tables.locales', 'locales'))
            ->where('language', $language)->where('region', $region)->get()->first();

        if ($locale->fallback) {
            throw new \RuntimeException('Fallback locale can not be removed');
        }

        DB::table(config('i18n.tables.locales', 'locales'))->delete($locale->id);
    }
}