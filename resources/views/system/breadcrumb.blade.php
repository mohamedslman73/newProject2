@if(isset($breadcrumb))
<div class="breadcrumb-wrapper col-xs-12">
    <ol class="breadcrumb">
        @foreach($breadcrumb as $value)
        <li class="breadcrumb-item @if(!isset($value['url'])) active @endif">
            @if(isset($value['url']))
            <a href="{{$value['url']}}">
                {{$value['text']}}
            </a>
            @else
            {{$value['text']}}
            @endif
        </li>
        @endforeach
    </ol>
</div>
@endif