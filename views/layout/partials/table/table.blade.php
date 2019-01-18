@if(!isset($id))
    @php($id = generateRandomString(10))
@endif
<div id="{{ $id }}" class="card card-table">
    <div class="card-header d-flex flex-row justify-content-between align-content-center">
        <div class="card-title pt-2"></div>
        <a href="javascript:;" class="btn btn-sm btn-outline-info btn-filters" data-card-id="{{ $id }}" title="More filters"><i class="fa fa-search"></i> Filters</a>
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
                            <div class="card-footer text-right">
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary ml-auto">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        @endif

        <div id="{{$id}}" class="table-container">
            <table class="table table-bordered table-hover table-sm datatable">
                <thead>
                @yield('table-head-' . $id)
                </thead>
                <tbody>
                @yield('table-body-' . $id)
                </tbody>
            </table>
            @if(isset($filters))
                <div>
                    {{ $filters->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('inline-js')
<script>
    function toggleFilters(card_id)
    {
        const $selector = $('.card#' + card_id + ' .card-filters');

        $selector.css('display') === 'none' ?
            $selector.css('display', 'block') : $selector.css('display', 'none');

    }

    $('document').ready(function() {
        $('.btn-filters').on('click', function() {
            const card_id = $(this).data('card-id') || null;

            if (card_id === null)
            {
                return console.log('Card ID no defined in the filter btn');
            }

            toggleFilters(card_id);
        });
    });
</script>
@endpush