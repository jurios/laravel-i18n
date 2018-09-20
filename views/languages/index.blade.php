@extends('i18n::layout.base')

@section('title')
    Languages
@endsection

@section('subtitle')
    {{ count(\Kodilab\LaravelI18n\Language::all()) }} languages
@endsection

@section('options')
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
                <div class="alert alert-icon alert-primary" role="alert">
                    <i class="fe fe-bell mr-2" aria-hidden="true"></i>
                    This list only shows <b>enabled</b> languages. If you want to add more languages, you can do it in
                    your <b><i class="fe fe-settings"></i> settings </b>.
                </div>
                @include('i18n::languages.partials.table')
            </div>
        </div>
    </div>
@endsection