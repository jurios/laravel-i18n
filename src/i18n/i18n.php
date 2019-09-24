<?php


namespace Kodilab\LaravelI18n\i18n;


class i18n
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
     * @return |null
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
}