<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

        <title>@yield('title')</title>

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
    <header class="main-header">
        <div class="outer-container">
            <div class="container">
                <div class="main-box clearfix">
                    <div class="logo-box pull-left">
                        <figure class="logo"><a href="{{ url('/') }}"><img src="images/logo-3.png" alt=""></a></figure>
                    </div>

                    @include('templates.crmomni.partials.layout-menu-header')
                </div>
            </div>
        </div>

        <!--sticky Header-->
        <div class="sticky-header">
            <div class="container clearfix">
                <figure class="logo-box"><a href="index.html"><img src="images/small-logo.png" alt=""></a></figure>
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
    <div class="mobile-menu d-block d-md-none d-lg-none d-xl-none">
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

    <!-- banner-section -->
    <section class="banner-style-13">
        <div class="image-shap wow slideInLeft" data-wow-delay="00ms" data-wow-duration="1500ms" style="background-image: url(images/icons/shap-9.png);"></div>
        <div class="anim-icons">
            <div class="icon icon-1"></div>
            <div class="icon icon-2"></div>
        </div>

        <div class="image-layer" style="background-image: url(images/icons/banner-bg-5.png);"></div>
        
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12 col-sm-12 content-column">
                    <div class="content-box">
                        <h1>Empower your business with the AI Powered<br /><span>Omni CRM</span></h1>
                        <div class="text">Omni CRM helps you engage your leads and customers, get insights about your business, build a scalable sales process, and grow your business faster</div>
                        <div class="mail-box">
                            <a href="{{ url('/register') }}" class="btn btn-crmomni-primary">Start Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 image-column">
                    <div class="image-box  js-tilt">
                        <figure class="image clearfix wow slideInLeft" data-wow-delay="00ms" data-wow-duration="1500ms"><img src="images/resource/illustration-20.png" alt=""></figure>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- banner-section end -->

    <!-- award-section -->
    <section class="award-section d-none" hidden>
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-md-12 col-sm-12 offset-lg-1 content-column">
                    <div class="content-box">
                        <figure class="award-image"><img src="images/icons/award-1.png" alt=""></figure>
                        <div class="text">Rated the Best CRM software in PCMag’s</div>
                        <h1>Business Choice Awards 2019</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- award-section end -->

    <!-- chooseus-section -->
    <section class="chooseus-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 inner-column">
                    <div class="inner-box">
                        <h3>Ease of use</h3>
                        <ul class="list-item clearfix">
                            <li>Simple and straightforward user interface with a minimal learning curve.</li>
                            <li>Ability to set up and start selling right from day one.</li>
                            <li>Easy migration from spreadsheets and other CRM systems.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 inner-column">
                    <div class="inner-box">
                        <h3>Feature-rich</h3>
                        <ul class="list-item clearfix">
                            <li>Best-in-class sales automation features for automating your entire business process.</li>
                            <li>Spend more time interacting with your customers and less on manually entering data.</li>
                            <li>Powerful analytics and reporting that helps you make smarter business decisions.</li>
                            <li>Empowered with AI based solutions that automate your work and secure your data.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 inner-column">
                    <div class="inner-box">
                        <h3>Customizable</h3>
                        <ul class="list-item clearfix">
                            <li>Completely customize your CRM experience without writing a single line of code.</li>
                            <li>Effortless integration with third-party business apps that you use daily.</li>
                            <li>Easily adaptable to meet different industry specific needs.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- chooseus-section end -->

    <!-- feature-style-11 -->
    <section class="feature-style-11">
        <div class="container">
            <div class="sec-title center">
                <h2>Features at a Glance<br />Date Assessment Soloution.</h2>
            </div>
            <div class="inner-content">
                <div class="inner-box">
                    <div class="row align-items-center">
                        <div class="col-lg-7 col-md-12 col-sm-12 image-column">
                            <div id="image_block_34">
                                <div class="image-box wow slideInLeft" data-wow-delay="00ms" data-wow-duration="1500ms">
                                    <div class="bg-layer" style="background-image: url(images/icons/pattern-15.png);"></div>
                                    <figure class="image clearfix js-tilt"><img src="images/resource/illustration-21.png" alt=""></figure>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-12 col-sm-12 content-column">
                            <div id="content_block_34">
                                <div class="content-box wow fadeInRight" data-wow-delay="00ms" data-wow-duration="1500ms">
                                    <div class="top-title"><i class="fas fa-angle-double-right"></i>Multi-Channel</div>
                                    <div class="sec-title"><h2>Engage with your customer</h2></div>
                                    <ul class="list-item clearfix">
                                        <li>Multichannel communication through telephone, email, live chat, and social media.</li>
                                        <li>Get real-time notifications when customers interact with your business.</li>
                                        <li>Measure the effectiveness of your email campaigns through email analytics.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="inner-box">
                    <div class="row align-items-center">
                        <div class="col-lg-5 col-md-12 col-sm-12 content-column">
                            <div id="content_block_34">
                                <div class="content-box wow fadeInLeft" data-wow-delay="00ms" data-wow-duration="1500ms">
                                    <div class="top-title"><i class="fas fa-angle-double-right"></i>Automation</div>
                                    <div class="sec-title"><h2>Automate routine tasks</h2></div>
                                    <ul class="list-item clearfix">
                                        <li>Create pre-defined conditions for every incoming lead, and automate your lead nurturing process.</li>
                                        <li>Automate every aspect of your business and cut out time-intensive, repetitive tasks.</li>
                                        <li>Implement scoring rules that help you prioritize your work so you can concentrate on the right set of leads, contacts, accounts, and deals.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-12 col-sm-12 image-column">
                            <div id="image_block_34">
                                <div class="image-box wow slideInRight" data-wow-delay="00ms" data-wow-duration="1500ms">
                                    <div class="bg-layer" style="background-image: url(images/icons/pattern-16.png);"></div>
                                    <figure class="image clearfix js-tilt"><img src="images/resource/illustration-22.png" alt=""></figure>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="inner-box">
                    <div class="row align-items-center">
                        <div class="col-lg-7 col-md-12 col-sm-12 image-column">
                            <div id="image_block_34">
                                <div class="image-box wow slideInLeft" data-wow-delay="00ms" data-wow-duration="1500ms">
                                    <div class="bg-layer" style="background-image: url(images/icons/pattern-17.png);"></div>
                                    <figure class="image clearfix js-tilt"><img src="images/resource/illustration-23.png" alt=""></figure>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-12 col-sm-12 content-column">
                            <div id="content_block_34">
                                <div class="content-box wow fadeInRight" data-wow-delay="00ms" data-wow-duration="1500ms">
                                    <div class="top-title"><i class="fas fa-angle-double-right"></i>Customization</div>
                                    <div class="sec-title"><h2>Make CRM your own</h2></div>
                                    <ul class="list-item clearfix">
                                        <li>Customize your CRM interface to meet the specific requirements of your organization.</li>
                                        <li>Add custom modules, buttons, and fields to manage your unique needs.</li>
                                        <li>Manage multiple business processes within your CRM using Layouts.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="inner-box">
                    <div class="row align-items-center">
                        <div class="col-lg-5 col-md-12 col-sm-12 content-column">
                            <div id="content_block_34">
                                <div class="content-box  wow fadeInLeft animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                                    <div class="top-title"><i class="fas fa-angle-double-right"></i>Analytics</div>
                                    <div class="sec-title"><h2>Reports and insights</h2></div>
                                    <ul class="list-item clearfix">
                                        <li>Get powerful, real-time analytics that can help you make smarter business decisions.</li>
                                        <li>Measure and manage your organization’s territory-wide sales performance.</li>
                                        <li>Track your key performance indicators, including current trends and future predictions, based on the wealth of data captured in your CRM.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-12 col-sm-12 image-column">
                            <div id="image_block_34">
                                <div class="image-box wow slideInRight" data-wow-delay="00ms" data-wow-duration="1500ms">
                                    <div class="bg-layer" style="background-image: url(images/icons/pattern-18.png);"></div>
                                    <figure class="image clearfix js-tilt"><img src="images/resource/illustration-24.png" alt=""></figure>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="inner-box">
                    <div class="row align-items-center">
                        <div class="col-lg-7 col-md-12 col-sm-12 image-column">
                            <div id="image_block_34">
                                <div class="image-box wow slideInLeft" data-wow-delay="00ms" data-wow-duration="1500ms">
                                    <div class="bg-layer" style="background-image: url(images/icons/pattern-19.png);"></div>
                                    <figure class="image clearfix js-tilt"><img src="images/resource/illustration-25.png" alt=""></figure>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-12 col-sm-12 content-column">
                            <div id="content_block_34">
                                <div class="content-box wow fadeInRight" data-wow-delay="00ms" data-wow-duration="1500ms">
                                    <div class="top-title"><i class="fas fa-angle-double-right"></i>AIl for Sales</div>
                                    <div class="sec-title"><h2>Conversational AI for Sales</h2></div>
                                    <ul class="list-item clearfix">
                                        <li>Your AI-powered sales assistant, can help you find any information you need from your CRM data, almost instantly.</li>
                                        <li>Appway improves the quality of your existing data so that you can get additional customer insights.</li>
                                        <li>Receive intelligent alerts, task reminders, and suggestions for the best times to contact your leads based on your past successful interactions.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- feature-style-11 end -->

    <!-- crm-programming -->
    <section class="crm-programming">
        <div class="image-layer" style="background-image: url(images/icons/crm-bg.png);"></div>
        <div class="container">
            <div class="sec-title center"><h2>CRM Programming That Gives<br />You Prompt Outcomes</h2></div>
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-12 single-column">
                    <div class="single-item wow slideInLeft animated" data-wow-delay="900ms" data-wow-duration="1500ms">
                        <div class="progress-box">
                            <div class="piechart" data-fg-color="#2eb100" data-value=".75">
                                <span>75</span>
                            </div>
                        </div>
                        <div class="text">Improvement in lead conversion rates</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 single-column">
                    <div class="single-item wow slideInLeft animated" data-wow-delay="600ms" data-wow-duration="1500ms">
                        <div class="progress-box">
                            <div class="piechart" data-fg-color="#ff0000" data-value=".50">
                                <span>50</span>
                            </div>
                        </div>
                        <div class="text">Revenue increase per sales person.</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 single-column">
                    <div class="single-item wow slideInLeft animated" data-wow-delay="300ms" data-wow-duration="1500ms">
                        <div class="progress-box">
                            <div class="piechart" data-fg-color="#393e95" data-value=".24">
                                <span>24</span>
                            </div>
                        </div>
                        <div class="text">shorter sales cycle</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 single-column">
                    <div class="single-item wow slideInLeft animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                        <div class="progress-box">
                            <div class="piechart" data-fg-color="#ff8500" data-value=".27">
                                <span>27</span>
                            </div>
                        </div>
                        <div class="text">Improvement in customer retention</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- crm-programming end -->

    <!-- pricing-style-four -->
    <section class="pricing-style-four" hidden>
        <div class="container">
            <div class="sec-title center"><h2>Unmatched Features With<br />Transparent Pricing</h2></div>
            <div class="row">
                <div class="col-lg-9 col-md-12 col-sm-12 pricing-column">
                    <div class="pricing-inner clearfix">
                        <div class="pricing-table">
                            <div class="table-header">
                                <h3 class="title">Start</h3>
                                <h1 class="price"><span>$</span>200</h1>
                                <div class="text">Per Month</div>
                            </div>
                            <div class="table-content">
                                <ul class="clearfix">
                                    <li><i class="fas fa-times"></i></li>
                                    <li><i class="fas fa-times"></i></li>
                                    <li><i class="fas fa-times"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                </ul>
                            </div>
                            <div class="table-footer"><a href="#">Choose Plan</a></div>
                        </div>
                        <div class="pricing-table">
                            <div class="table-header">
                                <h3 class="title">PRO</h3>
                                <h1 class="price"><span>$</span>320</h1>
                                <div class="text">Per Month</div>
                            </div>
                            <div class="table-content">
                                <ul class="clearfix">
                                    <li><i class="fas fa-times"></i></li>
                                    <li><i class="fas fa-times"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                </ul>
                            </div>
                            <div class="table-footer"><a href="#">Choose Plan</a></div>
                        </div>
                        <div class="pricing-table">
                            <div class="table-header">
                                <h3 class="title">Premium</h3>
                                <h1 class="price"><span>$</span>450</h1>
                                <div class="text">Per Month</div>
                            </div>
                            <div class="table-content">
                                <ul class="clearfix">
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                    <li><i class="fas fa-check"></i></li>
                                </ul>
                            </div>
                            <div class="table-footer"><a href="#">Choose Plan</a></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12 col-sm-12 feature-column">
                    <div class="feature-inner">
                        <h2>Comparison Features</h2>
                        <ul class="list clearfix">
                            <li>Adding time manually</li>
                            <li>Timeline</li>
                            <li>Tracking time</li>
                            <li>Adding time manually</li>
                            <li>Tracking time</li>
                            <li>Adding time manually</li>
                            <li>Keyboard shortcuts</li>
                            <li>Tags</li>
                            <li>Time formats</li>
                            <li>Pomodoro timer</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- pricing-style-four end -->

    <!-- testimonial-style-11 -->
    <section class="testimonial-style-11" hidden>
        <div class="anim-icons">
            <div class="icon icon-1"><img src="images/icons/anim-icon-12.png" alt=""></div>
            <div class="icon icon-2"><img src="images/icons/anim-icon-13.png" alt=""></div>
            <div class="icon icon-3"><img src="images/icons/anim-icon-14.png" alt=""></div>
        </div>
        <div class="container">
            <div class="inner-container">
                <div class="bg-layer" style="background-image: url(images/icons/testimonial-bg-7.png);"></div>
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-12 col-sm-12 title-column">
                        <div class="sec-title"><h2>People Experience Share With Us</h2></div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 inner-column">
                        <div class="testimonial-inner">
                            <div class="testimonial-carousel-3 owl-carousel owl-theme owl-dots-none">
                                <div class="testimonial-content">
                                    <figure class="image-box"><img src="images/resource/testimonial-12.png" alt=""></figure>
                                    <div class="text">“We don't take ourselves too seriously, but seriously enough to ensure we're creating the best product and experience for our customers. I feel like Help Scout does the same.”</div>
                                    <div class="author-info">
                                        <h3 class="name">TeamSnap</h3>
                                        <span class="designation">VP of Customer Experience</span>
                                    </div>
                                </div>
                                <div class="testimonial-content">
                                    <figure class="image-box"><img src="images/resource/testimonial-12.png" alt=""></figure>
                                    <div class="text">“We don't take ourselves too seriously, but seriously enough to ensure we're creating the best product and experience for our customers. I feel like Help Scout does the same.”</div>
                                    <div class="author-info">
                                        <h3 class="name">TeamSnap</h3>
                                        <span class="designation">VP of Customer Experience</span>
                                    </div>
                                </div>
                                <div class="testimonial-content">
                                    <figure class="image-box"><img src="images/resource/testimonial-12.png" alt=""></figure>
                                    <div class="text">“We don't take ourselves too seriously, but seriously enough to ensure we're creating the best product and experience for our customers. I feel like Help Scout does the same.”</div>
                                    <div class="author-info">
                                        <h3 class="name">TeamSnap</h3>
                                        <span class="designation">VP of Customer Experience</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- testimonial-style-11 end -->

    <!-- main-footer -->
    <footer class="main-footer">
        <div class="image-layer" style="background-image: url(images/icons/footer-bg-4.png);"></div>
        <div class="container">
            <div class="footer-top">
                <div class="widget-section">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 footer-column">
                            <div class="about-widget footer-widget">
                                <figure class="footer-logo"><a href="index.html"><img src="images/footer-logo.png" alt=""></a></figure>
                                <div class="text">Lorem ipsum dolor sit consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim lorem sed do eiusmod.</div>
                                <ul class="social-links">
                                    <li><h6>Follow Us :</h6></li>
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-skype"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                            <div class="links-widget footer-widget">
                                <h4 class="widget-title">Support</h4>
                                <div class="widget-content">
                                    <ul class="list clearfix">
                                        <li><a href="#">Contact Us</a></li>
                                        <li><a href="#">Submit a Ticket</a></li>
                                        <li><a href="#">Visit Knowledge Base</a></li>
                                        <li><a href="#">Support System</a></li>
                                        <li><a href="#">Refund Policy</a></li>
                                        <li><a href="#">Professional Services</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6 col-sm-12 footer-column">
                            <div class="links-widget footer-widget">
                                <h4 class="widget-title">Links</h4>
                                <div class="widget-content">
                                    <ul class="list clearfix">
                                        <li><a href="{{ url('/') }}">Home</a></li>
                                        <li><a href="#">Services</a></li>
                                        <li><a href="#">Price Plan</a></li>
                                        <li><a href="#">Testimonials</a></li>
                                        <li><a href="#">News</a></li>
                                        <li><a href="{{ url('/contact') }}">Contact Us</a></li>
                                        <li><a href="{{ url('/faq') }}">FAQs</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                            @include('templates.crmomni.widgets.widget-contact', ['showSocialIcons' => false])
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="copyright d-flex p-2">
                    <div class="flex-grow-0">
                        &copy; {{ now()->year }} 
                        <a target="_blank" href="https://www.ellaisys.com/">Ellai Information Systetms Pvt Ltd</a>. All rights reserved
                    </div>
                    <div class="flex-fill">
                        <ul class="footer-nav list-inline pull-right">
                            <li class="list-inline-item"><a href="{{ url('/terms_conditions') }}">Terms &amp; Conditions</a></li>
                            <li class="list-inline-item"><a href="{{ url('/privacy_policy') }}">Privacy Policy</a></li>
                            <li class="list-inline-item"><a href="{{ url('/legal') }}">Legal</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- main-footer end -->

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
    <script src="js/jquery.paroller.min.js"></script>
    <script src="js/tilt.jquery.js"></script>

    <!-- main-js -->
    <script src="js/script.js"></script>

    </body><!-- End of .page_wrapper -->
</html>
