@extends('templates.crmomni.layouts.default')

@section('site_title', 'About')
@section('page_title', 'About Us')
@section('page_subtitle', '')

@section('page_content')
    <!-- our-history -->
    <section class="our-history">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12 content-column">
                    <div id="content_block_53">
                        <div class="content-box wow fadeInLeft" data-wow-delay="00ms" data-wow-duration="1500ms">
                            <div class="sec-title"><h2>Our History</h2></div>
                            <div class="text">
                                <p>Over the years we have grown in all aspects — and continue to every day — but our goals have remained the same. Have fun while working with the best technology at hand. Design and create the finest product we can. Compete with the top in the industry. Learn from the best.</p>
                                <p>Focus on the essential. Cultivate openness and respect in all communication. Be friends with one another. Learn constantly. Share what we know.</p>
                            </div>
                            <h5>M. Ronica, CEO Colin.</h5>
                            <figure class="signatur"><img src="{{ asset('images/icons/signatur.png') }}" alt=""></figure>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 image-column">
                    <div id="image_block_47">
                        <div class="image-box js-tilt">
                            <figure class="image wow slideInRight" data-wow-delay="00ms" data-wow-duration="1500ms"><img src="{{ asset('images/resource/illustration-41.png') }}" alt=""></figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- our-history end -->

    @include('templates.crmomni.widgets.widget-section-video')

    @include('templates.crmomni.partials.subscribe-newsletter')
@endsection