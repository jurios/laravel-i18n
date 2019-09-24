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

if (!function_exists('currency')) {
    function currency(float $value, bool $show_symbol = true, \Kodilab\LaravelI18n\Models\Locale $locale = null)
    {
        $result = "";

        if (is_null($locale)) {
            $locale = \Kodilab\LaravelI18n\Models\Locale::getLocale(config('app.locale'));

            if (is_null($locale)) {
                $locale = \Kodilab\LaravelI18n\Models\Locale::getFallbackLocale();
            }
        }

        if ($show_symbol && $locale->currency_symbol_position === 'before') {
            $result = $locale->currency_symbol . ' ';
        }

        $result = $result . number_format(
            $value,
            !is_null($locale->decimals) ? $locale->decimals : 0,
            !is_null($locale->decimals_punctuation) ? $locale->decimals_punctuation : '',
            !is_null($locale->thousands_separator) ? $locale->thousands_separator : ''
        );

        if ($show_symbol && $locale->currency_symbol_position === 'after') {
            $result = $result . ' ' . $locale->currency_symbol;
        }

        return $result;
    }
}