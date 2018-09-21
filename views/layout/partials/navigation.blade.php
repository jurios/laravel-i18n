<div class="header collapse d-lg-flex p-0" id="headerMenuCollapse">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-3 ml-auto">
                {{--<form class="input-icon my-3 my-lg-0">--}}
                    {{--<input type="search" class="form-control header-search" placeholder="Search&hellip;" tabindex="1">--}}
                    {{--<div class="input-icon-addon">--}}
                        {{--<i class="fe fe-search"></i>--}}
                    {{--</div>--}}
                {{--</form>--}}
            </div>
            <div class="col-lg order-lg-first">
                <ul class="nav nav-tabs border-0 flex-column flex-lg-row">
                    <li class="nav-item">
                        <a href="{{ route('i18n.languages.index') }}" class="nav-link" {{ addClassIfRouteMatch('i18n.languages') }}><i class="fe fe-home"></i> Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="{{ route('i18n.languages.index') }}" class="nav-link {{ addClassIfRouteMatch('i18n.languages') }}">
                            <i class="fe fe-flag"></i> Languages
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="{{ route('i18n.languages.index') }}" class="nav-link {{ addClassIfRouteMatch('i18n.languages.translations') }}">
                            <i class="fe fe-list"></i> Translations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('i18n.settings.languages.index') }}" class="nav-link {{ addClassIfRouteContains('i18n.settings') }}">
                            <i class="fe fe-settings"></i> Settings</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>