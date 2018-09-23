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
            <div class="content-body">
                <!-- Server-side processing -->
                <section id="server-processing">
                    <div class="row">
                        @if($errors->any())
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="alert alert-danger">
                                        {{__('Some fields are invalid please fix them')}}
                                        <ul>
                                            @foreach($errors->all() as $key => $value)
                                                <li>{{$key}}: {{$value}}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @elseif(Session::has('status'))
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="alert alert-{{Session::get('status')}}">
                                        {{ Session::get('msg') }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        {!! Form::open(['route' => 'system.attendance.monthly-report-calc','method' => 'POST','autocomplete'=>'off']) !!}
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-block card-dashboard">
                                    <div class="form-group col-sm-6{!! formError($errors,'staff_id',true) !!}">
                                        <div class="controls">
                                            {!! Form::label('staff_id', __('Staff')) !!}
                                            {!! Form::select('staff_id', ['0'=>'Select Staff'],"",['id'=>'staff_id','class'=>'form-control']) !!}
                                         </div>
                                        {!! formError($errors,'staff_id') !!}
                                    </div>
                                        <div class="form-group col-sm-6{!! formError($errors,'staff_id',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('date',__('date')) }}
                                                {!! Form::text('date',null,['class'=>'form-control datepicker','id'=>'date']) !!}
                                            </div>
                                            {!! formError($errors,'date') !!}
                                        </div>


                                    </div>
                                </div>
                            </div>

                        <div class="col-xs-12" style="padding-top: 20px;">
                            <div class="card-header">
                                <div class="card-body">
                                    <div class="card-block card-dashboard">
                                        {!! Form::submit(__('Calculate'),['class'=>'btn btn-success pull-right']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </section>
                <!--/ Javascript sourced data -->
            </div>
        </div>

    </div>

        @section('header')
            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/extensions/pace.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/daterange/daterangepicker.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/datetime/bootstrap-datetimepicker.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/pickadate/pickadate.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/forms/selects/select2.min.css')}}">
        @endsection;

        @section('footer')
            <script src="{{asset('assets/system/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
            <!-- BEGIN PAGE VENDOR JS-->
            <script src="{{asset('assets/system/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/system/vendors/js/pickers/dateTime/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/system/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/system/vendors/js/pickers/daterange/daterangepicker.js')}}" type="text/javascript"></script>
            <!-- END PAGE VENDOR JS-->

            <script src="{{asset('assets/system/js/scripts/pickers/dateTime/picker-date-time.js')}}" type="text/javascript"></script>

            <script type="text/javascript">
                ajaxSelect2('#staff_id','staff','',"{{route('system.ajax.get')}}");



                $(function(){
                    $('.datepicker').datetimepicker({
                        viewMode: 'months',
                        format: 'YYYY-MM'
                    });
                });

            </script>
@endsection
