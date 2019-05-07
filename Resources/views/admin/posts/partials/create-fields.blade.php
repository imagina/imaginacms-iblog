<div class="box-body">
    <div class='form-group{{ $errors->has("{$lang}.title") ? ' has-error' : '' }}'>
        {!! Form::label("{$lang}[title]", trans('iblog::post.form.title')) !!}
        {!! Form::text("{$lang}[title]", old("{$lang}.title"), ['class' => 'form-control', 'data-slug' => 'source', 'placeholder' => trans('iblog::post.form.title')]) !!}
        {!! $errors->first("{$lang}.title", '<span class="help-block">:message</span>') !!}
    </div>

    <div class='form-group{{ $errors->has("$lang.summary") ? ' has-error' : '' }}'>
        {!! Form::label("{$lang}[summary]", trans('iblog::post.form.summary')) !!}
        {!! Form::textarea("{$lang}[summary]", old("$lang.summary"), ['class' => 'form-control','rows'=>2, 'placeholder' => trans('iblog::post.form.summary')]) !!}
        {!! $errors->first("$lang.summary", '<span class="help-block">:message</span>') !!}
    </div>
    
    <div class='form-group{{ $errors->has("{$lang}.description") ? ' has-error' : '' }}'>
        @editor('description', trans('iblog::post.form.description'), old("{$lang}.description"), $lang)
    </div>


    <div class="col-xs-12" style="padding-top: 35px;">
        <div class="panel box box-primary">
            <div class="box-header">
                <div class="box-tools pull-right">
                    <a href="#aditional{{$lang}}" class="btn btn-box-tool " data-target="#aditional{{$lang}}"
                       data-toggle="collapse"><i class="fa fa-minus"></i>
                    </a>
                </div>
                <label>{{ trans('iblog::post.form.metadata')}}</label>
            </div>
            <div class="panel-collapse collapse" id="aditional{{$lang}}">
                <div class="box-body">
                    <div class='form-group{{ $errors->has("{$lang}.metatitle") ? ' has-error' : '' }}'>
                        {!! Form::label("{$lang}[metatitle]", trans('iblog::post.form.metatitle')) !!}
                        {!! Form::text("{$lang}[metatitle]", old("{$lang}.metatitle"), ['class' => 'form-control', 'data-slug' => 'source', 'placeholder' => trans('iblog::post.form.metatitle')]) !!}
                        {!! $errors->first("{$lang}.metatitle", '<span class="help-block">:message</span>') !!}
                    </div>

                    <div class='form-group{{ $errors->has("{$lang}.metakeywords") ? ' has-error' : '' }}'>
                        {!! Form::label("{$lang}[metakeywords]", trans('iblog::post.form.metakeywords')) !!}
                        {!! Form::text("{$lang}[metakeywords]", old("{$lang}.metakeywords"), ['class' => 'form-control', 'data-slug' => 'source', 'placeholder' => trans('iblog::post.form.metakeywords')]) !!}
                        {!! $errors->first("{$lang}.metakeywords", '<span class="help-block">:message</span>') !!}
                    </div>

                    @editor('metadescription', trans('iblog::post.form.metadescription'),
                    old("{$lang}.metadescription"), $lang)
                </div>
            </div>
        </div>
    </div>
    
    

    @if (config('asgard.iblog.config.fields.post.partials.translatable.create') && config('asgard.iblog.config.fields.post.partials.translatable.create') !== [])
        @foreach (config('asgard.iblog.config.fields.post.partials.translatable.create') as $partial)
            @include($partial)
        @endforeach
    @endif
</div>
