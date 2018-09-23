@extends('system.layouts')

<div class="modal fade text-xs-left" id="changeDateTimeModal"  role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <label class="modal-title text-text-bold-600" id="myModalLabel33">{{__('Change Date Time')}}</label>
            </div>
            {!! Form::open(['route' => ['system.bus.change-availability'],'method' => 'POST']) !!}
            <div class="modal-body">

                <div class="card-body">
                    <div class="card-block">
                        <div class="row">


                            <div class="col-md-12">
                                <fieldset class="form-group">
                                {!! Form::label('available', __('Availability').':') !!}
                                {!! Form::select('available',[''=>'Select ','available'=>__('Available'),'unavailable'=>__('Unavailable')],isset($result->id) ? $result->available:old('available'),['class'=>'form-control']) !!}

                                </fieldset>
                            </div>
                            {!! Form::hidden('id',isset($result->id) ? $result->id:old('id'),['class'=>'form-control']) !!}

                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-outline-primary btn-md" value="{{__('Submit')}}">
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

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
                                <h4 class="card-title">
                                    {{--<span style="float: right;"><a class="btn btn-outline-primary"  href="javascript:void(0);" onclick="urlIframe('{{route('system.tracking.index').'?bus_id='.$result->id}}')"><i class="fa fa-pencil"></i> {{__('Bus Tracking')}}</a></span>--}}
                                    <a class="btn btn-outline-primary" style="float: right" href="{{route('system.tracking.index').'?bus_id='.$result->id}}" target="_blank">Bus Tracking</a>
                                </h4>

                                </h4>
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
                                                <td>{{__('Availability')}} <a href="javascript:void(0)" onclick="$('#changeDateTimeModal').modal('show');">( {{__('Change')}} )</a></td>
                                                <td>{{$result->available ?? '--'}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Bus Number')}}</td>
                                                <td>
                                                    {{$result->bus_number}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Daily Traffic Rate')}}</td>
                                                <td>
                                                        <code>{{$dailyTrafficRate}}</code>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Oil Change Rate')}}</td>
                                                <td>
                                                    @if(isset($oilChangeRate))
                                                    <code> {{$oilChangeRate}}</code>
                                                    @else
                                                        <code>--</code>
                                                        @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Quantity Of Oil Change Rate Per Day')}}</td>
                                                <td>
                                                    @if(isset($quantityOfOilChangeRate))
                                                    <code> {{$quantityOfOilChangeRate}} L </code>
                                                        @else
                                                        <code>--</code>
                                                        @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Driver Name')}}</td>
                                                @if($result->driver)
                                                <td>
                                                    <a target="_blank" href="{{route('system.staff.show',$result->busDriver->id)}}" target="_blank">{{$result->busDriver->Fullname}}</a>
                                                </td>
                                                    @else
                                                <td>--</td>
                                                @endif
                                            </tr>
                                            <tr>
                                                <td> {{__('Brand')}}</td>
                                               <td><a target="_blank" href="{{route('system.brand.show',$result->brand->id)}}"  >{{$result->brand->name}}</a></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Created_By')}}</td>
                                                <td>
                                                    <a target="_blank" href="{{route('system.staff.show',$result->staff->id)}}" target="_blank">{{$result->staff->Fullname}}</a>
                                                </td>
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
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/daterange/daterangepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/datetime/bootstrap-datetimepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/pickadate/pickadate.css')}}">

@endsection

@section('footer')
    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{asset('assets/system/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/dateTime/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/daterange/daterangepicker.js')}}" type="text/javascript"></script>
    <!-- END PAGE VENDOR JS-->

    <script type="text/javascript">
        $(function(){
            @if($errors->any())
                alertError('{{__('Validation Error')}}');
            @elseif(Session::has('msg'))
                alertSuccess('{{Session::get('msg')}}');
            @endif

            $('.datepicker').datetimepicker({
                viewMode: 'months',
                format: 'YYYY-MM-DD HH:mm:SS'
            });
        });
    </script>

@endsection
