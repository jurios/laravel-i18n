<div class="modal fade" id="{{ $id }}-help-modal" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}-help-modalModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}-help-modalModalLabel">
                    Help: @yield('title' . $id . '-help-modal')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    @yield('content' . $id. '-help-modal')
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>