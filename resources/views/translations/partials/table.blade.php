@extends('i18n::layout.partials.table.table')

@if(!isset($id))
    @php($id = generateRandomString(10))
@endif

@php($action = route('i18n.locales.translations.index', compact('locale')))

@section('header-filters')
    @if(filledQueryString('qf-translation'))
        <span class="tag">
            text:'<i>{{getQueryString('qf-translation')}}</i>'
        </span>
    @endif

    @if(filledQueryString('qf-status'))
        <span class="tag">
            status:'<i>{{getQueryString('qf-status')}}</i>'
        </span>
    @endif

    @if(filledQueryString('qf-needs_revision'))
        <span class="tag">
            needs_revision:'<i>{{getQueryString('qf-needs_revision')}}</i>'
        </span>
    @endif
@endsection

@section('filters')
    <div class="row">
        <div class="col-lg-8">
            <div class="form-group">
                <label class="form-label">Text</label>
                <input class="form-control" name="qf-translation" placeholder="Translatable text" type="text"
                       value="{{ getQueryString('qf-translation', null) }}">
            </div>
        </div>

        <div class="col-lg-4">
            <div class="form-group">
                <label class="form-label">Translation status</label>
                <select name="qf-status" id="input-status" class="form-control custom-select">
                    <option value="all">All</option>
                    <option value="translated">Translated</option>
                    <option value="untranslated">Untranslated</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="form-group">
                <label class="custom-control custom-checkbox custom-control-inline">
                    <input class="custom-control-input" name="qf-needs_revision" value="true" {{ filledQueryString('qf-needs_revision') ? 'checked' : '' }} type="checkbox">
                    <span class="custom-control-label">Show <i>needs revision</i> only</span>
                </label>
            </div>
        </div>
    </div>
@endsection



@section('table-head-' . $id)
    <tr>
        <th class="text-center">Fallback locale reference</th>
        <th class="text-center">{{ $locale->reference }} translation</th>
        <th class="text-center">Needs revision</th>
        <th class="text-center">Actions</th>
    </tr>
@endsection

@section('table-body-' . $id)
    @foreach($lines as $line)
        <tr class="line-update-tr">
            <td>
                <textarea class="form-control" disabled="">{{ $line->translation }}</textarea>
            </td>
            <td>
                @if(!is_null($locale->translations()->where('md5', $line->md5)->first()))
                    @php($translated_line = $locale->translations()->where('md5', $line->md5)->first())
                @else
                    @php($translated_line = new \Kodilab\LaravelI18n\Models\Translation())
                @endif
                <textarea class="form-control" name="translation">{{ $translated_line->translation }}</textarea>
            </td>
            <td class="text-center" style="vertical-align: middle;">

            </td>
            <td align="center" class="text-center" style="vertical-align: middle;">
                <form class="line-update-form"
                      action="{{ route('i18n.locales.translations.update', ['locale' => $locale, 'md5' => $line->md5]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <div class="btn-group-sm">
                        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-upload"></i> Save</button>

                        <a href="#" class="btn btn-sm btn-info" @ajaxmodal
                           data-ajax-url="{{ route('i18n.locales.translations.info', ['locale' => $locale, 'md5' => $line->md5]) }}">
                            <i class="fa fa-info-circle"></i>
                        </a>

                        {{--<a href="#" class="btn btn-sm btn-danger"><i class="fe fe-trash"></i> Remove</a>--}}
                    </div>
                    <input type="hidden" name="translation" value="">
                    <input type="hidden" name="needs_revision" value="">
                </form>
            </td>
        </tr>
    @endforeach
@endsection

@push('inline-js')
    <script>

        /**
         * Update a row with the data fetched
         * @param tr
         * @param line
         */
        function updateRowFromFetchData(tr, line)
        {
            getTranslationTextArea(tr).value = line.translation;
        }

        /**
         * Returns the translation textarea from a row
         * @param tr
         */
        function getTranslationTextArea(tr)
        {
            return tr.querySelectorAll('textarea[name="translation"]:not([disabled]')[0];
        }

        /**
         * Show a green color effect as a feedback
         * @param tr
         */
        function renderStatusEffect(tr)
        {
            tr.classList.add('table-success');

            setTimeout(() => {
                tr.classList.remove('table-success');
            }, 1000);
        }

        /**
         * Update the progress bar from the index view
         * @param html
         */
        function updateProgressBar(html) {
            document.querySelector('#translation_progress').innerHTML = html;
        }


        const forms = document.querySelectorAll('.line-update-tr form');

        forms.forEach((form) => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                e.stopPropagation();

                form = e.target;

                tr = form.parentElement.parentElement;

                form.querySelector('input[name="translation"]').value = getTranslationTextArea(tr).value;

                const url = form.getAttribute('action');
                const method = 'POST';
                const data = new FormData(form);

                fetch(url, {
                    method: method,
                    body: data
                }).then(response => {
                    return response.json()
                }).then(response => {
                    updateRowFromFetchData(tr, response.line);
                    renderStatusEffect(tr);
                    updateProgressBar(response.progress_bar_html);
                }).catch(error => console.error(error));
            });
        });
    </script>
@endpush
