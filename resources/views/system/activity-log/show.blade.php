<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even){background-color: #f2f2f2}
</style>
<div  style="overflow-x:auto;">
    <table border="1">
        <thead>
        <tr>
            <th>#</th>
            <th>{{__('Value')}}</th>
        </tr>
        </thead>
        <tbody>

        <tr>
            <td>{{__('ID')}}</td>
            <td>{{$result->id}}</td>
        </tr>


        <tr>
            <td>{{__('Log Name')}}</td>
            <td>{{$result->log_name}}</td>
        </tr>

        <tr>
            <td>{{__('Status')}}</td>
            <td>{{$result->description}}</td>
        </tr>

        <tr>
            <td>{{__('Model')}}</td>
            <td>{{$result->subject_type}} ({{$result->subject_id}})</td>
        </tr>

        <tr>
            <td>{{__('User')}}</td>
            <td>{{$result->causer_type}} ({{$result->causer_id}})</td>
        </tr>




        <tr>
            <td>{{__('Device')}}</td>
            <td>
                {{$result->agent->device()}}
                @if($result->agent->isDesktop())
                    {{__('Desktop')}}
                @elseif($result->agent->isMobile())
                    {{__('Mobile')}}
                @elseif($result->agent->isTablet())
                    {{__('Tablet')}}
                @else
                    --
                @endif
            </td>
        </tr>


        <tr>
            <td>{{__('Platform')}}</td>
            <td>{{$result->agent->platform()}} {{$result->agent->version($result->agent->platform())}}</td>
        </tr>


        <tr>
            <td>{{__('IP')}}</td>
            <td>{{$result->ip}}</td>
        </tr>


        <tr>
            <td>{{__('Browser')}}</td>
            <td>{{$result->agent->browser()}}</td>
        </tr>

        <tr>
            <td>{{__('Languages')}}</td>
            <td>{{implode(',',$result->agent->languages())}}</td>
        </tr>







        @if(isset($result->location))
        <tr>
            <td>{{__('Country')}}</td>
            <td>{{$result->location->country}} ({{$result->location->countryCode}})</td>
        </tr>
        <tr>
            <td>{{__('city')}}</td>
            <td>{{$result->location->city}}</td>
        </tr>
        <tr>
            <td>{{__('Region Name')}}</td>
            <td>{{$result->location->regionName}}</td>
        </tr>
        <tr>
            <td>{{__('ISP')}}</td>
            <td>{{$result->location->isp}}</td>
        </tr>
        <tr>
            <td>{{__('Latitude')}}</td>
            <td>{{$result->location->lat}}</td>
        </tr>
        <tr>
            <td>{{__('Longitude')}}</td>
            <td>{{$result->location->lon}}</td>
        </tr>
        @endif


        <tr>
            <td>{{__('URL')}}</td>
            <td>{{$result->method}} <a href="{{$result->url}}" target="_blank">{{$result->url}}</a> </td>
        </tr>

        <tr>
            <td>{{__('Created At')}}</td>
            <td>
                @if($result->created_at == null)
                    --
                @else
                    {{$result->created_at->diffForHumans()}}
                @endif
            </td>
        </tr>

        </tbody>
    </table>

    <hr>

    <h3 style="text-align: center;">{{__('Data')}}</h3>

    <table border="1">
        <thead>
        <tr>
            <th>Key</th>
            <th>{{__('Attributes')}}</th>
            @if(isset($result->properties['old']))
                <th>{{__('Old')}}</th>
            @endif
        </tr>
        </thead>
        <tbody>

        @php
        $keys = array_keys($result->properties['attributes']);
        @endphp

        @foreach($keys as $value)
            <tr>
                <td>{{$value}}</td>
                <td>
                    @if(is_array($result->properties['attributes'][$value]))
                    <pre>
                        {{print_r($result->properties['attributes'][$value])}}
                    </pre>
                    @else
                        {{$result->properties['attributes'][$value]}}
                    @endif
                </td>
                @if(isset($result->properties['old']))
                    <td>
                        @if(is_array($result->properties['old'][$value]))
                            <pre>
                        {{print_r($result->properties['old'][$value])}}
                    </pre>
                        @else
                            {{$result->properties['old'][$value]}}
                        @endif



                    </td>
                @endif
            </tr>
        @endforeach

        </tbody>
    </table>


</div>
