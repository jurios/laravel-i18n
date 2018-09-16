<div id="{{$id}}" class="table-container">
    <table class="table table-bordered table-hover table-sm datatable">
        <thead>
        @yield('table-head-' . $id)
        </thead>
        <tbody>
        @yield('table-body-' . $id)
        </tbody>
    </table>
</div>