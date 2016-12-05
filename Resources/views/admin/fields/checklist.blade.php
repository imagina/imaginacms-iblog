<!-- select2 -->
<div @include('bcrud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    <?php $entity_model = $crud->getModel(); ?>

    <div class="row icheckbox-inline">
        @foreach ($field['model']::all() as $connected_entity_entry)
            <div class="col-sm-4">
                <div class="checkbox checkbox-inline checkbox-primary">
                  <label>
                    <input type="checkbox" class="styled"
                      name="{{ $field['name'] }}[]"
                      value="{{ $connected_entity_entry->id }}"

                      @if( ( old( $field["name"] ) && in_array($connected_entity_entry->id, old( $field["name"])) ) || (isset($field['value']) && in_array($connected_entity_entry->id, $field['value']->pluck('id', 'id')->toArray())))
                             checked = "checked"
                      @endif > {!! $connected_entity_entry->{$field['attribute']} !!}
                  </label>
                </div>
            </div>
        @endforeach
    </div>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
@push('crud_fields_styles')
        <!-- include select2 css
<link href="{{ asset('modules/iblog/vendor/checkbox-inline/checkbox-inline.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('modules/iblog/vendor/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}" rel="stylesheet" type="text/css" />-->
@endpush
