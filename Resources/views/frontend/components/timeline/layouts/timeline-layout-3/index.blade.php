<div id="timelineLayout3" class="timeline">
 <div class="title-section">
  {{-- <h2>{{dd($category)}}</h2> --}}
 </div>
 <div class="container">
  <div class="col-12">
   <div class="row">
    <div class="content-timeline">
     <ul class="p-0">
      @foreach ($items as $key => $item)
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
          @if($withDate)
           <div class="content-date-time">
            <div class="date-time">{{$item->options->valueIdFieldTimeLine ?? $item->created_at}}</div>
           </div>
          @endif
         </div>
         <div class="item-info">
          @include("isite::frontend.partials.item", [
              "itemLayout" => $itemComponentAttributesList['layout'],
              "itemComponentAttributes" => $itemComponentAttributesList
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

  /* styles main line */
  .content-timeline:before {
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
      .content-timeline:before {
        left: 0;
      }
    }
  @endif

/* location first item*/
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
      & .icon-list{
        {{ $mainLinePosition === '1' ? 'left :' : 'right :' }} calc(-{{$sizeIcon}}px / 2);
      }
    @endif
  }

  @if($showTwoItems)
    @media (max-width: 991.98px) {
      #timelineLayout3 .content-item.content-item .icon-list {
        left: calc(-{{$sizeIcon}}px / 2);
      }
    }
  @endif

  /*Position Image*/
  #timelineLayout3 .content-item {
    @if($imageInterspersed)
      clear: both;
    @if($firstItemPosition === '1')
      &:nth-child(odd) .card-item .item-image {
        order: 0;
      }
      &:nth-child(even) .card-item .item-image {
        order: 1;
      }
    @elseif($firstItemPosition === '2')
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
  }

    /* num counter Style */
  #timelineLayout3 .content-item .number {
    font-size: {{$sizeNumber}}px;
    height: {{$sizeContainerNumber}}px;
    width: {{$sizeContainerNumber}}px;
    color: {{$colorNumber}};
    background: {{$bgNumber}};
    border-radius: {{$radiusNumber}}%;
    border: {{$borderNumber}};
    margin: {{ $marginNumber }};
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* date Time  styles*/
  #timelineLayout3 .content-item .content-date-time {
    &:after {
      display: inline-block;
      font-family: "Font Awesome 6 Sharp";
      content: "\f04b";
      font-weight: 900;
      color: {{$bgDate}};
    }

    .date-time {
      font-size: {{$sizeDate}}px;
      font-weight: bold;
      color: {{$colorDate}};
      padding: {{ $paddingDate }};
      background: {{$bgDate}};
      border-radius: {{$radiusDate}};
      text-align: center;
      margin: {{ $marginDate }} 0;
    }
  }
  #timelineLayout3 .content-item{
   .content-date-time {
    position: {{ $showTwoItems ? 'absolute' : 'initial' }};
    height: 100%;
    width: max-content;
    top: 0;
    display: flex;
    align-items: center;
   }

    @if($showTwoItems)
      @if($firstItemPosition === '1')
        &:nth-child(odd) .content-date-time {
          right: calc(100% + 30px);
        }
        &:nth-child(even) .content-date-time  {
          left: calc(100% + 30px);
          .date-time{
            order: 1;
          }
          &:after{
            transform: rotate(180deg);
          }
        }

       @elseif($firstItemPosition === '2')
       &:nth-child(odd) .content-date-time  {
         left: calc(100% + 30px);
         .date-time{
           order: 1;
         }
         &:after{
           transform: rotate(180deg);
         }
       }
       &:nth-child(even) .content-date-time  {
         left: calc(100% + 30px);
       }
     @endif
    @endif
  }

  @media (max-width: 991.98px){
    #timelineLayout3 .content-item .content-date-time{
      position: initial;
      &:after {
        display: none;
      }
    }
  }

</style>