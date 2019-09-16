<?php


namespace Kodilab\LaravelI18n;


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
}