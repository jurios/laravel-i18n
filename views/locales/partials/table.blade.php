@extends('i18n::layout.partials.table.table')

@if(!isset($id))
    @php($id = generateRandomString(10))
@endif

@php($action = route('i18n.locales.index', compact('locale')))

@section('header-filters')
    @if(hasQueryString('qf-name'))
        <span class="tag">
            nombre:'<i>{{getQueryString('qf-name')}}</i>'
        </span>
    @endif
@endsection

@section('filters')
    <div class="row">
        <div class="col-lg-8">
            <div class="form-group">
                <label class="form-label">Nombre</label>
                <input class="form-control" name="qf-name" placeholder="Language name" type="text"
                       value="{{ getQueryString('qf-name', null) }}">
            </div>
        </div>
    </div>
@endsection

@section('table-head-' . $id)
    <tr>
        <th>Reference</th>
        <th>Description</th>
        <th>Dialect of</th>
        <th width="30%" class="text-center">Progress</th>
        <th>Translations</th>
        <th>Enabled</th>
        <th>Actions</th>
    </tr>
@endsection

@section('table-body-' . $id)
    @foreach($locales as $locale)
        <tr>
            <td>
                {{ $locale->reference }}
                @if($locale->isFallbackLocale())
                    <span class="badge badge-primary">fallback</span>
                @endif
                @if($locale->created_by_sync)
                    <i class="fa fa-exclamation-triangle text-warning"
                       title="This locale has been created by sync with default values. Please, check it over."></i>
                @endif
            </td>
            <td>{{ $locale->description }}</td>
            <td>
                @if($locale->dialect_of)
                    {{ $locale->dialect_of->reference }}
                @endif
            </td>
            <td class="td--progress">
                @include('i18n::locales.partials.progress_bar')
            </td>
            <td>
                <a href="{{ route('i18n.locales.translations.index', compact('locale')) }}">
                    <i class="fe fe-list"></i> Translations
                </a>
            </td>
            <td class="text-center">
                @if($locale->enabled)
                    <span class="badge badge-success">
                        Enabled
                    </span>
                    <a href="" title="Disable this locale" @ajaxmodal
                       data-ajax-url="{{ route('i18n.locales.disable.dialog', [
                                'locale' => $locale,
                                'from' => \Illuminate\Support\Facades\Request::fullUrl()
                            ]) }}">
                        <i class="fas fa-toggle-off text-danger"></i>
                    </a>
                @else
                    <span class="badge badge-danger">
                        Disabled
                    </span>
                    <a href="javascript:;" title="Enable this locale" @ajaxmodal
                       data-ajax-url="{{ route('i18n.locales.enable.dialog', [
                                'locale' => $locale,
                                'from' => \Illuminate\Support\Facades\Request::fullUrl()
                            ]) }}">
                        <i class="fas fa-toggle-on text-success"></i>
                    </a>
                @endif
            </td>
            <td class="table--actions">
                <a href="#"><i class="fa fa-eye text-primary"></i></a>
                <a href="#"><i class="fa fa-edit text-info"></i></a>
                <a href="#"><i class="fa fa-trash text-danger"></i></a>
            </td>
        </tr>
    @endforeach
@endsection
