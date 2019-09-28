<?php


namespace Kodilab\LaravelI18n\Models\Traits;


use Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException;
use Kodilab\LaravelI18n\Models\Locale;

trait HelperMethods
{
    /**
     * Generate the locale reference based on language and region attributes
     *
     * @param string $language
     * @param string|null $region
     * @return string
     */
    public static function generateReference(string $language, string $region = null)
    {
        $language = mb_strtolower(trim($language));
        $region = !is_null($region) ? mb_strtoupper(trim($region)) : null;

        return is_null($region) ? $language : "{$language}_{$region}";
    }

    /**
     * Returns the language from a locale reference
     *
     * @param string $reference
     * @return mixed
     */
    public static function getLanguage(string $reference)
    {
        if (!self::isReferenceValid($reference)) {
            throw new \InvalidArgumentException('Name ' . $reference . ' is not a valid locale reference');
        }

        return explode('_', $reference)[0];
    }

    /**
     * Returns the region from the locale reference or null
     *
     * @param string $reference
     * @return string|null
     */
    public static function getRegion(string $reference)
    {
        if (!self::isReferenceValid($reference)) {
            throw new \InvalidArgumentException('Name ' . $reference . ' is not a valid locale reference');
        }

        $exploded = explode('_', $reference);

        return isset($exploded[1]) ? $exploded[1] : null;
    }

    /**
     * Returns whether a reference is valid
     *
     * @param string $reference
     * @return bool
     */
    public static function isReferenceValid(string $reference)
    {
        return (bool)preg_match('/^[a-z]{2,3}(_[A-Z]{2,3})?$/', $reference);
    }

    /**
     * Returns whether a timezone is valid
     *
     * @param string $timezone
     * @return bool
     */
    public static function isTimezoneValid(string $timezone)
    {
        $occurrence = array_filter(timezone_identifiers_list(), function ($item) use ($timezone) {
            return $item === $timezone;
        });

        return count($occurrence) > 0;
    }

    /**
     * Get the fallback locale. It does not exits, then an exception is sent.
     *
     * @return Locale
     * @throws MissingFallbackLocaleException
     */
    public static function getFallbackLocale()
    {
        /** @var Locale $fallback_locale */
        $fallback_locale = self::where('fallback', true)->get()->first();

        if (is_null($fallback_locale)) {
            throw new MissingFallbackLocaleException('Fallback locale not found.');
        }

        return $fallback_locale;
    }

    /**
     * Returns a locale by reference. If it does not exist, then null is returned.
     *
     * @param string $reference
     * @return mixed
     */
    public static function getLocale(string $reference)
    {
        $language = explode("_", $reference)[0];
        $region = isset(($splitted = explode("_", $reference))[1]) ? $splitted[1] : null;

        return self::where('language', $language)->where('region', $region)->first();
    }

    /**
     * Returns a locale by reference. If it does not exist, then fallback locale is returned
     *
     * @param string $reference
     * @return Locale
     * @throws MissingFallbackLocaleException
     */
    public static function getLocaleOrFallback(string $reference)
    {
        if (!is_null($locale = self::getLocale($reference))) {
            return $locale;
        }

        return self::getFallbackLocale();
    }
}