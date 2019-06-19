@extends('i18n::layouts.editor.partials.table.table')

@if(!isset($id))
    @php($id = Str::random(10))
@endif

@php($action = route('i18n.locales.index'))

@section('table-filters-header-' . $id)
    @if(hasQueryString('name'))
        <span class="badge badge-success">
            {{__('Name')}}:'<i>{{getQueryString('name')}}</i>'
        </span>
    @endif
@endsection

@section('filters')
    <div class="row">
        <div class="col-lg-8">
            <div class="form-group">
                <label class="form-label">{{ __('Name:') }}</label>
                <input class="form-control" name="name" placeholder="Language name" type="text"
                       value="{{ getQueryString('name', null) }}">
            </div>
        </div>
    </div>
@endsection

@section('table-head-' . $id)
    <tr>
        <th>Reference</th>
        <th>Region</th>
        <th>Description</th>
        <th>Fallback</th>
        <th width="30%" class="text-center">Translation progress</th>
        <th>Enabled</th>
        <th>Actions</th>
    </tr>
@endsection

@section('table-body-' . $id)
    @foreach($locales as $locale)
        <tr>
            <td>
                {{ $locale->iso }}
                @if($locale->created_by_sync)
                    <i class="fa fa-exclamation-triangle text-warning"
                       title="This locale has been created by sync with default values. Please, check it over."></i>
                @endif
            </td>
            <td>{{ $locale->region }}</td>
            <td>{{ $locale->description }}</td>
            <td>
                @if($locale->isFallback())
                    <span class="badge badge-primary">fallback</span>
                @endif
            </td>

            <td class="td--progress" style="cursor: pointer;"
                onclick="window.location.replace('{{ route('i18n.locales.translations.index', compact('locale')) }}')">

                @include('vendor.i18n.editor.locales.partials.progress_bar')

            </td>

            <td class="text-center">
                @if($locale->enabled)
                    <span class="badge badge-success">
                        Enabled
                    </span>
                    {{--<a href="" title="Disable this locale" @ajaxmodal
                       data-ajax-url="{{ route('i18n.locales.disable.dialog', [
                                'locale' => $locale,
                                'from' => \Illuminate\Support\Facades\Request::fullUrl()
                            ]) }}">
                        <i class="fas fa-toggle-on text-success"></i>
                    </a>--}}
                @else
                    <span class="badge badge-danger">
                        Disabled
                    </span>
                @endif
            </td>
            <td class="table--actions">
                <a href="{{ route('i18n.locales.show', compact('locale')) }}">See</a>
                <a href="{{ route('i18n.locales.edit', compact('locale')) }}">Edit</a>
                @unless($locale->isFallback())
                    <a class="locale-destroy-button" href="javascript:;"
                        data-locale-id="{{ $locale->id }}"
                        data-locale-reference="{{ $locale->reference }}"
                        data-locale-description="{{ $locale->description }}">
                        {{ __('Destroy') }}
                    </a>
                @endunless
            </td>
        </tr>
    @endforeach
@endsection

@section('table-footer-' . $id)
    {{ $locales->links() }}
@endsection