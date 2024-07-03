<!DOCTYPE html>
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>Admin Panel</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Admin panel" name="description" />
        <meta content="Suman Paul" name="author" />
        <link rel="shortcut icon" href="/favicon.ico" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="{{asset('/admin/global/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/admin/global/plugins/simple-line-icons/simple-line-icons.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/admin/global/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/admin/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="{{asset('/admin/global/plugins/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/admin/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/admin/global/plugins/bootstrap-sweetalert/sweetalert.css')}}" rel="stylesheet" type="text/css"
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="{{asset('/admin/global/css/components-md.min.css')}}" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{{asset('/admin/global/css/plugins-md.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="{{asset('/admin/layouts/layout/css/layout.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/admin/pages/css/login-3.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/admin/layouts/layout/css/themes/default.min.css')}}" rel="stylesheet" type="text/css" id="style_color" />
        <link href="{{asset('/admin/layouts/layout/css/custom.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/admin/pages/css/login-3.css')}}" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->

        <!-- featherlight -->
        <link href="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.css" type="text/css" rel="stylesheet" />

        <!-- custom css -->
        <link href="{{asset('/admin/global/css/custom.css')}}" rel="stylesheet" type="text/css" />


        <link rel="shortcut icon" href="/favicon.ico" />
    </head>
    <!-- END HEAD -->

        @include('static.sidebar')



            <!-- content -->
            @yield('content')


                </div>
                <!-- END CONTENT -->

            </div>
            <!-- END CONTAINER -->
            <!-- BEGIN FOOTER -->
            <div class="page-footer">
                <div class="page-footer-inner"> {{date('Y')}} &copy; V1technologies</div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
            </div>
            <!-- END FOOTER -->
            </div>



        <!--[if lt IE 9]>
        <script src="{{asset('/admin/global/plugins/respond.min.js')}}"></script>
        <script src="{{asset('/admin/global/plugins/excanvas.min.js')}}"></script>
        <script src="{{asset('/admin/global/plugins/ie8.fix.min.js')}}"></script>
        <![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="{{asset('/admin/global/plugins/jquery.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('/admin/global/plugins/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('/admin/global/plugins/js.cookie.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('/admin/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('/admin/global/plugins/jquery.blockui.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('/admin/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{asset('/admin/global/scripts/datatable.js')}}" type="text/javascript"></script>
        <script src="{{asset('/admin/global/plugins/datatables/datatables.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('/admin/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js')}}" type="text/javascript"></script>
        <script src="{{asset('/admin/global/plugins/bootstrap-sweetalert/sweetalert.min.js')}}" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="{{asset('/admin/global/scripts/app.min.js')}}" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="{{asset('/admin/pages/scripts/table-datatables-managed.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('/admin/pages/scripts/ui-sweetalert.min.js')}}" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="{{asset('/admin/layouts/layout/scripts/layout.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('/admin/layouts/layout/scripts/demo.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('/admin/layouts/global/scripts/quick-sidebar.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('/admin/layouts/global/scripts/quick-nav.min.js')}}" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->


        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{asset('/admin/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('/admin/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
        <!-- <script src="{{asset('/admin/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}" type="text/javascript"></script> -->
        <!-- END PAGE LEVEL PLUGINS -->

        <script src="{{asset('/admin/pages/scripts/components-date-time-pickers.min.js')}}" type="text/javascript"></script>

        <script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>

        <script src="{{asset('/admin/tinymce/tinymce.min.js')}}" type="text/javascript"></script>

         <!-- featherlight -->
        <script src="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>

    <!--custom-->
    <script src="{{asset('/admin/global/scripts/custom.js')}}"></script>
    <!--custom END-->

    </body>
</html>

