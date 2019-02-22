<div class="modal-content ajax">
    <div class="modal-header">
        <h5 class="modal-title" id="{{ $id }}Label">
            @yield('title')
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
    </div>

    @if(isset($form) && isset($form['action']) && isset($form['method']))

        <form action="{{ $form['action'] }}" method="{{ $form['method'] === 'GET' ? 'GET' : 'POST' }}">
            @csrf

    @endif

        @if(isset($form) && isset($form['method']))

            @if($form['method'] === 'DELETE')
                <input type="hidden" name="_method" value="DELETE">
            @endif

            @if($form['method'] === 'PATCH')
                <input type="hidden" name="_method" value="PATCH">
            @endif

        @endif

        <div class="modal-body">
            @yield('content')
        </div>
        <div class="modal-footer">
            @yield('buttons')
        </div>

    @if(isset($form) && isset($form['action']))
        </form>
    @endif
</div>