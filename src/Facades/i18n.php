<?php


namespace Kodilab\LaravelI18n\Facades;


use Illuminate\Support\Facades\Facade;
use Kodilab\LaravelI18n\Models\Locale;

/**
 * Class Facade
 * @package Kodilab\LaravelI18n
 *
 * @method static setLocale(Locale $locale)
 * @method static setFallbackLocale(Locale $locale)
 * @method static getLocale()
 * @method static getFallbackLocale()
 */
class i18n extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'i18n';
    }

    /**
     * @param string $language
     * @param string|null $region
     * @return string
     */
    public static function generateReference(string $language, string $region = null)
    {
        return Locale::generateReference($language, $region);
    }

    /**
     * @param string $reference
     * @return mixed
     */
    public static function getLanguage(string $reference)
    {
        return Locale::getLanguage($reference);
    }

    /**
     * @param string $reference
     * @return string|null
     */
    public static function getRegion(string $reference)
    {
        return Locale::getRegion($reference);
    }

    /**
     * @param string $reference
     * @return bool
     */
    public static function isReferenceValid(string $reference)
    {
        return Locale::isReferenceValid($reference);
    }

    /**
     * @param string $timezone
     * @return bool
     */
    public static function isTimezoneValid(string $timezone)
    {
        return Locale::isTimezoneValid($timezone);
    }
}