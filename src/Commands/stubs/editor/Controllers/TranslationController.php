<?php

namespace {{namespace}}Http\Controllers\i18n;

class TranslationController extends I18nController
{
    public function index(\Illuminate\Http\Request $request, \Kodilab\LaravelI18n\Models\Locale $locale)
    {
        $fallback_locale = \Kodilab\LaravelI18n\Models\Locale::getFallbackLocale();

        $results = $locale->translations->filters(\Kodilab\LaravelI18n\Filters\Translations\TranslationFilters::class, $request->all());

        $translations = $this->getPaginatedTranslations($request, $results, 10);
        $translations->withPath(route('i18n.locales.translations.index', compact('locale')));

        return view(self::VIEW_PATH . '.editor.locales.translations.index',
            compact('locale', 'fallback_locale', 'translations')
        );
    }

    public function update(\Illuminate\Http\Request $request, \Kodilab\LaravelI18n\Models\Locale $locale)
    {
        //TODO: Add request validation

        $original = $request->input('original');
        $translation = $request->input('translation');

        $locale->updateTranslation($original, $translation);

        $translation = $locale->translation($original);

        return response()->json([
            'line' => $translation->translation,
            'percentage' => 0
        ]);
    }

    /**
     * Returns a paginated translations
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Support\Collection $translations
     * @param int $per_page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function getPaginatedTranslations(
        \Illuminate\Http\Request $request, \Illuminate\Support\Collection $translations, int $per_page = 10)
    {
        $currentPage = $request->filled('page') ? $request->input('page') : 0;
        $per_page = 10;

        $result = new \Illuminate\Pagination\LengthAwarePaginator(
            $translations->slice($currentPage * $per_page, $per_page)->all(),
            count($translations),
            $per_page
        );

        return $result;
    }
}