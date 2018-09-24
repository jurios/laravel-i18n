<?php

namespace Kodilab\LaravelI18n\Controllers;

use Kodilab\LaravelI18n\Filters\CompareTranslationFilter;
use Kodilab\LaravelI18n\Filters\TranslationFilter;
use Kodilab\LaravelI18n\Language;
use Kodilab\LaravelI18n\Text;
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
        /** @var Language $fallback_language */
        $fallback_language = Language::getFallbackLanguage();

        $filters->setTranslatedLanguage($language);

        $lines = $fallback_language->translations()->filters($filters)->results($filters);

        return view('i18n::translations.index', compact('language', 'fallback_language', 'lines', 'filters'));
    }

    public function update(Request $request, Language $language, string $md5)
    {
        $translation_text = $request->input('translation');
        $needs_revision = $request->input('needs_revision') === 'true' ? true: false;

        $translation = $language->translations()->where('md5', $md5)->first();
        $fallback_translation = Language::getFallbackLanguage()->translations()->where('md5', $md5)->first();

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
                'language_id' => $language->id,
                'text_id' => $fallback_translation->text_id
            ]);
        }

        $language->refresh();

        return response()->json([
            'line' => $translation,
            'progress_bar_html' => view('i18n::languages.partials.progress_bar', ['language' => $language])->render()
        ]);
    }

    public function info(Request $request, Language $language, string $md5)
    {
        $text = Text::where('md5', $md5)->first();

        return view('i18n::translations.modals.info', compact('md5', 'text'));
    }
}
