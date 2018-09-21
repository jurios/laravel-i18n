<div class="header py-4">
    <div class="container">
        <div class="d-flex">
            <a class="header-brand" href="./index.html">
                {{--<img src="./demo/brand/tabler.svg" class="header-brand-img" alt="tabler logo">--}}
                <h3>Laravel i18n</h3>
            </a>
            <div class="d-flex order-lg-2 ml-auto">
                <div class="dropdown">
                    @include('i18n::layout.partials.header.auth')
                </div>
            </div>
            <a href="#" class="header-toggler d-lg-none ml-3 ml-lg-0" data-toggle="collapse" data-target="#headerMenuCollapse">
                <span class="header-toggler-icon"></span>
            </a>
        </div>
    </div>
</div>