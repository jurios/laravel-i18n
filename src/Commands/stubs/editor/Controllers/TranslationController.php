<?php

namespace {{namespace}}Http\Controllers\i18n;

class TranslationController extends I18nController
{
    public function index(\Kodilab\LaravelI18n\Models\Locale $locale)
    {
        $fallback = \Kodilab\LaravelI18n\Models\Locale::getFallbackLocale();

        return view(self::VIEW_PATH . '.editor.locales.translations.index',
            compact('locale' , 'fallback')
        );
    }
}