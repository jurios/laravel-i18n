@if(!isset($id))
    @php($id = Str::random(10))
@endif

<div id="{{ $id }}" class="card card-table">
    <div class="card-header d-flex flex-row justify-content-between align-content-center">
        <div class="card-title pt-2 w-100">
            <div class="row">
                <div class="h2 col-md-11">
                    @yield('table-title-' . $id)
                </div>
                <div class="float-right col-md-1">
                    <a href="javascript:;" class="btn btn-sm btn-outline-info btn-filters"
                       data-card-id="{{ $id }}" title="More filters">
                        {{ __('Filters') }}
                    </a>
                </div>
            </div>
            <div class="row">
                <small class="col-md-12">
                    {{ __('Filters:') }}
                </small>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @yield('table-filters-list-' . $id)
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(isset($action))
            <form action="{{ $action }}" method="GET" class="card-filters">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-collapsed mb-2">
                            <div class="card-body">
                                @yield('filters')
                            </div>
                            <div class="card-footer">
                                <div class="d-flex text-right">
                                    <button type="submit" class="btn btn-primary ml-auto">
                                        {{ __('Apply filters') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        @endif

        <div id="{{$id}}" class="table-container">
            <table class="table table-bordered table-hover table-sm">
                <thead>
                @yield('table-head-' . $id)
                </thead>
                <tbody>
                @yield('table-body-' . $id)
                </tbody>
            </table>
            <div>
                @yield('table-footer-' . $id)
            </div>
        </div>
    </div>
</div>

@push('inline-scripts')
    <script>
        (function ($) {

            function toggleFilters(card_id) {
                const $selector = $('.card#' + card_id + ' .card-filters');

                $selector.css('display') === 'none' ?
                    $selector.css('display', 'block') : $selector.css('display', 'none');

            }

            $('document').ready(function () {
                $('.btn-filters').on('click', function () {
                    const card_id = $(this).data('card-id') || null;

                    if (card_id === null) {
                        return console.log('Card ID no defined in the filter btn');
                    }

                    toggleFilters(card_id);
                });
            });
        })(jQuery);
    </script>
@endpush