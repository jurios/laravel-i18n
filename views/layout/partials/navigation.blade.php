<div class="sidebar sidebar-dark">
    <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li class="{{ addClassIfRouteContains('i18n.dashboard', 'active') }}">
            <a href="{{ route('i18n.dashboard') }}">
                <i class="fa fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="{{ addClassIfRouteContains('i18n.locales', 'active') }}">
            <a href="{{ route('i18n.locales.index') }}">
                <i class="fa fa-flag"></i>
                <span>Locales</span>
            </a>
        </li>
    </ul>
</div>
