@extends('i18n::layout.base')

@section('title')
    {{ $locale->reference }}
@endsection

@section('options')
    <div class="btn-group" role="group" aria-label="Locale options">
        <a href="{{ route('i18n.locales.edit', compact('locale')) }}" class="btn btn-sm btn-primary">
            <i class="fa fa-edit"></i> Edit
        </a>
        <a href="javascript:;" @ajaxmodal class="btn btn-sm btn-danger"
           data-ajax-url="{{ route('i18n.locales.destroy.dialog', ['locale' => $locale]) }}">
            <i class="fa fa-trash"></i> Delete
        </a>
    </div>
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <label>
                                ISO 639-1
                            </label>
                            <div class="h3">
                                {{$locale->ISO_639_1 }}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label>
                                Region
                            </label>
                            <div class="h3">
                                {{ $locale->region }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>
                                Short Description
                            </label>
                            <div class="h3">
                                {{ $locale->description }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>
                                Dialect of
                            </label>
                            <div class="h3">
                                {{ $locale->dialect_of ? $locale->dialect_of->reference : '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <h5 class="card-header">Date & Time Locale Settings</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label>
                                Date & Time Locale
                            </label>
                            <div class="h4">
                                {{ $locale->carbon_locale }}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label>
                                Time zone
                            </label>
                            <div class="h4">
                                {{ $locale->carbon_tz }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <h5 class="card-header">Laravel locale</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label>
                                Laravel Locale
                            </label>
                            <div class="h4">
                                {{ $locale->laravel_locale }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <h5 class="card-header">Currency Locale Settings</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center">This is how you would see the currency value of one thousand on
                                your website
                            </div>
                            <div id="currency_status" class="text-center h3">{{ currency(1000, $locale, true) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <h5 class="card-header">Translations</h5>
                <div class="card-body">
                    <div class="row">
                        <div id="translation_progress" class="col-lg-8 offset-lg-2 mb-5">
                            @include('i18n::locales.partials.progress_bar')
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <b class="h4">{{count($locale->translations)}}</b> of
                            <b class="h4">{{count(\Kodilab\LaravelI18n\Models\Locale::getFallbackLocale()->translations)}}</b>
                            lines translated
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('i18n.locales.translations.index', compact('locale')) }}">
                                <i class="fe fe-list"></i> Translations
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection