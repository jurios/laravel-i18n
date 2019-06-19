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

    public function update(\Illuminate\Http\Request $request, \Kodilab\LaravelI18n\Models\Locale $locale)
    {
        //TODO: Add request validation

        $original = $request->input('original');
        $translation = $request->input('translation');

        $manager = new \Kodilab\LaravelI18n\Translations\TranslationsManager($locale);
        $manager->update($original, $translation);

        $translation = $manager->find($original);

        return response()->json([
            'line' => $translation->translation,
            'percentage' => $manager->percentage
        ]);
    }
}