@extends('i18n::layout.base')

@section('title')
    {{ $locale->reference }} translations
@endsection

@section('subtitle')
    {{ count($locale->translations) }} lines
@endsection

@section('options')
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <div class="col-lg-4 offset-lg-4 mb-5">
                @include('i18n::locales.partials.progress_bar')
            </div>
        </div>
    </div>

    @include('i18n::translations.partials.table')

@endsection