@extends('layouts.master')

@section('meta')
    <meta name="description" content="@if(!empty($category->description)){!!$category->description!!}@endif">

    <!-- El canónico siempre debe ser la página actual -->
    <link rel="canonical" href="{{url($category->slug)}}" />

    <!-- Schema.org para Google+ -->
    <meta itemprop="name" content="{{$category->title}}">
    <meta itemprop="description" content="@if(! empty($category->description)){!!$category->description  !!} @endif">
    <meta itemprop="image" content=" @if(! empty($category->options->mainimage)){{URL($category->options->mainimage) }} @endif">

    <!-- Open Graph para Facebook-->
    <meta property="og:title" content="{{$category->title}}" />
    <meta property="og:type" content="categoria" />
    <meta property="og:url" content="{{url($category->slug)}}" />
    <meta property="og:image" content="@if(!empty($category->options->mainimage)){{URL($category->options->mainimage) }} @endif" />
    <meta property="og:description" content="@if(!empty($category->description)){!!$category->description  !!}@endif" />
    <meta property="og:site_name" content="" />
    <meta property="og:locale" content="{{locale().'_CO'}}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="">
    <meta name="twitter:title" content="{{$category->title}}">
    <meta name="twitter:description" content="{@if(!empty($category->description)){!!$category->description  !!}@endif">
    <meta name="twitter:creator" content="">
    <meta name="twitter:image:src" content="@if(!empty($category->options->mainimage)){{URL($category->options->mainimage) }} @endif">

@stop

@section('title')
    {{$category->title}} | @parent
@stop

@section('content')



    <div class="page blog">
        <div class="container">
            <div class="row">

                <div class="row">
                    <div class="col-xs-12">
                        <ol class="breadcrumb">
                            <li><a href="/">Inicio</a></li>
                            <li>{{$category->title}}</li>
                        </ol>
                    </div>
                </div>

                <!-- Blog Entries Column -->
                <div class="col-xs-12 col-md-12 category-body-1">

                    <h1 class="page-header">{{$category->title}}</h1>


                    @if (!empty($posts))

                    <?php $cont=0; ?>

                    @foreach($posts as $post)
                            <!-- Blog Post -->
                    <div class="col-xs-6 col-sm-3 contend post post{{$post->id}}">
                        <div class="bg-imagen">

                            @if($post->options)

                                <img class="image img-responsive" src="{{URL(str_replace('.jpg','_mediumThumb.jpg',$post->options->mainimage))}}"/>
                            @endif
                        </div>
                        <div class="content">
                            <div class="date"><span class="glyphicon glyphicon-time"></span>{{ $post->created_at->format('d / w / Y') }}</div>
                            <h4>
                                <a href="{{ URL::route($currentLocale . '.iblog.'.$category->slug.'.slug', [$post->slug]) }}">{{ $post->title }}</a>
                            </h4>

                            <p>{!! $post->summary !!} ...</p>


                            <a class="btn btn-primary post-link" href="{{ URL::route($currentLocale . '.iblog.'.$category->slug.'.slug', [$post->slug]) }}">Ver Mas<span class="glyphicon glyphicon-chevron-right"></span></a>
                        </div>
                    </div>

                    <?php $cont++; ?>

                    @if($cont%4==0)
                        <div class="clearfix"></div>
                    @endif

                    @if($cont%2==0)
                        <div class="clearfix visible-xs-block"></div>
                    @endif

                    @endforeach

                    <div class="clearfix"></div>
                    <div class="paginacion-blog row">
                        {{ $posts->links() }}
                    </div>

                    @endif
                </div>

            </div>

        </div>

    </div>
@stop