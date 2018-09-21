@if(!is_null(\Illuminate\Support\Facades\Auth::user()))

    @php($user = \Illuminate\Support\Facades\Auth::user())
    @php($logout_name = config('i18n.logout_route.name'))
    @php($logout_method = !is_null(config('i18n.logout_route.name')) ? config('i18n.logout_route.method') : 'GET')

    <a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
        <span class="avatar" style="background-image: url(./demo/faces/female/25.jpg)"></span>
        <span class="ml-2 d-none d-lg-block">
            <span class="text-default">{{ $user->name }}</span>
            <small class="text-muted d-block mt-1">{{ $user->email }}</small>
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
        <a class="dropdown-item" href="#">
            <i class="dropdown-icon fe fe-help-circle"></i> Need help?
        </a>
        @if(!is_null($logout_name))
            <a class="dropdown-item" href="{{ route($logout_name) }}"
            {{ $logout_method !== 'GET' ? 'onclick="event.preventDefault();document.getElementById(\'logout-form\').submit();"' : '' }}>
                <i class="dropdown-icon fe fe-log-out"></i> Sign out
            </a>
            @if($logout_method !== 'GET')
                <form id="logout-form" action="{{ route($logout_name) }}" method="POST"
                      style="display: none;">
                    @csrf
                </form>
            @endif
        @endif
    </div>

@endif