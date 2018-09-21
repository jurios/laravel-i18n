@extends('i18n::layout.base')

@section('title')
    Languages
@endsection

@section('subtitle')
    {{ count(\Kodilab\LaravelI18n\Language::all()) }} languages
@endsection

@section('options')
@endsection

@section('alerts')
    @if(!\Kodilab\LaravelI18n\Text::hasBeenSyncronized())
        <div class="alert alert-icon alert-warning" role="alert">
            <i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i>
            Looks like you didn't <b>syncronized</b> your translations statically.
            It's not necessary but it's a recommendable way to get all your translatable lines with <code>php artisan i18n:sync</code>.
        </div>
    @endif
@endsection

@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Languages list</h3>
                <div class="card-options">
                </div>
            </div>
            <div class="card-body">
                @include('i18n::settings.languages.partials.table')
            </div>
        </div>
    </div>
@endsection