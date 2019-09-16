<?php

namespace {{namespace}}Http\Controllers\i18n;

class LocaleController extends I18nController
{
    public function index(\Illuminate\Http\Request $request)
    {
        $locales = \Kodilab\LaravelI18n\Models\Locale::
            filters(\Kodilab\LaravelI18n\Filters\Locales\LocaleFilters::class, $request->all())
            ->paginate(10);

        return view(self::VIEW_PATH . '.editor.locales.index', compact('locales'));
    }

    public function show(\Kodilab\LaravelI18n\Models\Locale $locale)
    {
        return view(self::VIEW_PATH . '.editor.locales.show', compact('locale'));
    }

    public function create()
    {
        $locale = new \Kodilab\LaravelI18n\Models\Locale();

        return view(self::VIEW_PATH . '.editor.locales.form', compact('locale'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $locale = \Kodilab\LaravelI18n\Models\Locale::create([
            'iso' => $request->input('iso'),
            'region' => $request->input('region'),
            'description' => $request->input('description'),
            'enabled' => (bool)$request->input('enabled'),
            'laravel_locale' => $request->input('laravel_locale'),
            'currency_number_decimals' => $request->input('currency_number_decimals'),
            'currency_decimals_punctuation' => $request->input('currency_decimals_punctuation'),
            'currency_thousands_separator' => $request->input('currency_thousands_separator'),
            'currency_symbol' => $request->input('currency_symbol'),
            'currency_symbol_position' => $request->input('currency_symbol_position'),
            'carbon_locale' => $request->input('carbon_locale'),
            'tz' => $request->input('tz')
        ]);
        $request->session()->flash('status', [
            'level' => 'success',
            'message' => "Locale <b>" . $locale->name . "</b> created"
        ]);

        return redirect()->route('i18n.locales.show', compact('locale'));
    }

    public function edit(\Kodilab\LaravelI18n\Models\Locale $locale)
    {
        return view(self::VIEW_PATH . '.editor.locales.form', compact('locale'));
    }

    public function update(\Illuminate\Http\Request $request, \Kodilab\LaravelI18n\Models\Locale $locale)
    {
        $locale->update([
            'iso' => $request->input('iso'),
            'region' => $request->input('region'),
            'description' => $request->input('description'),
            'enabled' => (bool)$request->input('enabled'),
            'laravel_locale' => $request->input('laravel_locale'),
            'currency_number_decimals' => $request->input('currency_number_decimals'),
            'currency_decimals_punctuation' => $request->input('currency_decimals_punctuation'),
            'currency_thousands_separator' => $request->input('currency_thousands_separator'),
            'currency_symbol' => $request->input('currency_symbol'),
            'currency_symbol_position' => $request->input('currency_symbol_position'),
            'carbon_locale' => $request->input('carbon_locale'),
            'tz' => $request->input('tz')
        ]);
        $request->session()->flash('status', [
            'level' => 'success',
            'message' => "Locale <b>" . $locale->name . "</b> updated"
        ]);

        return redirect()->route('i18n.locales.show', compact('locale'));
    }

    public function destroy(\Illuminate\Http\Request $request, \Kodilab\LaravelI18n\Models\Locale $locale)
    {
        if (!$locale->isFallback()) {

            $locale->delete();
            $request->session()->flash('status', [
                'level' => 'success',
                'message' => "Locale <b>" . $locale->name . "</b> deleted"
            ]);
        }

        return redirect()->route('i18n.locales.index');

    }
}