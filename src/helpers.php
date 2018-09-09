<?php

function t(string $text, $replace = [], $locale = null, $honestly = false)
{
    return app(\Kodilab\LaravelI18n\I18n::class)->getTranslationFromText($text, $replace, $locale, $honestly);
}