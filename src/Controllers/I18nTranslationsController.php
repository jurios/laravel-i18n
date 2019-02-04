<?php

namespace Kodilab\LaravelI18n\Controllers;

use Kodilab\LaravelI18n\Filters\TranslationFilter;
use Kodilab\LaravelI18n\Models\Locale;
use Illuminate\Http\Request;
use Kodilab\LaravelI18n\Models\Translation;

class I18nTranslationsController extends I18nController
{
    /**
     * Display a listing of the language's translations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TranslationFilter $filters, Locale $locale)
    {
        /** @var Locale $fallback_locale */
        $fallback_locale = Locale::getFallbackLocale();

        $filters->setTranslatedLocale($locale);

        $lines = $fallback_locale->translations()->filters($filters)->results($filters);

        return view('i18n::translations.index', compact('locale', 'fallback_locale', 'lines', 'filters'));
    }

    public function update(Request $request, Locale $locale, string $md5)
    {
        $translation_text = $request->input('translation');
        $needs_revision = $request->input('needs_revision') === 'true' ? true: false;

        $translation = $locale->translations()->where('md5', $md5)->first();
        $fallback_translation = Locale::getFallbackLocale()->translations()->where('md5', $md5)->first();

        if (!is_null($translation))
        {
            $translation->update([
                'translation' => $translation_text,
                'needs_revision' => $needs_revision,
                'text_id' => $fallback_translation->text_id
            ]);

        } else {
            $translation = Translation::create([
                'md5' => $md5,
                'translation' => $translation_text,
                'needs_revision' => $needs_revision,
                'locale_id' => $locale->id,
                'text_id' => $fallback_translation->text_id
            ]);
        }

        $locale->refresh();

        return response()->json([
            'line' => $translation,
            'progress_bar_html' => view('i18n::locales.partials.progress_bar', ['locale' => $locale])->render()
        ]);
    }

    public function info(Request $request, Locale $locale, string $md5)
    {
        $text = Text::where('md5', $md5)->first();

        return view('i18n::translations.modals.info', compact('md5', 'text'));
    }
}
