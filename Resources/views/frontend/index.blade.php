@extends('layouts.master')

@section('meta')
  @include('iblog::frontend.partials.category.metas')
@stop
@section('title')
  {{$category->title}} | @parent
@stop
@section('content')
  <div class="blog-category-{{$category->id}}">
    <div class="container">
      <div class="row">
        @include('iblog::frontend.partials.breadcrumb')
      </div>
    </div>
    <div class="container">
      
      <div class="row">
        
     
          <h1 class="my-4">{{$category->title}}
          </h1>
          <p>{!! $category->description!!}</p>

  
  
        <livewire:isite::items-list
          moduleName="Iblog"
          itemComponentName="isite::item-list"
          itemComponentNamespace="Modules\Isite\View\Components\ItemList"
          :configLayoutIndex="config('asgard.iblog.config.layoutIndex')"
          :itemComponentAttributes="config('asgard.iblog.config.indexItemListAttributes')"
          entityName="Post"
          :showTitle="false"
          :params="['filter' => ['category' => $category->id]]"
          :responsiveTopContent="['mobile'=>false,'desktop'=>false]"
        />



      </div>
      <!-- /.row -->
    
    </div>
  </div>
@stop
