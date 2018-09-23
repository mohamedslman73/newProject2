@extends('system.layouts')

@section('content')
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-xs-12">
                    <h4>{{$pageTitle}}</h4>
                </div>
                <div class="content-header-right col-md-8 col-xs-12">
                    <div class=" content-header-title mb-0" style="float: right;">
                        @include('system.breadcrumb')
                    </div>
                </div>
            </div>

            <div class="content-body"><!-- Spacing -->
                <div class="row">
                    <div class="col-md-12">
                        <section id="spacing" class="card">
                            <div class="card-header">

                            </div>
                            <div class="card-body collapse in">
                                <div class="card-block">
                                <div class="table-responsive">
                                    <table class="table table-hover">
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
                                                <td>{{__('Status')}}</td>
                                                <td>{{$result->status}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Type')}}</td>
                                                <td>{{$result->type}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Phone Number')}}</td>
                                                <td>{{ $result->phone_number }}</td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Call time')}}</td>
                                                <td>{{$result->calltime}}
                                                <br>
                                                    {{$result->call_time->diffForHumans().' / '.$result->call_time }}
                                                </td>
                                            </tr>
                            @if(!empty($result['complain']))
                                            <tr>
                                                <td>{{__('complain')}}</td>
                                                <td><a href="{{route('system.complain.show',$result['complain']->id)}}">Complain</a> </td>
                                            </tr>
                                                    @endif

                                            <tr>
                                                <td>{{__('Call Details')}}</td>
                                                <td><code>{{ $result->call_details }}</code></td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Call Reminder')}}</td>
                                                @if($result->reminder)
                                                <td>{{ $result->reminder}}</td>
                                                    @else
                                                <td>--</td>
                                                    @endif
                                            </tr>

                                            <tr>
                                                <td>{{__('Created By')}}</td>
                                                <td>
                                                    <a href="{{url('system/staff/'.$result->staff_id)}}" target="_blank">
                                                        {{__('#ID')}}:{{$result->staff_id}} <br >{{$result->staff->Fullname}}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Created At')}}</td>
                                                <td>
                                                    @if($result->created_at == null)
                                                        --
                                                    @else
                                                        {{$result->created_at->diffForHumans().' / '.$result->created_at}}
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Updated At')}}</td>
                                                <td>
                                                    @if($result->updated_at == null)
                                                        --
                                                    @else
                                                        {{$result->updated_at->diffForHumans().' / '.$result->updated_at}}
                                                    @endif
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                                </div>
                            </div>
                        </section>

                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('header')
@endsection

@section('footer')
@endsection
