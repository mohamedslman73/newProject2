@extends('system.layouts')

@section('content')
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-xs-12">
                    <h4>
                        {{$pageTitle}}
                    </h4>
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
                                                <td>{{__('Bus')}}</td>
                                                <td>
                                                    <a target="_blank" href="{{route('system.bus.index',['id'=>$result->bus_id])}}"> {{$result->bus->bus_number}}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Project')}}</td>
                                                <td>
                                                    <a target="_blank" href="{{route('system.project.show',$result->project_id)}}">{{$result->project->name}}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Driver Name')}}</td>
                                                <td>
                                                    <a target="_blank" href="{{route('system.staff.show',$result->busDriver->id)}}" target="_blank">{{$result->busDriver->Fullname}}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Km Before</td>
                                                <td>
                                                    {{$result->km_before}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Km After</td>
                                                <td>
                                                    {{$result->km_after}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Number Of Km</td>
                                                <td>
                                                    {{$result->number_km}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Date</td>
                                                <td>
                                                      {{$result->date}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>From</td>
                                                <td>
                                                    <code>  {{$result->destination_from}}</code>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>To</td>
                                                <td>
                                                    <code>  {{$result->destination_to}}</code>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Created_By')}}</td>
                                                <td>
                                                    <a target="_blank" href="{{route('system.staff.show',$result->staff_id)}}" target="_blank">{{$result->staff->Fullname}}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Created At')}}</td>
                                                <td>
                                                    @if($result->created_at == null)
                                                        --
                                                    @else
                                                        {{$result->created_at}}
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Updated At')}}</td>
                                                <td>
                                                    @if($result->updated_at == null)
                                                        --
                                                    @else
                                                        {{$result->updated_at->diffForHumans()}}
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
