@extends('i18n::layout.base')

@section('title')
    Locales
@endsection

@section('subtitle')
    {{ count(\Kodilab\LaravelI18n\Models\Locale::all()) }} languages
@endsection

@section('options')
@endsection

@section('alerts')
    @if(! \Kodilab\LaravelI18n\Models\Text::hasBeenSyncronized())
        <div class="alert alert-icon alert-warning" role="alert">
            <i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i>
            Looks like you didn't <b>syncronized</b> your translations statically.
            It's not necessary but it's a recommendable way to get all your translatable lines with <code>php artisan i18n:sync</code>.
        </div>
    @endif
@endsection

@section('content')
    @include('i18n::locales.partials.table')
@endsection