@extends('i18n::layouts.editor.editor')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="btn-group float-right">
                <a href="{{ route('i18n.locales.create') }}" class="btn btn-success">
                    {{ __('New locale') }}
                </a>
            </div>
        </div>
    </div>

    @include('vendor.i18n.editor.locales.partials.table')
@endsection

@push('inline-scripts')
<script>
    (function ($) {

        $('document').ready(function () {
            $('.locale-destroy-button').on('click', function () {
                const id = $(this).data('locale-id');
                const reference = $(this).data('locale-reference');
                const description = $(this).data('locale-description');

                result = confirm("{{ __('Are you sure want to remove the locale') }}" + " " + reference + ": " + description + "?");

                if (result === true) {

                    var url = "{{route('i18n.locales.destroy', ['locale' => '%ID%'])}}".replace('%ID%', id);

                    $.ajax(url, {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            '_method': 'DELETE'
                        }
                    }).done(function (data) {
                        const url = "{{ route('i18n.locales.index') }}"
                        window.location.replace(url);
                    });

                }
            })
        });
    })(jQuery);
</script>
@endpush