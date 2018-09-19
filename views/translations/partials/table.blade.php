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
            <form class="line-update-form"
                  action="{{ route('i18n.languages.translations.update', ['language' => $language, 'md5' => $line->md5]) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
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
                    <button type="submit" class="btn btn-success btn-sm"><i class="fe fe-upload mr-2"></i>Upload</button>
                </td>
            </form>
        </tr>
    @endforeach
@endsection

@push('inline-js')
    <script>
        require(['jquery'], function (jquery) {

            var $ = jquery;

            $(document).ready(function () {
                $('.line-update-form').on('submit', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    $form = $(this);


                    $.ajax({
                        type: 'PATCH',
                        url: $(this).attr('action'),
                        data: $(this).serialize(),

                        success: function(data) {
                            console.log($form.parent());
                            $form.parent().addClass('table-success').fadeIn(10000, function() {
                                console.log('hola');
                            });
                        }
                    });

                });
            });
        });
    </script>

@endpush