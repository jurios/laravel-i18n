@extends('i18n::layout.partials.modals.ajax_dialog.ajax_modal')

@php($id = generateRandomString())

@php($form = [
    "action" => route('i18n.languages.enable', compact('language')),
    'method' => 'PATCH'
])

@section('title')
    Enable <b>{{ $language->name }}</b>
@endsection

@section('content')
    <p>
        Are you sure you want to enable <b>{{ $language->name }}</b> language?
    </p>
    <p>
        This language will be visible for your website's users and could be chosen as a language to translate your website.
    </p>
@endsection

@section('buttons')
    <button type="submit" class="btn btn-success">
        <i class="fe fe-check"></i> {{ t('Enable') }}
    </button>
    <a href="javascript:;" type="button" class="btn btn-danger" data-dismiss="modal">
        {{ t('Cancelar') }}
    </a>
@endsection
