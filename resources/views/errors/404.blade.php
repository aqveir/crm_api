@extends('templates.crmomni.layouts.default')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Not Found'))

@section('site_title', 'Error:404')
@section('page_title', 'Page Not Found')
@section('page_subtitle', '')

@section('page_content')
    <!-- error-section -->
    <section class="error-section centred">
        <div class="container">
            <div class="content-box">
                <figure class="error-image"><img src="images/resource/error.png" alt=""></figure>
                <h1>Sorry, The page was not found</h1>
                <div class="text">We can’t find the page that you’re<br />looking for</div>

                <div class="btn-box">
                    <a href="{{ route('crmomni.site.index', [request()->getHost()]) }}" class="theme-btn-two">Back To Home</a>
                </div>
            </div>
        </div>
    </section>
    <!-- error-section end -->
@endsection
