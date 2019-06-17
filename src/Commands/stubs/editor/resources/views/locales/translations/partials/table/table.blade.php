@extends('i18n::layouts.editor.partials.table.table')

@if(!isset($id))
    @php($id = Str::random(10))
@endif

@php($action = route('i18n.locales.translations.index', compact('locale')))

@section('table-filters-header-' . $id)
@endsection

@section('filters')
@endsection

@section('table-head-' . $id)
    <tr>
        <th width="30%">Fallback Translation</th>
        <th>Translation </th>
        <th>Actions</th>
    </tr>
@endsection

@section('table-body-' . $id)
    @foreach($fallback->translations as $fallback_translation)
        <tr>
            <td>
                <textarea class="form-control">{{ $fallback_translation->translation }}</textarea>
            </td>
            <td>
                <textarea class="form-control" name="translation">{{ !is_null($translation = $locale->translation($fallback_translation->original)) ? $translation->translation : "" }}</textarea>
            </td>
            <td class="table--actions">
                <form class="line-update-form"
                      action="{{ route('i18n.locales.translations.update', ['locale' => $locale]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <div class="btn-group-sm">
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                    </div>
                    <input type="hidden" name="translation" value="">
                    <input type="hidden" name="original" value="{{$fallback_translation->original}}">
                </form>
            </td>
        </tr>
    @endforeach
@endsection

@section('table-footer-' . $id)
@endsection

