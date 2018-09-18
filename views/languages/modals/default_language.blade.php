@extends('i18n::layout.partials.modals.ajax_dialog.ajax_modal')

@php($id = generateRandomString())

@php($form = [
    "action" => route('i18n.languages.default', compact('language')),
    'method' => 'PATCH'
])

@section('title')
    Mark <b>{{ $language->name }}</b> as default
@endsection

@section('content')
    <p>
        Are you sure you want mark <b>{{ $language->name }}</b> as your default language?
    </p>
    <p>
        Every call to translate an untranslated text for a given language using <i> non honestly mode </i>
        will be translated to {{ $language->name }} as  fallback instead of the previous one
        ({{ \Kodilab\LaravelI18n\Language::getDefaultLanguage()->name }})
    </p>
@endsection

@section('buttons')
    <button type="submit" class="btn btn-warning">
        <i class="fe fe-x"></i> {{ t('Mark as default') }}
    </button>
    <a href="javascript:;" type="button" class="btn btn-danger" data-dismiss="modal">
        {{ t('Cancelar') }}
    </a>
@endsection
