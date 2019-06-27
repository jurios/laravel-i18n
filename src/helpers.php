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

if (!function_exists('exportTranslationCollectionToRaw')) {
    function exportTranslationCollectionToRaw(\Illuminate\Support\Collection $translations)
    {
        $raw = [];

        /** @var Translation $translation */
        foreach ($translations as $translation) {
            $raw[$translation->original] = $translation->translation;
        }

        return $raw;
    }
}

if (!function_exists('exportArrayToFile')) {
    function exportArrayToString(array $array)
    {
        $content = "<?php". PHP_EOL . PHP_EOL . "return [" . PHP_EOL;
        $content = $content . printArray($array, 1);
        $content = $content . PHP_EOL .PHP_EOL . "];";
        return $content;
    }
}

if (!function_exists('printArray')) {
    function printArray(array $array, $level)
    {
        $content = '';
        for ($i = 0; $i < count($array); $i++) {
            $key = array_keys($array)[$i];
            if (is_array($array[$key])) {

                $content = $content . printTab($level + 1);
                $content = $content . "'" . $key . "'" . $this->printTab(1) . "=> [" . PHP_EOL;
                $content = $content . printArray($array[$key], $level + 1);
                $content = $content . printTab($level + 1) . "], \n";

            } else {

                $content = $content . printItem($key, $array[$key], $level + 1);
            }
        }
        return $content;
    }
}

if (!function_exists('printTab')) {
    function printTab($times)
    {
        $content = "";
        for ($i = 0; $i < $times; $i++) {
            $content = $content . "    ";
        }
        return $content;
    }
}

if (!function_exists('printItem')) {
    function printItem($key, $value, $level)
    {
        $content = printTab($level);
        $content = $content . "'" . $key . "'" . printTab(1) . "=> '" . $value . "'," . PHP_EOL;
        return $content;
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
            !is_null($locale->currency_number_decimals) ? $locale->currency_number_decimals : 0,
            !is_null($locale->currency_decimals_punctuation) ? $locale->currency_decimals_punctuation : '',
            !is_null($locale->currency_thousands_separator) ? $locale->currency_thousands_separator : ''
        );

        if ($show_symbol && $locale->currency_symbol_position === 'after') {
            $result = $result . ' ' . $locale->currency_symbol;
        }

        return $result;
    }
}