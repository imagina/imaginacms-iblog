@extends('layouts.master')

@section('meta')
  @include('iblog::frontend.partials.post.metas')
@stop

@section('title')
  {{ $post->title }} | @parent
@stop

@section('content')
  <div id="postLayout1" class="post-layout-1 page blog single single-{{$category->slug}} single-{{$category->id}}">
    <div class="page-banner position-relative">
      @if (isset($category))
        <div class="position-absolute h-100 w-100 content-title">
          <div class="container d-flex flex-column align-items-center w-100 h-100 justify-content-center">
            <div class="content-breadcrumb-center">
              <h2 class="text-white text-center">
                {{$category->title}}
              </h2>
              @include('iblog::frontend.partials.breadcrumb')
            </div>
          </div>
        </div>
        <div class="content-title-hidden"></div>
        @if (isset($category) && empty($category->breadcrumb) && strpos($category->mediaFiles()->breadcrumbimage->extraLargeThumb, 'default.jpg') == false)
          <div class="content-image h-100">
            <x-media::single-image
              :title="$category->title"
              :isMedia="true"
              width="100%"
              :mediaFiles="$category->mediaFiles()"
              zone="breadcrumbimage"
            />
          </div>
        @elseif (isset($post) && empty($post->breadcrumb) && strpos($post->mediaFiles()->breadcrumbimage->extraLargeThumb, 'default.jpg') == false)
          <div class="content-image h-100">
            <x-media::single-image
              :title="$post->title"
              :isMedia="true"
              width="100%"
              :mediaFiles="$post->mediaFiles()"
              zone="breadcrumbimage"
            />
          </div>
        @endif
      @endif
    </div>
    <div class="post-content container py-5">
      @if(isset($post->mediaFiles()->mainimage)&&!empty($post->mediaFiles()->mainimage))
        <div class="col-12 col-md-10 m-auto">
          <x-media::single-image imgClasses=""
                                 :mediaFiles="$post->mediaFiles()"
                                 :isMedia="true" :alt="$post->title"/>
        </div>
      @endif
      <div class="col-12 post-description pt-3">
        <div class="section-title pb-5 d-flex justify-content-center">
          <h1 class="title text-primary">{{ $post->title }}</h1>
        </div>
        <div class="post-description text-justify">
          {!! $post->description !!}
        </div>
      </div>
    </div>
  </div>
@stop