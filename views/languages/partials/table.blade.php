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
    </tr>
@endsection

@section('table-body-' . $id)
    @foreach($languages as $language)
        <tr>
            <td>
                {{ $language->name }}
                @if($language->isFallbackLanguage())
                    <span class="badge badge-primary">fallback</span>
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
        </tr>
    @endforeach
@endsection
