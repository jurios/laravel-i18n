@extends('i18n::layout.partials.modals.ajax_dialog.ajax_modal')

@php($id = generateRandomString())

@php($form = [
    "action" => route('i18n.locales.destroy', ['locale' => $locale]),
    'method' => 'DELETE'
])

@section('title')
    Delete <b>{{ $locale->reference }}</b>
@endsection

@section('content')
    @if($locale->isFallbackLocale())
        <div class="alert alert-icon alert-warning" role="alert">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            You can't delete a fallback locale.
        </div>
    @else
        <p>
            Are you sure you want to delete <b>{{ $locale->reference }}</b> locale?
        </p>
        <p>
            If you delete a locale, all locale translations will be <b>deleted</b>.
        </p>
    @endif
@endsection

@section('buttons')
    @if($locale->isFallbackLocale())
        <a href="javascript:;" class="btn btn-primary" data-dismiss="modal">
            Accept
        </a>
    @else
        <button type="submit" class="btn btn-danger">
            <i class="fe fe-x"></i> Delete
        </button>
        <a href="javascript:;" class="btn btn-warning" data-dismiss="modal">
            Cancel
        </a>
    @endif
@endsection
