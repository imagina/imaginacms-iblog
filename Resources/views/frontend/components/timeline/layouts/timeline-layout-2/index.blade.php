<div id="timelineLayout2" class="timeline">

  <div class="entry">
    @foreach($items as $index => $item)
      <div class="content-timeline">
        <div class="title">
          <h3><b>{{$item->options->eventHour ?? ''}}</b><br>
            {{$item->options->eventPlace ?? ''}} <br> {{$item->options->eventDate ?? ''}} </h3>
          <i class="icon fa fa-home {{$item->options->icon ?? ''}}" style=" opacity: 0.5;"></i>
        </div>
        <div class="body">
          <hr>
          <h2>{{$item->title}}</h2>
          <ul>
            <li> {!! $item->summary !!}</li>
          </ul>
          <hr>
        </div>
      </div>
    @endforeach
  </div>
</div>

