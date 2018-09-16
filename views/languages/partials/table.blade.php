@extends('i18n::layout.partials.table.table')

@if(!isset($id))
    @php($id = generateRandomString(10))
@endif

@section('table-head-' . $id)
    <tr>
        <th>Name</th>
        <th>ISO_639_1</th>
        <th>Status</th>
        <td>Progress</td>
        <th>Translations</th>
    </tr>
@endsection

@section('table-body-' . $id)
    @foreach($languages as $language)
        <tr>
            <td>{{ $language->name }}</td>
            <td>{{ $language->ISO_639_1 }}</td>
            <td>
                @if($language->enabled)
                    <button type="button" class="btn btn-sm btn-success" @ajaxmodal
                            data-ajax-url="{{ route('i18n.languages.disable.dialog', compact('language')) }}">
                        <i class="fe fe-check mr-2"></i>Enabled
                    </button>
                @else
                    <button type="button" class="btn btn-sm btn-warning">
                        <i class="fe fe-x mr-2"></i>Disabled
                    </button>
                @endif
            </td>
            <td>
                <div class="clearfix">
                    <div class="float-left">
                        <strong>42%</strong>
                    </div>
                    <div class="float-right">
                        <small class="text-muted">Jun 11, 2015 - Jul 10, 2015</small>
                    </div>
                </div>
                <div class="progress progress-xs">
                    <div class="progress-bar bg-yellow" role="progressbar" style="width: 42%" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </td>
            <td>
                <a href="{{ route('i18n.languages.translations', compact('language')) }}">
                    <i class="fe fe-list"></i> Translations
                </a>
            </td>
        </tr>
    @endforeach
@endsection
