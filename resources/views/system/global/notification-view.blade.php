@foreach($notifications as $key => $value)
    <a {!! iif($value->read_at == null,'style="background-color: #ffebcd;"') !!} href="{!! iif(isset($value->data['url']) && !empty($value->data['url']) ,route('system.notifications.url',$value->id),'javascript:void(0);') !!}" class="list-group-item">
        <div class="media">
            <div class="media-left valign-middle"><i class="ft-plus-square icon-bg-circle bg-cyan"></i></div>
            <div class="media-body">
                <h6 class="media-heading">{{$value->data['title']}}</h6>
                <p class="notification-text font-small-3 text-muted">{{$value->data['description']}}</p><small>
                    <time datetime="{{str_replace(' ','T',$value->created_at)}}" class="media-meta text-muted">{{$value->created_at->diffForHumans()}}</time></small>
            </div>
        </div>
    </a>
@endforeach

