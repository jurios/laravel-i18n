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
        <th>Actions</th>
    </tr>
@endsection

@section('table-body-' . $id)
    @foreach($languages as $language)
        <tr>
            <td>
                {{ $language->name }}
                @if($language->isBaseLanguage())
                    <span class="badge badge-primary">base</span>
                @endif
                @if($language->isDefaultLanguage())
                    <span class="badge badge-info">default</span>
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
                <div class="dropdown">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                        <i class="fe fe-settings"></i>
                    </button>
                    <div class="dropdown-menu">
                        @if(!$language->isDefaultLanguage())
                            <a class="dropdown-item" href="#" @ajaxmodal
                               data-ajax-url="{{ route('i18n.languages.default.dialog', compact('language')) }}">
                                Mark as default
                            </a>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
@endsection
