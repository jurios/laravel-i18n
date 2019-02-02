@extends('i18n::layout.partials.modals.ajax_dialog.ajax_modal')

@php($id = generateRandomString())

@php($form = [
    "action" => route('i18n.locales.enable', ['locale' => $locale, '_callback' => \Illuminate\Support\Facades\Request::input('from')]),
    'method' => 'PATCH'
])

@section('title')
    Enable <b>{{ $locale->reference }}</b>
@endsection

@section('content')
    <p>
        Are you sure you want to enable <b>{{ $locale->reference }}</b> locale?
    </p>
    <p>
        This locale will be available on your website.
    </p>
@endsection

@section('buttons')
    <button type="submit" class="btn btn-primary">
        <i class="fe fe-check"></i> Enable
    </button>
    <a href="javascript:;" class="btn btn-warning" data-dismiss="modal">
        Cancel
    </a>
@endsection
