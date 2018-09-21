@extends('i18n::layout.partials.table.table')

@if(!isset($id))
    @php($id = generateRandomString(10))
@endif

@section('table-head-' . $id)
    <tr>
        <th class="text-center">Base language reference</th>
        <th class="text-center">{{ $language->name }} translation</th>
        <th class="text-center">Needs revision</th>
        <th class="text-center">Actions</th>
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
                        <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip" data-placement="bottom" title="Needs revision">
                            <i class="fe fe-eye"></i>
                        </span>
                    </label>
                </div>
            </td>
            <td align="center" class="text-center" style="vertical-align: middle;">
                <form class="line-update-form"
                      action="{{ route('i18n.languages.translations.update', ['language' => $language, 'md5' => $line->md5]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <div class="btn-group-sm">
                        <button type="submit" class="btn btn-success btn-sm"><i class="fe fe-upload mr-2"></i>Upload</button>

                        <a href="#" class="btn btn-sm btn-info" @ajaxmodal
                           data-ajax-url="{{ route('i18n.languages.translations.info', ['language' => $language, 'md5' => $line->md5]) }}">
                            <i class="fe fe-info"></i>
                        </a>

                        <a href="#" class="btn btn-sm btn-danger"><i class="fe fe-trash"></i> Remove</a>
                    </div>
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
                            let $_tr = $tr;
                            $_tr.removeClass('table-warning');
                            $_tr.addClass('table-success');

                            setTimeout(function() {
                                $_tr.removeClass('table-success');
                            }, 1000);

                            $(document).trigger('language-updated', data.progress_bar_html);
                        }
                    });

                });
            });
        });
    </script>

@endpush