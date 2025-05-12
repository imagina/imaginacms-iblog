<div id="timelineLayout3" class="timeline">
  <div class="container">
    <div class="col-12">
      <div class="title-section {{$textAlign}} @if($textPosition==3) d-flex flex-column @endif ">
        @if($title!=="")
          <h2
            class="title {{$titleClasses}} {{ $textPosition==3 ? 'order-1':'' }} {{$titleColor}} {{$titleWeight}} {{$titleTransform}} {{$titleMarginT}} {{$titleMarginB}}">
            @if($titleVineta)
              <i class="{{$titleVineta}} {{$titleVinetaColor}} mr-1"></i>
            @endif
            <span> {!! $title !!}</span>
          </h2>
        @endif
        @if($subtitle!=="" && $textPosition!=1)
          <h3
            class="subtitle {{$subtitleClasses}} {{$subtitleColor}} {{$subtitleWeight}} {{$subtitleTransform}} {{$subtitleMarginT}} {{$subtitleMarginB}}">
            {!! $subtitle !!}
          </h3>
        @endif
      </div>
      <div class="row">
        <div class="content-timeline position-relative">
          <ul class="p-0">
            @foreach ($items as $key => $item)
              @php
                $dataContent = null;

                // Content Date
                if(empty($contentLabel)){

                   // Format Date
                  if (isset($item->date_available) && !empty($item->date_available)) {
                     $dateFix = new \DateTime($item->date_available);
                     $dataContent = $dateFix->format($formatDate);
                  } elseif (isset($item->created_at) && !empty($item->created_at)) {
                     $dataContent = $item->created_at->format($formatDate);

                  } else {
                      $dataContent = $item->options->valueIdFieldTimeLine ?? $item->created_at;
                  }
                }else{

                   //Content from fields
                  foreach($contentLabel as $label){
                     $dataContent = $item->{$label}
                        ?? $item->options->{$label}
                            ?? (method_exists($item, 'formatFillableToModel')
                               ? $item->getFieldByName($label) : null);
                  }
                }
              @endphp

              @if($key > 0)
                <div class="content-item d-block d-lg-flex align-items-center {{$showTwoItems ? 'col-12 col-lg-6' : 'col-12'}} {{$classItem}}">
                  <div class="icon-list">
                    <i class="{{ $icon }}"></i>
                  </div>
                  <div class="content-increment-time">
                    @if($withNumber)
                      <div class="content-number">
                        <div class="number {{ $classNumber }}">
                          {{$key}}
                        </div>
                      </div>
                    @endif
                    @if($dataContent && $withContentLabel)
                      <div class="content-content-label">
                        <div class="content label">
                          {!! $dataContent !!}
                        </div>
                      </div>
                    @endif
                  </div>
                  <div class="item-info">
                    @include("isite::frontend.partials.item", [
                        "itemLayout" => $itemComponentAttributes['layout'],
                        "itemComponentAttributes" => $itemComponentAttributes
                    ])
                  </div>
                </div>
              @endif
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<style>

  /* Title TimeLine */
  #timelineLayout3 .title-section .title {
    font-size: {{ $titleSize }}px;
    letter-spacing: {{ $titleLetterSpacing }}px;
  }

  #timelineLayout3 .title-section .subtitle {
    font-size: {{ $subtitleSize }}px;
    letter-spacing: {{ $subtitleLetterSpacing }}px;
  }

  #timelineLayout3 .content-timeline {
    max-width: 100% !important;
  }

  /* styles main line */
  #timelineLayout3 .content-timeline:before {
    content: "";
    position: absolute;
    top: 0;
    width: 3px;
    height: 100%;
    background-color: {{$mainLineColor}};

    @if($showTwoItems)
      left: 49.9%;
    @else
      @if($mainLinePosition === '1')
        left: 0;
      @elseif($mainLinePosition == '2')
        left: 100%;
      @endif
    @endif
  }

  @if($showTwoItems)
    @media (max-width: 991.98px) {
      #timelineLayout3 .content-timeline:before {
        left: {{ $mainLinePosition === '1' ? 0 : '100%' }} !important;
      }
    }
  @endif

  /* location first item */
  #timelineLayout3 .content-item {
    @if($showTwoItems)
      clear: both;

        @if($firstItemPosition === '1')
            &:nth-child(odd) {
              float: right;
              padding-left: 20px;

              & .icon-list {
            left: calc(-{{$sizeIcon}}px / 2);
              }
           }

          &:nth-child(even) {
            float: left;
            padding-right: 20px;

            & .icon-list {
           right: calc(-{{$sizeIcon}}px / 2);
            }
          }

        @elseif($firstItemPosition === '2')
            &:nth-child(odd) {
              float: left;
              padding-right: 20px;

              & .icon-list {
              right: calc(-{{$sizeIcon}}px / 2);
              }
            }

            &:nth-child(even) {
              float: right;
              padding-left: 20px;

              & .icon-list {
            left: calc(-{{$sizeIcon}}px / 2);
              }
            }
        @endif
      @else
        & .icon-list {
          {{ $mainLinePosition === '1' ? 'left' : 'right' }}: calc(-{{$sizeIcon}}px / 2);
        }
    @endif
  }

  @if($showTwoItems)
    @media (max-width: 991.98px) {
    #timelineLayout3 .content-item.content-item .icon-list {
    {{ $mainLinePosition === '1' ? 'left' : 'right' }}: calc(-{{$sizeIcon}}px / 2) !important;
      {{ $mainLinePosition === '1' ? 'right' : 'left' }}: auto !important;
    }
  }
  @endif

  /*Position Image*/
  #timelineLayout3 .content-item {
    @if($imageInterspersed)
      clear: both;

    @if($firstImagePosition === '1')
        &:nth-child(odd) .card-item .item-image {
      order: 0;
    }

    &:nth-child(even) .card-item .item-image {
      order: 1;
    }

    @elseif($firstImagePosition === '2')
        &:nth-child(odd) .card-item .item-image {
      order: 1;
    }

    &:nth-child(even) .card-item .item-image {
      order: 0;
    }
    @endif
    @else
      & .icon-list{
    {{ $mainLinePosition === '1' ? 'left :' : 'right :' }} calc(-{{$sizeIcon}}px / 2);
    }
  @endif
}

  /*Icon styles*/
  #timelineLayout3 .content-item .icon-list {
    position: absolute;
    top: 45%;
    i{
      font-size: {{$sizeIcon}}px;
      color: {{$colorIcon}};
    }
  }

  #timelineLayout3 .content-item {
    .content-increment-time{
      margin: 0 10px;
    }
    @if($showTwoItems)
      @if($firstItemPosition === '1')
        &:nth-child(odd) .content-increment-time {
      order: 0;
    }
    &:nth-child(even) .content-increment-time {
      order: 2;
    }

    @elseif($firstItemPosition === '2')

        &:nth-child(odd) .content-increment-time {
      order: 2;
    }
    &:nth-child(even) .content-increment-time {
      order: 0;
    }
    @endif
    @else
      .content-increment-time {
      order: {{ $mainLinePosition === '1' ? '0' : '1' }};
    }
  @endif
}

  /* num counter Style */
  #timelineLayout3 .content-item .number {
    font-size: {{$sizeNumber}}px;
    height: {{$sizeContainerNumber}}px;
    width: {{$sizeContainerNumber}}px;
    color: {{$colorNumber}};
    background: {{$bgNumber}};
    border-radius: {{$radiusNumber}}%;
    border: {{$borderNumber}} solid {{$colorBorderNumber}};
    margin: {{ $marginNumber }};
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* date label styles*/
  #timelineLayout3 .content-item .content-content-label {
    &:after {
      display: inline-block;
      font-family: "Font Awesome 6 Sharp";
      content: "\f04b";
      font-weight: 900;
      color: {{$bgContentLabel }};
    }

    .content.label {
      font-size: {{$sizeContentLabel }}px;
      font-weight: bold;
      color: {{$colorContentLabel }};
      padding: {{ $paddingContentLabel }}px;
      background: {{$bgContentLabel }};
      border-radius: {{$radiusContentLabel }}px;
      text-align: center;
      margin: {{ $marginContentLabel }};
    }
  }

  #timelineLayout3 .content-item{
    .content-content-label {
      position: {{ $showTwoItems ? 'absolute' : 'initial' }};
      height: 100%;
      width: max-content;
      top: 0;
      display: flex;
      align-items: center;
    }

    @if($showTwoItems)
      @if($firstItemPosition === '1')
        &:nth-child(odd) .content-content-label {
      right: calc(100% + 30px);
    }

    &:nth-child(even) .content-content-label {
      left: calc(100% + 30px);

      .content.label {
        order: 1;
      }

      &:after{
        transform: rotate(180deg);
      }
    }

    @elseif($firstItemPosition === '2')
        &:nth-child(odd) .content-content-label {
      left: calc(100% + 30px);

      .content.label {
        order: 1;
      }

      &:after{
        transform: rotate(180deg);
      }
    }

    &:nth-child(even) .content-content-label {
      left: calc(100% + 30px);
    }
  @endif
@endif
}

  @media (max-width: 991.98px) {
    #timelineLayout3 .content-item .content-content-label {
      position: initial;

      &:after {
        content: none !important;
      }
    }

    #timelineLayout3 .content-item .content-increment-time {
      order: {{ $mainLinePosition === '1' ? '0' : '1' }} !important;
    }
  }
</style>
