@extends('layouts.master')

@section('meta')
    <meta name="description" content="@if(!empty($category->description)){!!$category->description!!}@endif">
    <!-- Schema.org para Google+ -->
    <meta itemprop="name" content="{{$category->title}}">
    <meta itemprop="description" content="@if(! empty($category->description)){!!$category->description!!} @endif">
    <meta itemprop="image"
          content=" @if(! empty($category->options->mainimage)){{url($category->options->mainimage)}} @endif">

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
                    @if (count($posts) !=0)
                        @php $cont = 0; @endphp
                        @foreach($posts as $post)
                                <div class="col-xs-6 col-sm-4 contend post post{{$post->id}}">
                                    <div class="bg-imagen">
                                        <a href="{{$post->url}}">
                                            @if(isset($post->options->mainimage)&&!empty($post->options->mainimage))
                                                <img class="image img-responsive"
                                                     src="{{url(str_replace('.jpg','_mediumThumb.jpg',$post->options->mainimage))}}"
                                                     alt="{{$post->title}}"/>
                                            @else
                                                <img class="image img-responsive"
                                                     src="{{url('modules/iblog/img/post/default.jpg')}}"
                                                     alt="{{$post->title}}"/>
                                            @endif
                                        </a>
                                    </div>
                                    @foreach($post->categories as $category)
                                    {{$category->title}}
                                    @endforeach
                                    <div class="content">
                                        <a href="{{$post->url}}"><h2>{{$post->title}}</h2></a>
                                        <p>{!! $post->summary!!}</p>
                                        <a class="btn btn-primary post-link" href="{{$post->url}}">Ver Mas<span
                                                    class="glyphicon glyphicon-chevron-right"></span></a>
                                    </div>
                                </div>
                                @php $cont++; @endphp
                                @if($cont%3==0)
                                    <div class="clearfix" style="margin:10px 0"></div>
                                @endif
                            @endforeach
                            <div class="clearfix"></div>
                            <div class="pagination paginacion-blog row">
                                <div class="pull-right">
                                    {{$posts->links()}}
                                </div>
                            </div>
                         @else
                        <div class="col-xs-12 con-sm-12">
                            <div class="white-box">
                                <h3>Ups... :(</h3>
                                <h1>404 Post no encontrado</h1>
                                <hr>
                                <p style="text-align: center;">No hemos podido encontrar el Contenido que estás buscando.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="col-xs-12 col-sm-3 col-sm-offset-1 column2">

                    <div class="sidebar-revista">

                        <div class="cate">
                            <h3>Categorias</h3>
                            <button type="submit"></button>

                            {{--<div class="listado-cat">
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
                            </div>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop