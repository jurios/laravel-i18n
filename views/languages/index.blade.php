@extends('i18n::layout.base')

@section('title')
    Languages
@endsection

@section('subtitle')
    {{ count(\Kodilab\LaravelI18n\Language::enabled()->get()) }} enabled
    of {{ count(\Kodilab\LaravelI18n\Language::all()) }} languages
@endsection

@section('options')
@endsection

@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Languages list</h3>
                <div class="card-options">
                    <form action="">
                        <div class="input-group">
                            <input class="form-control form-control-sm" placeholder="Search something..." name="s" type="text">
                            <span class="input-group-btn ml-2">
                            <button class="btn btn-sm btn-default" type="submit">
                              <span class="fe fe-search"></span>
                            </button>
                          </span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @include('i18n::languages.partials.table')
            </div>
        </div>
    </div>
@endsection