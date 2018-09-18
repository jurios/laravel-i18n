@extends('i18n::layout.partials.modals.ajax_dialog.ajax_modal')

@php($id = generateRandomString())

@php($form = [
    "action" => route('i18n.languages.disable', compact('language')),
    'method' => 'PATCH'
])

@section('title')
    Disable <b>{{ $language->name }}</b>
@endsection

@section('content')
    <p>
        Are you sure you want to disable <b>{{ $language->name }}</b> language?
    </p>
    <p>
        If you disable a language, you will be able to add or edit translations. However, this language won't be able to
        be used in your website.
    </p>
@endsection

@section('buttons')
    <button type="submit" class="btn btn-danger">
        <i class="fe fe-x"></i> {{ t('Disable') }}
    </button>
    <a href="javascript:;" type="button" class="btn btn-success" data-dismiss="modal">
        {{ t('Cancelar') }}
    </a>
@endsection
