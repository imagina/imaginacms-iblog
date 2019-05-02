<div class="box-body">
    <div class='form-group{{ $errors->has("{$lang}.title") ? ' has-error' : '' }}'>
        {!! Form::label("{$lang}[title]", trans('iblog::post.form.title')) !!}
        <?php $old = $post->hasTranslation($lang) ? $post->translate($lang)->title : '' ?>
        {!! Form::text("{$lang}[title]", old("{$lang}.title", $old), ['class' => 'form-control', 'data-slug' => 'source', 'placeholder' => trans('iblog::post.form.title')]) !!}
        {!! $errors->first("{$lang}.title", '<span class="help-block">:message</span>') !!}
    </div>
    <div class='form-group{{ $errors->has("{$lang}.slug") ? ' has-error' : '' }}'>
        {!! Form::label("{$lang}[slug]", trans('iblog::post.form.slug')) !!}
        <?php $old = $post->hasTranslation($lang) ? $post->translate($lang)->slug : '' ?>
        {!! Form::text("{$lang}[slug]", old("{$lang}.slug",$old), ['class' => 'form-control slug', 'data-slug' => 'target', 'placeholder' => trans('iblog::post.form.slug')]) !!}
        {!! $errors->first("{$lang}.slug", '<span class="help-block">:message</span>') !!}
    </div>

    <div class='form-group{{ $errors->has("$lang.summary") ? ' has-error' : '' }}'>
        {!! Form::label("{$lang}[summary]", trans('iblog::post.form.summary')) !!}
        <?php $old = $post->hasTranslation($lang) ? $post->translate($lang)->summary : '' ?>
        {!! Form::textarea("{$lang}[summary]", old("$lang.summary", $old), ['class' => 'form-control','rows'=>2, 'placeholder' => trans('iblog::post.form.summary')]) !!}
        {!! $errors->first("$lang.summary", '<span class="help-block">:message</span>') !!}
    </div>


    <?php $old = $post->hasTranslation($lang) ? $post->translate($lang)->description : '' ?>
    <div class='form-group{{ $errors->has("{$lang}.description") ? ' has-error' : '' }}'>
        @editor('description', trans('iblog::post.form.description'), old("$lang.description", $old), $lang)
    </div>

    <div class="col-xs-12" style="padding-top: 35px;">
        <div class="panel box box-primary">
            <div class="box-header">
                <div class="box-tools pull-right">
                    <a href="#aditional{{$lang}}" class="btn btn-box-tool" data-target="#aditional{{$lang}}"
                       data-toggle="collapse"><i class="fa fa-minus"></i>
                    </a>
                </div>
                <label>{{ trans('iblog::common.form.metadata')}}</label>
            </div>
            <div class="panel-collapse collapse" id="aditional{{$lang}}">
                <div class="box-body ">
                    <div class='form-group{{ $errors->has("{$lang}.metatitle") ? ' has-error' : '' }}'>
                        {!! Form::label("{$lang}[metatitle]", trans('iblog::post.form.metatitle')) !!}
                        <?php $old = $post->hasTranslation($lang) ? $post->translate($lang)->metatitle : '' ?>
                        {!! Form::text("{$lang}[metatitle]", old("{$lang}.metatitle", $old), ['class' => 'form-control', 'data-slug' => 'source', 'placeholder' => trans('iblog::post.form.metatitle')]) !!}
                        {!! $errors->first("{$lang}.metatitle", '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class='form-group{{ $errors->has("{$lang}.metakeywords") ? ' has-error' : '' }}'>
                        {!! Form::label("{$lang}[metakeywords]", trans('iblog::post.form.metakeywords')) !!}
                        <?php $old = $post->hasTranslation($lang) ? $post->translate($lang)->metakeywords : '' ?>
                        {!! Form::text("{$lang}[metakeywords]", old("{$lang}.metakeywords", $old), ['class' => 'form-control', 'data-slug' => 'source', 'placeholder' => trans('iblog::post.form.metakeywords')]) !!}
                        {!! $errors->first("{$lang}.metakeywords", '<span class="help-block">:message</span>') !!}
                    </div>

                    <?php $old = $post->hasTranslation($lang) ? $post->translate($lang)->metadescription : '' ?>
                    @editor('content', trans('iblog::post.form.metadescription'), old("$lang.metadescription",$old), $lang)
                </div>
            </div>
        </div>
    </div>
    @if (config('asgard.iblog.config.post.partials.translatable.edit') && config('asgard.iblog.config.post.partials.translatable.edit') !== [])
    @foreach (config('asgard.page.config.post.partials.translatable.edit') as $partial)
    @include($partial)
    @endforeach
   @endif
</div>

