@extends('templates.crmomni.layouts.default')

@section('site_title', 'Core Team')
@section('page_title', 'Core Team')
@section('page_subtitle', '')

@php
    $listMembers = [
        [
            'name' => 'Amit Dhongde',
            'avatar' => asset('images/resource/team-12.jpg'),
            'role' => 'Director',
            'introduction' => 'Co-founder of EllaiSys and involved in successful incubation of multiple firms. Hand\'s on with technology. A proven track record of working with Global firms in IT Consulting and Delivery Excellence.',
            'socials' => [
                'linkedin' => 'https://www.linkedin.com/in/amitdhongde/',
                'twitter' => 'https://twitter.com/amitdhongde/'
            ]
        ],
        [
            'name' => 'Geeta Godbole',
            'avatar' => 'https://i1.wp.com/www.ellaisys.com/wp-content/uploads/2020/04/geetagodbole.jpg',
            'role' => 'Director',
            'introduction' => 'Majorly worked in Cloud Technologies, DevOps & Web development for enterprises. Formulating Migration & Transformation strategies for IT landscape has been the forte.',
            'socials' => [
                'linkedin' => 'https://www.linkedin.com/in/geeta-godbole-5b52b51/',
                'twitter' => 'https://twitter.com/amitdhongde/'
            ]
        ]
    ];
@endphp

@section('page_content')
    <!-- team-style-two -->
    <section class="team-style-two">
        <div class="container">
            <div class="row">
                @foreach ($listMembers as $member)
                <div class="col-6 inner-content">
                    <div class="inner-content">
                        <div class="team-block-two">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 image-column">
                                    <figure class="image-box"><a href="#"><img src="{{ $member['avatar'] }}" alt="{{ $member['name'] }}" class="rounded d-block" style="width:270px; height:332px; object-fit:cover;"></a></figure>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 content-column d-flex">
                                    <div class="content-box my-auto w-75">
                                        <span class="role">{{ $member['role'] }}</span>
                                        <h4><a href="#">{{ $member['name'] }}</a></h4>
                                        <div class="text text-justify" style="line-height: 20px;">{!! $member['introduction'] !!}</div>

                                        {{-- check socials --}}
                                        @if(array_key_exists('socials', $member))
                                            <ul class="team-social {{ ($member['socials'])?'d-block':'d-none'}}">
                                                @if(array_key_exists('linkedin', $member['socials']))
                                                    <li class="social_icons"><a href="{{ $member['socials']['linkedin'] }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                                                @endif

                                                @if(array_key_exists('facebook', $member['socials']))
                                                    <li class="social_icons"><a href="{{ $member['socials']['facebook'] }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                                @endif
                                                
                                                @if(array_key_exists('twitter', $member['socials']))
                                                    <li class="social_icons"><a href="{{ $member['socials']['twitter'] }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                                @endif
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- team-style-two end -->
@endsection