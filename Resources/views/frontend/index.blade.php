@extends('layouts.master')

@section('meta')
    <meta name="description" content="@if(!empty($category->description)){!!$category->description!!}@endif">

    <!-- Schema.org para Google+ -->
    <meta itemprop="name" content="{{$category->title}}">
    <meta itemprop="description" content="@if(! empty($category->description)){!!$category->description  !!} @endif">
    <meta itemprop="image" content=" @if(! empty($category->options->mainimage)){{url($category->options->mainimage) }} @endif">

    <!-- Open Graph para Facebook-->
    <meta property="og:title" content="{{$category->title}}" />
    <meta property="og:type" content="categoria" />
    <meta property="og:url" content="{{url($category->slug)}}" />
    <meta property="og:image" content="@if(!empty($category->options->mainimage)){{url($category->options->mainimage) }} @endif" />
    <meta property="og:description" content="@if(!empty($category->description)){!!$category->description  !!}@endif" />
    <meta property="og:site_name" content="" />
    <meta property="og:locale" content="{{locale().'_CO'}}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="">
    <meta name="twitter:title" content="{{$category->title}}">
    <meta name="twitter:description" content="{@if(!empty($category->description)){!!$category->description  !!}@endif">
    <meta name="twitter:creator" content="">
    <meta name="twitter:image:src" content="@if(!empty($category->options->mainimage)){{url($category->options->mainimage) }} @endif">

@stop

@section('title')
    {{$category->title}} | @parent
@stop

@section('content')

<div class="page blog blog-revista blog-category-{{$category->slug}} blog-category-{{$category->id}}">
<div class="container">

    <div class="row fondo1 sombra-interna">

        <div class="col-xs-12">
            <div class="titulo-2">
                <h2>
                    <i class="fa fa-caret-right" aria-hidden="true"></i>
                    {{$category->title}}
                </h2>
            </div>
        </div>


        <div class="col-xs-12 col-sm-8 category-body-1 column1">
        <div class="row">
                @if (!empty($posts))

                <?php $cont=0; ?>

                @foreach($posts as $post)
                            <!-- Blog Post -->
                    <div class="col-xs-6 col-sm-3 contend post post{{$post->id}}">
                        <div class="bg-imagen">
                            <a href="{{ $post->url }}">
                            @if($post->options)
                                <img class="image img-responsive" src="{{url(str_replace('.jpg','_mediumThumb.jpg',$post->options->mainimage))}}"/>
                            @else
                                <img class="image img-responsive" src="{{url('module/iblog/img/post/default.jpg')}}"/>
                            @endif
                            </a>
                        </div>
                        <div class="content">
                                <a href="{{ $post->url }}">{{ $post->title }}</a>
                                <h4> {{ $post->title }}</h4>
                                <p>{!! $post->summary !!} ...</p>
                            </a>

                            <a class="btn btn-primary post-link" href="{{ $post->url }}">Ver Mas<span class="glyphicon glyphicon-chevron-right"></span></a>
                        </div>
                    </div>

                    <?php $cont++; ?>

                    @if($cont%2==0)
                        <div class="clearfix"></div>
                    @endif

                @endforeach

                <div class="clearfix"></div>

                <div class="pagination paginacion-blog row">
                    <div class="pull-right">
                        {{ $posts->links() }}
                    </div>
                </div>

                @endif
        </div>
        </div>

        <div class="col-xs-12 col-sm-3 col-sm-offset-1 column2">

            <div class="sidebar-revista">

                <div class="cate">
                    <h3>Categorias</h3>

                    <div class="listado-cat">
                        <ul>
                           @php
                            $categories=get_categories();
                            @endphp

                            @if(isset($categories))
                                @foreach($categories as $index=>$category)
                                    <li><a href="{{url($category->slug)}}">{{$category->title}}</a></li>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                </div>

            </div>

        </div>

            </div>

        </div>

    </div>
@stop