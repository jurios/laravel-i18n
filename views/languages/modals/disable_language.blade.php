@extends('i18n::layout.partials.modals.ajax_dialog.ajax_modal')

@php($id = generateRandomString())

@php($form = [
    "action" => route('i18n.languages.disable', ['language' => $language, '_callback' => \Illuminate\Support\Facades\Request::input('_mcallback')]),
    'method' => 'PATCH'
])

@section('title')
    Disable <b>{{ $language->name }}</b>
@endsection

@section('content')
    @if($language->isFallbackLanguage())
        <div class="alert alert-icon alert-info" role="alert">
            <i class="fe fe-info mr-2" aria-hidden="true"></i>
            You can't disable a fallback language.
        </div>
    @else
        <p>
            Are you sure you want to disable <b>{{ $language->name }}</b> language?
        </p>
        <p>
            If you disable a language, you won't be able to add or update translations. However we keep your progress safe
            in order to resume it if you enable this language again.
        </p>
    @endif
@endsection

@section('buttons')
    @if($language->isFallbackLanguage())
        <a href="javascript:;" type="button" class="btn btn-success" data-dismiss="modal">
            {{ t('Aceptar') }}
        </a>
    @else
        <button type="submit" class="btn btn-warning">
            <i class="fe fe-x"></i> {{ t('Disable') }}
        </button>
        <a href="javascript:;" type="button" class="btn btn-success" data-dismiss="modal">
            {{ t('Cancelar') }}
        </a>
    @endif
@endsection
