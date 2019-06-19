<?php

if (!function_exists('exportToPlainTranslationArray')) {
    function exportToPlainTranslationArray($index, array $translations) {

        if (!function_exists('recursive_transform')) {
            function recursive_transform($index, array $translations, &$results)
            {

                foreach ($translations as $original => $translation) {

                    $i = is_null($index) ? $original : $index . '.' . $original;

                    if (!is_array($translation)) {
                        $results[$i] = $translation;
                    }

                    if (is_array($translation)) {
                        recursive_transform($i, $translation, $results);
                    }
                }
            }
        }

        $result = [];
        recursive_transform($index, $translations, $result);

        return $result;
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