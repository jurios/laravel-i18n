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
                        <a href="./index.html" class="nav-link active"><i class="fe fe-home"></i> Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="{{ route('i18n.languages.index') }}" class="nav-link">
                            <i class="fe fe-flag"></i> Languages
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"><i class="fe fe-list"></i> Translations</a>
                        <div class="dropdown-menu dropdown-menu-arrow">
                            @foreach(\Kodilab\LaravelI18n\Language::enabled()->get() as $enabled_language)
                                <a href="{{ route('i18n.languages.translations', ['language' => $enabled_language]) }}"
                                   class="dropdown-item ">{{ $enabled_language->name }}</a>
                            @endforeach
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('i18n.dashboard') }}" class="nav-link">
                            <i class="fe fe-settings"></i> Settings</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>