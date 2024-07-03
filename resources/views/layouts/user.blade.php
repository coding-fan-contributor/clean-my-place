<!DOCTYPE html>
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>@if (isset($title)) {{$title}} @else {{config('WEBSITE_NAME')}} @endif</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Description" name="@if (isset($description)) {{$description}}@else {{config('WEBSITE_NAME')}} @endif" />
        <meta content="V1 Technologies" name="author" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if(request()->is('review/*'))
        <!-- review meta data  -->

        @elseif(request()->is('u/*'))
        <!-- user meta data  -->

        @endif
        <link rel="shortcut icon" href="/favicon.ico" />
        <!-- STYLES -->
        <link rel="stylesheet" href="{{asset('/assets/v1-css-2019/style.css')}}">
        <link rel="stylesheet" href="{{asset('/assets/v1-css-2019/styles.css')}}">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{asset('/assets/v1-css-2019/demo.css')}}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.theme.min.css">

        <link rel="stylesheet" href="{{asset('/assets/v1-css-2019/jquery.mmenu.all.css')}}">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="{{asset('/assets/v1-js-2019/jquery.mmenu.min.all.js')}}"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.css" integrity="sha256-zuyRv+YsWwh1XR5tsrZ7VCfGqUmmPmqBjIvJgQWoSDo=" crossorigin="anonymous" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.js" integrity="sha256-JirYRqbf+qzfqVtEE4GETyHlAbiCpC005yBTa4rj6xg=" crossorigin="anonymous"></script>

        {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> --}}

        <link href="{{asset('admin/global/plugins/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('admin/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css')}}" rel="stylesheet" type="text/css" />

        <script src="{{asset('admin/global/scripts/datatable.js')}}" type="text/javascript"></script>
        <script src="{{asset('admin/global/plugins/datatables/datatables.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('admin/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js')}}" type="text/javascript"></script>
        <!-- STYLES -->

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" integrity="sha256-yMjaV542P+q1RnH6XByCPDfUFhmOafWbeLPmqKh11zo=" crossorigin="anonymous" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" integrity="sha256-5YmaxAwMjIpMrVlK84Y/+NjCpKnFYa8bWWBbUHSBGfU=" crossorigin="anonymous"></script>

        <script src="{{asset('/assets/v1-js-2019/markercluster.min.js')}}">
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDEbxog-yEZl93UjGylBj8e4GLfzO3LAu8"
          type="text/javascript"></script>

        <script type="text/javascript">
            $(function() {
                $('nav#menu').mmenu();
            });
            </script>
            <div class="header">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="logo">
                                <div class="responsivemnu">
                                    <a href="#menu"><i class="fa fa-bars"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div id="cssmenu">
                                <ul>
                                    <li><a href="/">Home</a></li>
                                    <li><a href="/about">About</a></li>
                                    {{-- <li><a href="/signup">Plans</a></li> --}}
                                    <li><a href="/blog">Blog</a></li>
                                    <li><a href="/contact">Contact Us</a></li>
                                    @if (session('user'))
                                    <li><a href="/dashboard">Dashboard</a>
                                        <ul>
                                            <li><a href="/profile">Profile</a></li>
                                            <li><a href="/reviews">Reviews</a></li>
                                            <li><a href="/logout">Logout</a></li>
                                        </ul>
                                    </li>

                                    @else
                                    <li><a href="/login">Login</a></li>
                                    <li class="sign"><a href="/signup">Dog Walkers Sign Up</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        <link rel="shortcut icon" href="/favicon.ico" />
    </head>
    <!-- END HEAD -->
    <body>
        <!-- content -->
        @yield('content')
        <!-- END CONTENT BODY -->

        <div class="sub-footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <p>&copy; {{config('WEBSITE_NAME')}} | {{date('Y')}} All Right Reserved </p>
                    </div>

                    <div class="col-md-4">
                        <p style="text-align:center;"><a href="/privacy-policy">Privacy Policy | </a><a href="/terms-and-conditions">Terms And Conditions</a></p>
                    </div>

                    <div class="col-md-4">
                        <div class="footer-right">
                            <ul id="social" class="social-footer">
                                @isset($settings->social_facebook)
                                    <li class="brand brand-facebook"><a href="{{$settings->social_facebook}}"><i class="fa fa-facebook"></i></a></li>
                                @endisset
                                @isset($settings->social_twitter)
                                    <li class="brand brand-twitter"><a href="{{$settings->social_twitter}}"><i class="fa fa-twitter"></i></a></li>
                                @endisset
                                @isset($settings->social_linkedin)
                                    <li class="brand brand-linkedin"><a href="{{$settings->social_linkedin}}"><i class="fa fa-linkedin"></i></a></li>
                                @endisset
                                @isset($settings->social_instagram)
                                    <li class="brand brand-instagram"><a href="{{$settings->social_instagram}}"><i class="fa fa-instagram"></i></a></li>
                                @endisset
                                @isset($settings->social_google)
                                    <li class="brand brand-google-plus"><a href="{{$settings->social_google}}"><i class="fa fa-google-plus"></i></a></li>
                                @endisset
                                @isset($settings->social_youtube)
                                    <li class="brand brand-youtube"><a href="{{$settings->social_youtube}}"><i class="fa fa-youtube"></i></a></li>
                                @endisset
                                @isset($settings->social_pinterest)
                                    <li class="brand brand-pinterest"><a href="{{$settings->social_pinterest}}"><i class="fa fa-pinterest"></i></a></li>
                                @endisset
                                @isset($settings->social_tumblr)
                                    <li class="brand brand-tumblr"><a href="{{$settings->social_tumblr}}"><i class="fa fa-tumblr"></i></a></li>
                                @endisset
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <nav id="menu">
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/about">About</a></li>
                {{-- <li><a href="/signup">Plans</a></li> --}}
                <li><a href="/blog">Blog</a></li>
                <li><a href="/contact">Contact Us</a></li>
                @if (session('user'))
                <li><a href="/dashboard">Dashboard</a>
                    <ul>
                        <li><a href="/profile">Profile</a></li>
                        <li><a href="/reviews">Reviews</a></li>
                        <li><a href="/logout">Logout</a></li>
                    </ul>
                </li>
                @else
                <li><a href="/login">Login</a></li>
                <li class="sign"><a href="/signup">Dog Walkers Sign Up</a></li>
                @endif
                <li><a href="/privacy-policy">Privacy Policy</a></li>
                <li><a href="/terms-and-conditions">Terms And Conditions</a></li>
            </ul>
        </nav>



        <script>
            $(document).ready(function () {
                $(".header").before($(".header").clone().addClass("animateIt"));
                $(window).on("scroll", function () {
                    $("body").toggleClass("down", ($(window).scrollTop() > 50));
                });
            });
        </script>

        <script>
            $(document).ready(function(){
                $("#testimonial-slider").owlCarousel({
                    items:2,
                    itemsDesktop:[1000,2],
                    itemsDesktopSmall:[980,1],
                    itemsTablet:[768,1],
                    pagination:true,
                    navigation:false,
                    navigationText:["",""],
                    autoPlay:true
                });
            });
        </script>
    </body>
</html>
