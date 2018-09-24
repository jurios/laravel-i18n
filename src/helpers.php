<?php

function t(string $text, $replace = [], $locale = null, $honestly = false)
{
    return app(\Kodilab\LaravelI18n\I18n::class)->translate($text, $replace, $locale, $honestly);
}


if (!function_exists('generateRandomString')) {
    function generateRandomString(int $length = 15)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('addClassIfRouteMatch')) {
    function addClassIfRouteMatch(string $route, $class_name = 'active')
    {
        $paths = array();
        if (!is_array($route)) {
            array_push($paths, $route);
        } else {
            $paths = $route;
        }
        foreach ($paths as $path) {
            if (Route::currentRouteName() === $path) {
                return $class_name;
            }
        }
        return '';
    }
}

if (!function_exists('addClassIfRouteContains')) {
    function addClassIfRouteContains(string $route, $class_name = 'active')
    {
        $paths = array();
        if (!is_array($route)) {
            array_push($paths, $route);
        } else {
            $paths = $route;
        }
        foreach ($paths as $path) {
            if (substr(Route::currentRouteName(), 0, strlen($path)) === $path) {
                return $class_name;
            }
        }
        return '';
    }
}

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

