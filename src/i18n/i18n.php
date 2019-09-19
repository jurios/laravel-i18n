<?php


namespace Kodilab\LaravelI18n\i18n;


class i18n
{
    /**
     * Generate the locale name based on iso and region attributes
     *
     * @param string $iso
     * @param string|null $region
     * @return string
     */
    public static function generateName(string $iso, string $region = null)
    {
        $iso = mb_strtolower(trim($iso));
        $region = !is_null($region) ? mb_strtoupper(trim($region)) : null;

        return is_null($region) ? $iso : "{$iso}_{$region}";
    }

    /**
     * Returns the iso from a locale name
     *
     * @param string $name
     * @return mixed
     */
    public static function getISO(string $name)
    {
        if (!self::isNameValid($name)) {
            throw new \InvalidArgumentException('Name ' . $name . ' is not a valid locale name');
        }

        return explode('_', $name)[0];
    }

    public static function getRegion(string $name)
    {
        if (!self::isNameValid($name)) {
            throw new \InvalidArgumentException('Name ' . $name . ' is not a valid locale name');
        }

        $exploded = explode('_', $name);

        return isset($exploded[1]) ? $exploded[1] : null;
    }

    /**
     * Returns whether a name is valid
     *
     * @param string $name
     * @return bool
     */
    public static function isNameValid(string $name)
    {
        return (bool)preg_match('/^[a-z]{2,3}(_[A-Z]{2,3})?$/', $name);
    }
}