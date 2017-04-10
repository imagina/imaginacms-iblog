@if (count(postgallery($post->id)) > 0)
    <div class="row">
        <div class="general-block10 col-sm-12 col-xs-12 col-md-10 col-md-offset-1">
            <div class="titulo">
                <span> Galería </span>
            </div>
            <div class="row">
                @foreach(postgallery($post->id) as $image)
                    <div class="col-md-3">
                        <a class="fancybox" href="{{ asset($image) }}" data-fancybox-group="gallery">
                            <img src="{{ asset($image) }}" class="img-thumbnail" alt="" /></a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif


@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('modules/iblog/vendor/fancyBox/source/jquery.fancybox.css') }}" media="screen" />
@stop
@section('scripts')
    <script type="text/javascript" src="{{ asset('modules/iblog/vendor/fancyBox/source/jquery.fancybox.js') }}"></script>
    <script>
        $(document).ready(function(){
            /**
             * Main
             **/
            $('.fancybox').fancybox();
        })
    </script>
@stop