<div class="modal fade" id="placeholderModal" tabindex="-1" role="dialog" aria-labelledby="placeholderModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content placeholder">
            <div class="modal-header">
                <h5 class="modal-title" id="placeholderLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="dimmer active">
                    <div class="loader"></div>
                    <div class="dimmer-content" style="min-height: 100px">
                    </div>
                    <div class="text-center">
                        Loading...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
        <div class="modal-content placeholder-error d-none">
            <div class="modal-header">
                <h5 class="modal-title" id="placeholderLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-icon alert-danger" role="alert">
                    <i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i>
                    Something terrible happened when it was loading this modal.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('inline-js')
    <script>
        require(['jquery'], function (jquery) {

            var $ = jquery;

            $(document).ready(function () {
                $('#placeholderModal').on('show.bs.modal', function (e) {
                    $source = $(e.relatedTarget);
                    url = $source.data('ajax-url');

                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function (data) {
                            $('#placeholderModal .modal-dialog').append(data);
                            $('#placeholderModal .modal-dialog .modal-content.placeholder').addClass('d-none');
                        },
                        error: function(data) {
                            $('#placeholderModal .modal-dialog .modal-content.placeholder').addClass('d-none');
                            $('#placeholderModal .modal-dialog .modal-content.placeholder-error').removeClass('d-none');
                        }
                    });


                });

                $('#placeholderModal').on('hidden.bs.modal', function (e) {
                    $('#placeholderModal .modal-dialog .modal-content.ajax').remove();
                });

            });
        });
    </script>

@endpush