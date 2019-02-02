@extends('i18n::layout.partials.modals.ajax_dialog.ajax_modal')

@php($id = generateRandomString())

@php($form = [
    "action" => route('i18n.locales.disable', ['locale' => $locale, '_callback' => \Illuminate\Support\Facades\Request::input('from')]),
    'method' => 'PATCH'
])

@section('title')
    Disable <b>{{ $locale->reference }}</b>
@endsection

@section('content')
    @if($locale->isFallbackLocale())
        <div class="alert alert-icon alert-warning" role="alert">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            You can't disable a fallback locale.
        </div>
    @else
        <p>
            Are you sure you want to disable <b>{{ $locale->reference }}</b> locale?
        </p>
        <p>
            If you disable a locale, it can not be used on your webiste.
        </p>
    @endif
@endsection

@section('buttons')
    @if($locale->isFallbackLocale())
        <a href="javascript:;" class="btn btn-primary" data-dismiss="modal">
            Accept
        </a>
    @else
        <button type="submit" class="btn btn-primary">
            <i class="fe fe-x"></i> Disable
        </button>
        <a href="javascript:;" class="btn btn-warning" data-dismiss="modal">
            Cancelar
        </a>
    @endif
@endsection
