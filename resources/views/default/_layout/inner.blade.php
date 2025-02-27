@extends(AdminTemplate::getViewPath('_layout.base'))

@section('content')
    <div class="wrapper" id="vueApp">
        <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
            @include(AdminTemplate::getViewPath('_partials.header'))
        </nav>

        @php
            if (Cookie::get('menu-state') == 'close') {
                $menu_class = 'sidebar-collapse';
            }	else {
                $menu_class = 'sidebar-open';
            }
        @endphp

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            @include(AdminTemplate::getViewPath('_partials.navigation'))
        </aside>

        <div class="content-wrapper">

            <div class="content-header">
                {{-- @sngrl merge: bs4 to dev --}}
                {{--
                <h1>
                    {!! $title !!}
                </h1>
                --}}
                <div class="container-fluid">
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-12 col-md-6">
                            <h1>
                                {!! $title !!}
                            </h1>
                        </div>

                        <div class="col-sm-12 col-md-6">
                            {!! $template->renderBreadcrumbs($breadcrumbKey) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="content body">
                @stack('content.top')

                {!! $content !!}

                @stack('content.bottom')
            </div>
        </div>
    </div>
@stop
