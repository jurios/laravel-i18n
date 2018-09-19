@extends('i18n::layout.partials.table.table')

@if(!isset($id))
    @php($id = generateRandomString(10))
@endif

@section('table-head-' . $id)
    <tr>
        <th>Base language reference</th>
        <th>{{ $language->name }} translation</th>
        <th>Needs revision</th>
        <th>Actions</th>
    </tr>
@endsection

@section('table-body-' . $id)
    @foreach($lines as $line)
        <tr>
            <td>
                <textarea class="form-control" disabled="">{{ $line->text }}</textarea>
            </td>
            <td>
                @if(!is_null($language->translations()->where('md5', $line->md5)->first()))
                    @php($translated_line = $language->translations()->where('md5', $line->md5)->first())
                @else
                    @php($translated_line = new \Kodilab\LaravelI18n\Translation())
                @endif
                <textarea class="form-control">{{ $translated_line->text }}</textarea>
            </td>
            <td class="text-center" style="vertical-align: middle;">
                <div class="selectgroup selectgroup-pills">
                    <label class="selectgroup-item">
                        <input name="needs_revision" value="true" class="selectgroup-input"
                               {{ $translated_line->needs_revision ? 'checked' : '' }} type="checkbox">
                        <span class="selectgroup-button">Needs revision</span>
                    </label>
                </div>
            </td>
            <td align="center" class="text-center" style="vertical-align: middle;">
                <button type="button" class="btn btn-success btn-sm"><i class="fe fe-upload mr-2"></i>Upload</button>
            </td>
        </tr>
    @endforeach
@endsection
