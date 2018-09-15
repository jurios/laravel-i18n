<?php

namespace Kodilab\LaravelI18n\Controllers;

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
    public function index(Language $language)
    {
        $base_language = Language::getBaseLanguage();

        $texts = Translation::getLanguageTranslations($base_language);

        //return view('activities.index', compact('language', 'base_language', 'texts'));
    }

    public function update(Request $request, Language $language, string $md5)
    {
        $text = $request->input('text');
        $needs_revision = $request->has('needs_revision');

        $translation = $language->setTranslation($md5, $text, $needs_revision);

        return response()->json($translation);
    }
}
