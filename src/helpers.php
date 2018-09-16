<?php

function t(string $text, $replace = [], $locale = null, $honestly = false)
{
    return app(\Kodilab\LaravelI18n\I18n::class)->translate($text, $replace, $locale, $honestly);
}