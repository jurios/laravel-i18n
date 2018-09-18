<?php

function t(string $text, $replace = [], $locale = null, $honestly = false)
{
    return app(\Kodilab\LaravelI18n\I18n::class)->translate($text, $replace, $locale, $honestly);
}

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

function addClassIfRoutMatch(string $route, $class_name = 'active')
{
    $paths = array();
    if (!is_array($route)) {
        array_push($paths, $route);
    } else {
        $paths = $route;
    }
    foreach ($paths as $path){
        if (substr(Route::currentRouteName(), 0, strlen($path)) === $path) {
            return $class_name;
        }
    }
    return '';
}