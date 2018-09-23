@if(isset($action))
    <form action="{{ $action }}" method="GET">

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-collapsed">
                    <div class="card-header">
                        <h3 class="card-title">
                            @yield('header-filters')
                        </h3>
                        <div class="card-options">
                            <a href="#" class="card-options-collapse" data-toggle="card-collapse">
                                <i class="fe fe-chevron-up"></i>
                                <small>show filters</small>
                            </a>
                        </div>
                    </div>
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