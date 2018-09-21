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
        <tr class="line-update-tr">
            <td>
                <textarea class="form-control" disabled="">{{ $line->text }}</textarea>
            </td>
            <td>
                @if(!is_null($language->translations()->where('md5', $line->md5)->first()))
                    @php($translated_line = $language->translations()->where('md5', $line->md5)->first())
                @else
                    @php($translated_line = new \Kodilab\LaravelI18n\Translation())
                @endif
                <textarea class="form-control" name="text">{{ $translated_line->text }}</textarea>
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
                <form class="line-update-form"
                      action="{{ route('i18n.languages.translations.update', ['language' => $language, 'md5' => $line->md5]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <button type="submit" class="btn btn-success btn-sm"><i class="fe fe-upload mr-2"></i>Upload</button>
                    <input type="hidden" name="text" value="">
                    <input type="hidden" name="needs_revision" value="">
                </form>
            </td>
        </tr>
    @endforeach
@endsection

@push('inline-js')
    <script>
        require(['jquery'], function (jquery) {

            var $ = jquery;

            $(document).ready(function () {
                $('.line-update-tr :input').on('input propertychange change', function (){

                    $(this).parents('tr').removeClass('table-success');
                    $(this).parents('tr').addClass('table-warning');

                });

                $('.line-update-form').on('submit', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    $form = $(this);

                    $tr = $form.parent().parent();

                    $textarea = $tr.find('textarea[name="text"]');
                    $needs_revision = $tr.find(':input[name="needs_revision"]');

                    $form.find(':input[name="text"]').val($textarea.val());
                    $form.find(':input[name="needs_revision"]').val($needs_revision.prop('checked'));

                    $.ajax({
                        type: 'PATCH',
                        url: $(this).attr('action'),
                        data: $(this).serialize(),

                        success: function(data) {
                            $tr.removeClass('table-warning');
                            $tr.addClass('table-success');
                            $tr.css('transition', 'all 2s');

                            $(document).trigger('language-updated', data.progress_bar_html);
                        }
                    });

                });
            });
        });
    </script>

@endpush