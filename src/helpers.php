<?php

if (!function_exists('transformArrayTranslation')) {
    function transformArrayTranslation($index, array $translations, array &$results) {

        foreach ($translations as $original => $translation) {

            $index = is_null($index) ? $original : $index . '.' . $original;

            if (!is_array($translation)) {
                $results[$index] = $translation;
            }

            if (is_array($translation)) {
                transformArrayTranslation($index, $translation, $results);
            }
        }
    }
}