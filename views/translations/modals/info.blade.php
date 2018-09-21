@extends('i18n::layout.partials.modals.ajax_dialog.ajax_modal')

@php($id = generateRandomString())

@section('title')
    Translation {{ $md5 }}
@endsection

@section('content')
    @if(is_null($text))
        <div class="alert alert-icon alert-danger" role="alert">
            <i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i>
            We don't have static information about this translation yet. This could be because you didn't <b>syncronized</b>
            statically your translations with <code>php artisan i18n:sync</code> or because this translation doesn't exist.
        </div>
    @else
        <div class="form-group">
            <label class="form-label">Files</label>
            <div class="form-control">
                @foreach($text->paths as $path => $occurrences)
                    {{ $path }} : {{$occurrences}} occurrences <br>
                @endforeach
            </div>
        </div>
    @endif
@endsection

@section('buttons')
    <a href="javascript:;" type="button" class="btn btn-success" data-dismiss="modal">
        {{ t('Accept') }}
    </a>
@endsection
