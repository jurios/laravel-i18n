<?php

if (!function_exists('getQueryString')) {
    function getQueryString(string $query, $default = null)
    {
        return \Illuminate\Support\Facades\Request::input($query, $default);
    }
}
if (!function_exists('hasQueryString')) {
    function hasQueryString(string $query)
    {
        return \Illuminate\Support\Facades\Request::has($query);
    }
}
if (!function_exists('filledQueryString')) {
    function filledQueryString(string $query)
    {
        return \Illuminate\Support\Facades\Request::filled($query);
    }
}

if (!function_exists('__number')) {
    function __number(float $value, \Kodilab\LaravelI18n\Models\Locale $locale = null)
    {
        if (is_null($locale)) {
            $locale = \Kodilab\LaravelI18n\Models\Locale::getLocale(config('app.locale'));

            if (is_null($locale)) {
                $locale = \Kodilab\LaravelI18n\Models\Locale::getFallbackLocale();
            }
        }

        return number_format(
            $value,
            !is_null($locale->decimals) ? $locale->decimals : 0,
            !is_null($locale->decimals_punctuation) ? $locale->decimals_punctuation : '',
            !is_null($locale->thousands_separator) ? $locale->thousands_separator : ''
        );
    }
}

if (!function_exists('__price')) {
    function __price(float $value, \Kodilab\LaravelI18n\Models\Locale $locale = null)
    {
        if (is_null($locale)) {
            $locale = \Kodilab\LaravelI18n\Models\Locale::getLocale(config('app.locale'));

            if (is_null($locale)) {
                $locale = \Kodilab\LaravelI18n\Models\Locale::getFallbackLocale();
            }
        }

        $value = __number($value, $locale);

        if (is_null($locale->currency_symbol)) {
            return $value;
        }

        if ($locale->currency_symbol_position === 'before') {
            return $locale->currency_symbol . ' ' . $value;
        }

        return $value . ' ' . $locale->currency_symbol;
    }
}