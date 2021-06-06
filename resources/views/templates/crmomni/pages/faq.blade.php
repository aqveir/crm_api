@extends('templates.crmomni.layouts.default')

@section('site_title', 'FAQ')
@section('page_title', 'Frequently Asked Questions')
@section('page_subtitle', 'Reach out to the world’s most reliable IT services.')

@section('page_content')
    <!-- faq-section -->
    <section class="faq-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-12 sidebar-column">
                    <div class="faq-sidebar">
                        <h3>Quick Navigation</h3>
                        <div class="online-purchase"><a href="#">Purchasing Online</a></div>
                        <ul class="list-item">
                            <li><a href="#">Returns</a></li>
                            <li><a href="#">Pricing & Support</a></li>
                            <li><a href="#">Care & Repair</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-8 col-md-12 col-sm-12 content-column">
                    <div class="faq-content">
                        <div class="sec-title"><h2>How To Purchase</h2></div>
                        <ul class="accordion-box">
                            <li class="accordion block">
                                <div class="acc-btn">
                                    <div class="icon-outer"><i class="fas fa-plus"></i></div>
                                    <h4>How do I repair an item?</h4>
                                </div>
                                <div class="acc-content">
                                    <div class="content">
                                        <div class="text">Why I say old chap that is spiffing pukka, bamboozled wind up bugger buggered zonked hanky panky a blinding shot the little rotter, bubble and squeak vagabond cheeky bugger at public school pardon you bloke the BBC. Tickety-boo Elizabeth plastered matie.!</div>
                                    </div>
                                </div>
                            </li>
                            <li class="accordion block">
                                <div class="acc-btn active">
                                    <div class="icon-outer"><i class="fas fa-plus"></i></div>
                                    <h4>Where can I find instructions on how to use my watch?</h4>
                                </div>
                                <div class="acc-content current">
                                    <div class="content">
                                        <div class="text">Why I say old chap that is spiffing pukka, bamboozled wind up bugger buggered zonked hanky panky a blinding shot the little rotter, bubble and squeak vagabond cheeky bugger at public school pardon you bloke the BBC. Tickety-boo Elizabeth plastered matie.!</div>
                                    </div>
                                </div>
                            </li>
                            <li class="accordion block">
                                 <div class="acc-btn">
                                    <div class="icon-outer"><i class="fas fa-plus"></i></div>
                                    <h4>Is there a warranty on my item?</h4>
                                </div>
                                <div class="acc-content">
                                    <div class="content">
                                        <div class="text">Why I say old chap that is spiffing pukka, bamboozled wind up bugger buggered zonked hanky panky a blinding shot the little rotter, bubble and squeak vagabond cheeky bugger at public school pardon you bloke the BBC. Tickety-boo Elizabeth plastered matie.!</div>
                                    </div>
                                </div>
                            </li>
                            <li class="accordion block">
                                 <div class="acc-btn">
                                    <div class="icon-outer"><i class="fas fa-plus"></i></div>
                                    <h4>Where to look at your rates?</h4>
                                </div>
                                <div class="acc-content">
                                    <div class="content">
                                        <div class="text">Why I say old chap that is spiffing pukka, bamboozled wind up bugger buggered zonked hanky panky a blinding shot the little rotter, bubble and squeak vagabond cheeky bugger at public school pardon you bloke the BBC. Tickety-boo Elizabeth plastered matie.!</div>
                                    </div>
                                </div>
                            </li>
                            <li class="accordion block">
                                 <div class="acc-btn">
                                    <div class="icon-outer"><i class="fas fa-plus"></i></div>
                                    <h4>On the other hand the strengthening?</h4>
                                </div>
                                <div class="acc-content">
                                    <div class="content">
                                        <div class="text">Why I say old chap that is spiffing pukka, bamboozled wind up bugger buggered zonked hanky panky a blinding shot the little rotter, bubble and squeak vagabond cheeky bugger at public school pardon you bloke the BBC. Tickety-boo Elizabeth plastered matie.!</div>
                                    </div>
                                </div>
                            </li>
                            <li class="accordion block">
                                 <div class="acc-btn">
                                    <div class="icon-outer"><i class="fas fa-plus"></i></div>
                                    <h4>And development of the structure?</h4>
                                </div>
                                <div class="acc-content">
                                    <div class="content">
                                        <div class="text">Why I say old chap that is spiffing pukka, bamboozled wind up bugger buggered zonked hanky panky a blinding shot the little rotter, bubble and squeak vagabond cheeky bugger at public school pardon you bloke the BBC. Tickety-boo Elizabeth plastered matie.!</div>
                                    </div>
                                </div>
                            </li>
                            <li class="accordion block">
                                 <div class="acc-btn">
                                    <div class="icon-outer"><i class="fas fa-plus"></i></div>
                                    <h4>Largely determines the creation?</h4>
                                </div>
                                <div class="acc-content">
                                    <div class="content">
                                        <div class="text">Why I say old chap that is spiffing pukka, bamboozled wind up bugger buggered zonked hanky panky a blinding shot the little rotter, bubble and squeak vagabond cheeky bugger at public school pardon you bloke the BBC. Tickety-boo Elizabeth plastered matie.!</div>
                                    </div>
                                </div>
                            </li>
                            <li class="accordion block">
                                 <div class="acc-btn">
                                    <div class="icon-outer"><i class="fas fa-plus"></i></div>
                                    <h4>Substantial financial?</h4>
                                </div>
                                <div class="acc-content">
                                    <div class="content">
                                        <div class="text">Why I say old chap that is spiffing pukka, bamboozled wind up bugger buggered zonked hanky panky a blinding shot the little rotter, bubble and squeak vagabond cheeky bugger at public school pardon you bloke the BBC. Tickety-boo Elizabeth plastered matie.!</div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- faq-section end -->

    @include('templates.crmomni.partials.form-ask-question')

    @include('templates.crmomni.partials.subscribe-newsletter')
@endsection