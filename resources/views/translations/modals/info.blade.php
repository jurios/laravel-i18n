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
            <label class="form-label">Original text (from template)</label>
            <div class="form-control">
                {{ $text->text }}
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Files</label>
            <div class="form-control" style="height: inherit">
                <ul style="list-style: none; padding: 0;">
                @foreach($text->paths as $path => $occurrences)
                        <li><span class="badge badge-info" title="{{$occurrences}} occurrences">{{$occurrences}}</span> - {{ $path }}</li>
                @endforeach
                </ul>
            </div>
        </div>
    @endif
@endsection

@section('buttons')
    <a href="javascript:;" class="btn btn-info" data-dismiss="modal">
        {{ 'Accept' }}
    </a>
@endsection
