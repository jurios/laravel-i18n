@extends('i18n::layout.base')

@section('title')
    {{ $language->name }} translations
@endsection

@section('subtitle')
    {{ count($language->translations) }} lines
@endsection

@section('options')
@endsection

@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Translations list</h3>
                <div class="card-options">
                </div>
            </div>
            <div class="card-body">
                @include('i18n::translations.partials.table')
            </div>
        </div>
    </div>
@endsection