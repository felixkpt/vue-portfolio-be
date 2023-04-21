@if(Request::ajax())
    @if(isset($_GET['t_optimized']))
        @yield('t_optimized')
    @elseif(isset($_GET['ta_optimized']))
        @yield('ta_optimized')
    @else

        <div class="system-title hidden">  @yield('title') </div>

        <section id="auth-login" class="row flexbox-container">

            <div class="col-xl-8 col-11">
                <div class="card bg-authentication mb-0">
                    <div class="row m-0">
                        <!-- left section-login -->
                        <div class="col-md-6 col-12 px-0">
                            <div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
                                <div class="card-header pb-1">
                                    <div class="card-title  text-center">

                                        <?php
                                        $default_logo = asset('/') . "img/artcaffe-logo.svg";
                                        if (getimagesize($default_logo))
                                            $auth_page_logo = $default_logo;
                                        else if (isset($organization->logo) && getimagesize($organization->logo))
                                            $auth_page_logo = asset($organization->logo);
                                        else
                                            $auth_page_logo = $default_logo;
                                        ?>
                                        <img class="card-img-top" style="width: auto" src="{{ $auth_page_logo }}" height="60" alt="Logo">
                                    </div>
                                </div>
                                <div class="card-content">
                                    @yield('content')
                                </div>
                            </div>
                        </div>
                    <!-- right section image -->
                        <div class="col-md-6 d-md-block d-none text-center align-self-center p-3">
                            <div class="card-content">
                                <?php
                                $auth_page_image = asset('auth')."/app-assets/images/pages/login.png";
                                if (@getimagesize(@$organization->auth_page_image))
                                    $auth_page_image = asset($organization->auth_page_image);
                                ?>

                                <img class="img-fluid" src="{{ $auth_page_image }}"
                                     alt="branding logo">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @endif
    @include('common.essential_js')
@else
    <!DOCTYPE html>

    <html class="loading" lang="en" data-textdirection="ltr">
    <!-- BEGIN: Head-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="description" content="">
        <meta name="keywords" content="">
        <title>@yield('title')</title>

        <?php
        $default_favicon_image = asset('metrica') . "/assets/images/icon.png";
        $favicon_image_url = @$organization->favicon_image??'img/favicon.ico';
        $favicon_image_url = ($favicon_image_url) ? asset($favicon_image_url) : $default_favicon_image;
        ?>
        <link rel="shortcut icon" href="{{ @$favicon_image_url }}">
        <!-- <link rel="shortcut icon" href="{{url('metrica')}}/assets/images/icon.png"> -->

        <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700"
              rel="stylesheet">

        <!-- BEGIN: Vendor CSS-->
        <link rel="stylesheet" type="text/css" href="{{url('auth')}}/app-assets/vendors/css/vendors.min.css">
        <!-- END: Vendor CSS-->

        <!-- BEGIN: Theme CSS-->
        <link rel="stylesheet" type="text/css" href="{{url('auth')}}/app-assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="{{url('auth')}}/app-assets/css/bootstrap-extended.min.css">
        <link rel="stylesheet" type="text/css" href="{{url('auth')}}/app-assets/css/colors.min.css">
        <link rel="stylesheet" type="text/css" href="{{url('auth')}}/app-assets/css/components.min.css">
        <link rel="stylesheet" type="text/css" href="{{url('auth')}}/app-assets/css/themes/dark-layout.min.css">
        <link rel="stylesheet" type="text/css" href="{{url('auth')}}/app-assets/css/themes/semi-dark-layout.min.css">

        <!-- END: Theme CSS-->

        <!-- BEGIN: Page CSS-->
        <link rel="stylesheet" type="text/css"
              href="{{url('auth')}}/app-assets/css/core/menu/menu-types/vertical-menu.min.css">
        <link rel="stylesheet" type="text/css" href="{{url('auth')}}/app-assets/css/pages/authentication.css">
        <!-- END: Page CSS-->

        <!-- BEGIN: Custom CSS-->
        <link rel="stylesheet" type="text/css" href="{{url('auth')}}/assets/css/style.css">
        <link href="{{url('plugins/toastr/toastr.min.css')}}" rel="stylesheet">
        <script src="{{url('plugins/toastr/toastr.min.js')}}"></script>
        <!-- END: Custom CSS-->

    </head>
    <!-- END: Head-->

    <!-- BEGIN: Body-->
    <body
        class="vertical-layout vertical-menu-modern 1-column  navbar-sticky footer-static bg-full-screen-image  blank-page blank-page"
        data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body system-container"><!-- login page start -->
                <div class="system-title hidden">@yield('title')</div>
                <section id="auth-login" class="row flexbox-container">
                    <div class="col-xl-8 col-11">
                        <div class="card bg-authentication mb-0">
                            <div class="row m-0">
                                <!-- left section-login -->
                                <div class="col-md-6 col-12 px-0">
                                    <div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
                                        <div class="card-header pb-1">
                                            <div class="card-title  text-center">

                                                <?php
                                                $default_logo = asset('/') . "img/artcaffe-logo.png";
                                                if (isset($organization->logo) && getimagesize($organization->auth_page_logo))
                                                    $auth_page_logo = asset($organization->auth_page_logo);
                                                else if (isset($organization->logo) && getimagesize($default_logo))
                                                    $auth_page_logo = $default_logo;
                                                else if (isset($organization->logo) && getimagesize($organization->logo))
                                                    $auth_page_logo = asset($organization->logo);
                                                else
                                                    $auth_page_logo = $default_logo;
                                                ?>
                                                <img class="card-img-top" style="width: auto" src="{{ $auth_page_logo }}" height="60" alt="Logo">
                                            </div>
                                        </div>
                                        <div class="card-content">
                                            @yield('content')
                                        </div>
                                    </div>
                                </div>

                            <!-- right section image -->
                                <div class="col-md-6 d-md-block d-none text-center align-self-center p-3">
                                    <div class="card-content">
                                        <?php
                                        $auth_page_image = asset('auth')."/app-assets/images/pages/login.png";
                                        if (isset($organization->logo) && getimagesize($organization->auth_page_image))
                                            $auth_page_image = asset($organization->auth_page_image);
                                        ?>

                                        <img class="img-fluid" src="{{ $auth_page_image }}"
                                             alt="branding logo">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- login page ends -->
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <!-- BEGIN: Vendor JS-->
    <script src="{{url('auth')}}/app-assets/vendors/js/vendors.min.js"></script>
    <script src="{{url('auth')}}/app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.min.js"></script>
    <script src="{{url('auth')}}/app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.min.js"></script>
    <script src="{{url('auth')}}/app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{url('auth')}}/app-assets/js/scripts/configs/vertical-menu-light.min.js"></script>
    <script src="{{url('auth')}}/app-assets/js/core/app-menu.min.js"></script>
    <script src="{{url('auth')}}/app-assets/js/core/app.min.js"></script>
    <script src="{{url('auth')}}/app-assets/js/scripts/components.min.js"></script>
    <script src="{{url('auth')}}/app-assets/js/scripts/footer.min.js"></script>
    <script src="{{url('plugins/toaster/toastr.min.js')}}"></script>

    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <!-- END: Page JS-->
    <script src="{{ asset('js/jquery.history.js') }}"></script>
    <script src="{{ url('js/jquery.datetimepicker.js') }}"></script>
    <script src="{{ url('js/jquery.form.js') }}"></script>
    @include('common.javascript')

    @stack('footer-scripts')
    <!-- Custom main Js -->
    <input type="hidden" name="material_page_loaded" value="1">
    <script src="{{ url('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    </body>
    <!-- END: Body-->

    </html>
@endif
