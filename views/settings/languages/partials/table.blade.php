@extends('i18n::layout.partials.table.table')

@if(!isset($id))
    @php($id = generateRandomString(10))
@endif

@section('table-head-' . $id)
    <tr>
        <th>Name</th>
        <th>ISO_639_1</th>
        <th>Progress</th>
        <th>Translations</th>
        <th>Status</th>
    </tr>
@endsection

@section('table-body-' . $id)
    @foreach($languages as $language)
        <tr>
            <td>
                {{ $language->name }}
                @if($language->isFallbackLanguage())
                    <span class="badge badge-primary">base</span>
                @endif
            </td>
            <td>{{ $language->ISO_639_1 }}</td>
            <td>
                @include('i18n::languages.partials.progress_bar')
            </td>
            <td>
                <a href="{{ route('i18n.languages.translations', compact('language')) }}">
                    <i class="fe fe-list"></i> Translations
                </a>
            </td>
            <td>
                @if($language->enabled)
                    <button type="button" class="btn btn-sm btn-success" @ajaxmodal
                            data-ajax-url="{{ route('i18n.languages.disable.dialog', [
                                'language' => $language,
                                '_mcallback' => \Illuminate\Support\Facades\Request::fullUrl()
                            ]) }}">
                        <i class="fe fe-check mr-2"></i>Enabled
                    </button>
                @else
                    <button type="button" class="btn btn-sm btn-warning" @ajaxmodal
                            data-ajax-url="{{ route('i18n.languages.enable.dialog', [
                                'language' => $language,
                                '_mcallback' => \Illuminate\Support\Facades\Request::fullUrl()
                            ]) }}">
                        <i class="fe fe-x mr-2"></i>Disabled
                    </button>
                @endif
            </td>
        </tr>
    @endforeach
@endsection