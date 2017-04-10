@extends('layouts.master')

@section('meta')
    <meta name="description" content="{!! $post->summary !!}">

    <!-- Schema.org para Google+ -->
    <meta itemprop="name" content="{{$post->name}}">
    <meta itemprop="description" content="{!! $post->summary !!}">
    <meta itemprop="image" content=" {{url($post->options->mainimage or '') }}">

    <!-- Open Graph para Facebook-->
    <meta property="og:title" content="{{$post->name}}"/>
    <meta property="og:type" content="articulo"/>
    <meta property="og:url" content="{{url($post->slug)}}"/>
    <meta property="og:image" content="{{url($post->options->mainimage or '')}}"/>
    <meta property="og:description" content="{!! $post->summary !!}"/>
    <meta property="og:site_name" content="{{ Setting::get('core::site-name') }}"/>
    <meta property="og:locale" content="{{locale().'_CO'}}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="{{ Setting::get('core::site-name') }}">
    <meta name="twitter:title" content="{{$post->name}}">
    <meta name="twitter:description" content="{!! $post->summary !!}">
    <meta name="twitter:creator" content="">
    <meta name="twitter:image:src" content="{{url($post->options->mainimage or '')}}">

@stop

@section('title')
    {{ $post->title }} | @parent
@stop

@section('content')

    <div class="page">
        <div class="container" id="body-wrapper">
            <div class="row">
                <div class="col-lg-12">
                <span class="linkBack">
                     <a href="{{url($category->slug)}}"><i class="glyphicon glyphicon-chevron-left"></i> Regresar</a>
                </span>
                    <div class="row">
                        <h1>{{ $post->title }}</h1>

                        <span class="date">{{format_date($post->create_at)}}</span>
                        <div class="bgimg">
                            @if($post->options)
                                <img class="image img-responsive" src="{{url($post->options->mainimage)}}"/>
                            @else
                                <img class="image img-responsive" src="{{url('module/iblog/img/post/default.jpg')}}"/>
                            @endif
                        </div>
                        <div class="content">
                            {!! $post->description !!}
                        </div>

                        @include('iblog::frontend.gallery.viewline')

                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="share-box">
                                <span>Compartir:</span>
                                <div class="share-box-wrap">
                                    <div class="share-box">
                                        <ul class="social-share">
                                            <li class="facebook_share">
                                                <a onclick="window.open('http://www.facebook.com/sharer.php?u={{$post->url }}','Facebook','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+'')">
                                                    <div class="share-item-icon"><i class="fa fa-facebook "
                                                                                    title="Facebook"></i></div>
                                                </a>
                                            </li>
                                            <li class="twitter_share">
                                                <a onclick="window.open('http://twitter.com/share?url={{ $post->url }}','Twitter share','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+'')">
                                                    <div class="share-item-icon"><i class="fa fa-twitter "
                                                                                    title="Twitter"></i></div>
                                                </a>
                                            </li>
                                            <li class="gplus_share">
                                                <a onclick="window.open('https://plus.google.com/share?url={{ $post->url }}','Google plus','width=585,height=666,left='+(screen.availWidth/2-292)+',top='+(screen.availHeight/2-333)+'')">
                                                    <div class="share-item-icon"><i class="fa fa-google-plus "
                                                                                    title="Google Plus"></i></div>
                                                </a>
                                            </li>
                                            <li class="pinterest_share">
                                                <a href="javascript:void((function()%7Bvar%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)%7D)());">
                                                    <div class="share-item-icon"><i class="fa fa-pinterest "
                                                                                    title="Pinterest"></i></div>
                                                </a>
                                            </li>
                                            <li class="linkedin_share">
                                                <a onclick="window.open('http://www.linkedin.com/shareArticle?mini=true&amp;url={{ $post->url }}','Linkedin','width=863,height=500,left='+(screen.availWidth/2-431)+',top='+(screen.availHeight/2-250)+'')">
                                                    <div class="share-item-icon"><i class="fa fa-linkedin "
                                                                                    title="Linkedin"></i></div>
                                                </a>
                                            </li>
                                            <li class="whatsapp_share">
                                                <!-- WhatsApp Share Button-->
                                                <a href="whatsapp://send?text={{ $post->url }}"
                                                   data-action="share/whatsapp/share">
                                                    <div class="share-item-icon"><span class="fa-stack"><i
                                                                    class="fa fa-square fa-stack-2x"></i><i
                                                                    class="fa fa-whatsapp fa-stack-1x"></i></span></div>
                                                </a>
                                                <!-- /WhatsApp Share Button  -->
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <ul class="post-nav">
                                <?php if ($previous = $post->present()->previous): ?>
                                <li class="post-prev">
                                    <a href="{{ route(locale() . '.iblog.'.$category->slug.'.slug', [$previous->slug]) }}"><i
                                                class="fa fa-angle-left"></i> </a>
                                </li>
                                <?php endif; ?>
                                @if ($next = $post->present()->next)
                                    <li class="post-next">
                                        <a href="{{ route(locale() . '.iblog.'.$category->slug.'.slug', [$next->slug]) }}"><i
                                                    class="fa fa-angle-right"></i> Siguiente </a>
                                    </li>
                                @else
                                    <li class="post-next">
                                        <a href="{{ route(locale() . '.iblog.'.$category->slug.'.slug', [$previous->slug]) }}">anterior </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@stop
