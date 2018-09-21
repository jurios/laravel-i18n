<?php

namespace Kodilab\LaravelI18n\Controllers;

use Kodilab\LaravelI18n\Filters\CompareTranslationFilter;
use Kodilab\LaravelI18n\Filters\TranslationFilter;
use Kodilab\LaravelI18n\Language;
use Kodilab\LaravelI18n\Translation;
use Illuminate\Http\Request;

class I18nTranslationsController extends \Illuminate\Routing\Controller
{
    /**
     * Display a listing of the language's translations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TranslationFilter $filters, Language $language)
    {
        $base_language_filters = clone $filters;
        $language_filters = clone $filters;

        /** @var Language $base_language */
        $base_language = Language::getBaseLanguage();

        $base_language_translations = $base_language->translations()->filters($base_language_filters)->get()->pluck('id')->toArray();

        $language_translations = $language->translations()->filters($language_filters)->get()->pluck('id')->toArray();

        $translatios_ids = array_unique(array_merge($base_language_translations, $language_translations), SORT_REGULAR);

        $lines = Translation::whereIn('id', $translatios_ids)->results($filters, true);

        return view('i18n::translations.index', compact('language', 'base_language', 'lines', 'filters'));
    }

    public function update(Request $request, Language $language, string $md5)
    {
        $text = $request->input('text');
        $needs_revision = $request->input('needs_revision') === 'true' ? true: false;

        $translation = $language->translations()->where('md5', $md5)->first();

        if (!is_null($translation))
        {
            $translation->update([
                'text' => $text,
                'needs_revision' => $needs_revision
            ]);

        } else {
            $translation = Translation::create([
                'md5' => $md5,
                'text' => $text,
                'needs_revision' => $needs_revision,
                'language_id' => $language->id
            ]);
        }

        $language->refresh();

        return response()->json([
            'line' => $translation,
            'progress_bar_html' => view('i18n::languages.partials.progress_bar', ['language' => $language])->render()
        ]);
    }
}
