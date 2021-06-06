<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

        <title>@yield('site_title') [Omni CRM]</title>

        <!-- Fav Icon -->
        <link rel="icon" href="images/favicon.ico" type="image/x-icon">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Ubuntu:300,300i,400,400i,500,500i,700,700i&display=swap" rel="stylesheet">

        <!-- Stylesheets -->
        <link href="css/font-awesome-all.css" rel="stylesheet">
        <link href="css/flaticon.css" rel="stylesheet">
        <link href="css/owl.css" rel="stylesheet">
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/jquery.fancybox.min.css" rel="stylesheet">
        <link href="css/animate.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <link href="css/responsive.css" rel="stylesheet">
    </head>

    <!-- page wrapper -->
    <body class="boxed_wrapper">

        <!-- preloader -->
        <div class="preloader"></div>
        <!-- preloader -->

        <!-- main header -->
        <header class="main-header style-four">
            <div class="outer-container">
                <div class="container">
                    <div class="main-box clearfix">
                        <div class="logo-box pull-left">
                            <figure class="logo"><a href="http://www.crmomni.com/"><img src="images/logo-4.png" alt=""></a></figure>
                        </div>
                        @include('templates.crmomni.partials.layout-menu-header')
                    </div>
                </div>
            </div>

            <!--sticky Header-->
            <div class="sticky-header">
                <div class="container clearfix">
                    <figure class="logo-box"><a href="http://www.crmomni.com/"><img src="images/small-logo.png" alt=""></a></figure>
                    <div class="menu-area">
                        <nav class="main-menu clearfix">
                            <!--Keep This Empty / Menu will come through Javascript-->
                        </nav>
                    </div>
                </div>
            </div>
        </header>
        <!-- main-header end -->

        <!-- Mobile Menu  -->
        <div class="mobile-menu">
            <div class="menu-backdrop"></div>
            <div class="close-btn"><i class="fas fa-times"></i></div>
            
            <nav class="menu-box">
                <div class="nav-logo"><a href="index.html"><img src="images/logo.png" alt="" title=""></a></div>
                <div class="menu-outer"><!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header--></div>
                <div class="contact-info">
                    <h4>Contact Info</h4>
                    <ul>
                        <li>Chicago 12, Melborne City, USA</li>
                        <li><a href="tel:+8801682648101">+88 01682648101</a></li>
                        <li><a href="mailto:info@example.com">info@example.com</a></li>
                    </ul>
                </div>
                <div class="social-links">
                    <ul class="clearfix">
                        <li><a href="#"><span class="fab fa-twitter"></span></a></li>
                        <li><a href="#"><span class="fab fa-facebook-square"></span></a></li>
                        <li><a href="#"><span class="fab fa-pinterest-p"></span></a></li>
                        <li><a href="#"><span class="fab fa-instagram"></span></a></li>
                        <li><a href="#"><span class="fab fa-youtube"></span></a></li>
                    </ul>
                </div>
            </nav>
        </div><!-- End Mobile Menu -->

        <!-- page-title -->
        <section class="page-title" style="background-image: url(images/background/pagetitle-bg.png);background-position-y: -185px;">
            <div class="anim-icons">
                <div class="icon icon-1"><img src="images/icons/anim-icon-17.png" alt=""></div>
                <div class="icon icon-2 rotate-me"><img src="images/icons/anim-icon-18.png" alt=""></div>
                <div class="icon icon-3 rotate-me"><img src="images/icons/anim-icon-19.png" alt=""></div>
                <div class="icon icon-4" style="top:280px;"></div>
            </div>
            <div class="container">
                <div class="content-box clearfix">
                    <div class="title-box pull-left">
                        <h1>@yield('page_title')</h1>
                        <p>@yield('page_subtitle')</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- page-title end -->

        <!-- begin:page-content -->
        <div class="d-block">
            @yield('page_content')
        </div>
        <!-- end:page-content -->

        <!-- main-footer -->
        <footer class="main-footer style-five style-six">
            <div class="anim-icons">
                <div class="icon icon-1"><img src="images/icons/pattern-21.png" alt=""></div>
            </div>
            <div class="image-layer" style="background-image: url(images/icons/footer-bg-6.png);"></div>
            <div class="container">
                <div class="footer-top">
                    <div class="widget-section pb-5">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                                <div class="about-widget footer-widget">
                                    <figure class="footer-logo"><a href="index.html"><img src="images/footer-logo-2.png" alt=""></a></figure>
                                    <div class="text">Lorem ipsum dolor sit consectetur adipisicing elit, sed do eiusmod tempor .........</div>
                                    <div class="apps-download d-none">
                                        <h3>Download the App</h3>
                                        <div class="download-btn">
                                            <a href="#" class="app-store-btn">
                                                <i class="fab fa-apple"></i>
                                                <span>Download on the</span>
                                                App Store
                                            </a>
                                            <a href="#" class="google-play-btn">
                                                <i class="fab fa-android"></i>
                                                <span>Get on it</span>
                                                Google Play
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6 col-sm-12 footer-column">
                                <div class="links-widget footer-widget">
                                    <h4 class="widget-title">Company</h4>
                                    <div class="widget-content">
                                        <ul class="list clearfix">
                                            <li><a href="#">About</a></li>
                                            <li><a href="#">Our Leadership</a></li>
                                            <li><a href="#">Carrers</a></li>
                                            <li><a href="#">What We Do</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12 footer-column">
                                <div class="links-widget footer-widget">
                                    <h4 class="widget-title">Links</h4>
                                    <div class="widget-content">
                                        <ul class="list clearfix">
                                            <li><a href="#">Business Dashboards</a></li>
                                            <li><a href="#">Sales Analytics</a></li>
                                            <li><a href="#">Digital Marketing</a></li>
                                            <li><a href="#">Financial Help</a></li>
                                            <li><a href="{{ route('crmomni.site.faq', [request()->getHost()]) }}">FAQs</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                                @include('templates.crmomni.widgets.widget-contact')
                            </div>                                                     
                        </div>
                    </div>
                </div>
            </div>

            <div class="copyright d-flex">
                <div class="flex-grow-0 footer-bottom ml-3">
                    &copy; {{ now()->year }} 
                    <a target="_blank" href="https://www.ellaisys.com/">Ellai Information Systetms Pvt Ltd</a>. All rights reserved
                </div>
                <div class="flex-fill footer-bottom mr-3">
                    <ul class="footer-nav pull-right">
                        <li><a href="{{ route('crmomni.site.tnc', [request()->getHost()]) }}">Terms &amp; Conditions</a></li>
                        <li><a href="{{ route('crmomni.site.policy', [request()->getHost()]) }}">Privacy Policy</a></li>
                        <li><a href="{{ route('crmomni.site.legal', [request()->getHost()]) }}">Legal</a></li>
                    </ul>
                </div>
            </div>
        </footer>
        <!-- main-footer end -->

        <!-- sidebar cart item -->
        <div class="xs-sidebar-group info-group info-sidebar">
            <div class="xs-overlay xs-bg-black"></div>
            <div class="xs-sidebar-widget">
                <div class="sidebar-widget-container">
                    <div class="widget-heading">
                        <a href="#" class="close-side-widget">X</a>
                    </div>
                    <div class="sidebar-textwidget">
                        
                    <!-- Sidebar Info Content -->
                    <div class="sidebar-info-contents">
                        <div class="content-inner">
                            <div class="logo">
                                <a href="index.html"><img src="images/logo-2.png" alt="" /></a>
                            </div>
                            <div class="content-box">
                                <h4>About Us</h4>
                                <p class="text">Lorem ipsum dolor amet consectetur sed do eiusmod tempor incididunt ut labore. Lorem ipsum dolor amet consectetur adipisicing sed do eiusmod tempor incididunt ut labore.</p>
                                <a href="#" class="theme-btn-two">Explore</a>
                            </div>
                            <div class="contact-info">
                                <h4>Contact Info</h4>
                                <ul>
                                    <li>Unit #7, Level #3, Karve Road, Pune - 411029, INDIA</li>
                                    <li><a href="tel:+919423009635">+91 942 300 9635</a></li>
                                    <li><a href="mailto:info@crmomni.com">info@crmomni.com</a></li>
                                </ul>
                            </div>
                            <!-- Social Box -->
                            <ul class="social-box">
                                <li class="facebook"><a href="#" class="fab fa-facebook-f"></a></li>
                                <li class="twitter"><a href="#" class="fab fa-twitter"></a></li>
                                <li class="linkedin"><a href="#" class="fab fa-linkedin-in"></a></li>
                                <li class="instagram"><a href="#" class="fab fa-instagram"></a></li>
                                <li class="youtube"><a href="#" class="fab fa-youtube"></a></li>
                            </ul>
                        </div>
                    </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <!-- END sidebar widget item -->

        <!--Scroll to top-->
        <button class="scroll-top scroll-to-target" data-target="html">
            <span class="fa fa-arrow-up"></span>
        </button>

        <!-- jequery plugins -->
        <script src="js/jquery.js"></script>
        <script src="js/popper.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/owl.js"></script>
        <script src="js/wow.js"></script>
        <script src="js/validation.js"></script>
        <script src="js/jquery.fancybox.js"></script>
        <script src="js/appear.js"></script>
        <script src="js/circle-progress.js"></script>
        <script src="js/jquery.countTo.js"></script>
        <script src="js/scrollbar.js"></script>
        <script src="js/nav-tool.js"></script>
        <script src="js/jquery.paroller.min.js"></script>
        <script src="js/tilt.jquery.js"></script>

        <!-- map script -->
        <script src="http://maps.google.com/maps/api/js?key=AIzaSyATY4Rxc8jNvDpsK8ZetC7JyN4PFVYGCGM"></script>
        <script src="js/gmaps.js"></script>
        <script src="js/map-helper.js"></script>

        <!-- main-js -->
        <script src="js/script.js"></script>

    </body><!-- End of .page_wrapper -->
</html>