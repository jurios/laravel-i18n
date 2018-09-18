@extends('i18n::layout.partials.table.table')

@if(!isset($id))
    @php($id = generateRandomString(10))
@endif

@section('table-head-' . $id)
    <tr>
        <th>Name</th>
        <th>ISO_639_1</th>
        <td>Progress</td>
        <th>Translations</th>
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
                <div class="clearfix">
                    <div class="float-left">
                        <strong>{{ $language->translations_perc }}%</strong>
                    </div>
                    <div class="float-right">
                        <small class="text-muted">
                            {{ count($language->translations) }} of
                            {{ count(\Kodilab\LaravelI18n\Language::getBaseLanguage()->translations) }}
                        </small>
                    </div>
                </div>
                <div class="progress progress-xs">
                    <div class="progress-bar bg-yellow" role="progressbar"
                         style="width: {{ $language->translations_perc }}%"
                         aria-valuenow="{{ $language->translations_perc }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
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
