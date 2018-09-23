@extends('system.layouts')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/forms/selects/select2.min.css')}}">
@endsection
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
                        {!! Form::open(['route' => isset($result->id) ? ['system.tracking.update',$result->id]:'system.tracking.store','method' => isset($result->id) ?  'PATCH' : 'POST','files'=> true]) !!}
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-block card-dashboard">


                                    <div class="form-group col-md-6{!! formError($errors,'bus_id',true) !!}">
                                        <div class="controls">
                                            {{ Form::label('bus_id',__('Bus :')) }}
                                            {!! Form::select('bus_id',isset($result->id) ? [$result->bus_id =>$result->bus->bus_number]:[''=>__('Select Bus')],isset($result->id) ? $result->bus_id:old('bus_id'),['style'=>'width: 100%;' ,'id'=>'bus_id','class'=>'form-control col-md-12']) !!}

                                        </div>
                                        {!! formError($errors,'bus_id') !!}
                                    </div>

                                    <div class="form-group col-md-6{!! formError($errors,'driver_id',true) !!}">
                                        <div class="controls">
                                            {{ Form::label('staffSelect2',__(' Driver:')) }}
                                            {!! Form::select('driver_id',isset($result->id) ? [$result->driver_id =>$result->busDriver->Fullname]:[''=>__('Select Driver')],isset($result->id) ? $result->driver_id:old('driver_id'),['style'=>'width: 100%;' ,'id'=>'staffSelect2','class'=>'form-control col-md-12']) !!}

                                        </div>
                                        {!! formError($errors,'driver_id') !!}
                                    </div>

                                    <div class="form-group col-sm-6{!! formError($errors,'destination_from',true) !!}">
                                        <div class="controls">
                                            {!! Form::label('destination_from', __('Destination From')) !!}
                                            {!! Form::text('destination_from',isset($result->id) ? $result->destination_from:old('destination_from'),['class'=>'form-control']) !!}
                                        </div>
                                        {!! formError($errors,'destination_from') !!}
                                    </div>

                                    <div class="form-group col-sm-6{!! formError($errors,'destination_to',true) !!}">
                                        <div class="controls">
                                            {!! Form::label('to', __('Destination To')) !!}
                                            {!! Form::text('destination_to',isset($result->id) ? $result->destination_to:old('destination_to'),['class'=>'form-control']) !!}
                                        </div>
                                        {!! formError($errors,'destination_to') !!}
                                    </div>

                                    <div class="form-group col-sm-6{!! formError($errors,'date_from',true) !!}">
                                        <div class="controls">
                                            {!! Form::label('from', __('Date Of The Move')) !!}
                                            {!! Form::text('date_from',isset($result->id) ? $result->date_from:old('date_from'),['class'=>'form-control datepicker']) !!}
                                        </div>
                                        {!! formError($errors,'date_from') !!}
                                    </div>

                                    <div class="form-group col-sm-6{!! formError($errors,'date_to',true) !!}">
                                        <div class="controls">
                                            {!! Form::label('to', __('Date Of Arrival')) !!}
                                            {!! Form::text('date_to',isset($result->id) ? $result->date_to:old('date_to'),['class'=>'form-control datepicker']) !!}
                                        </div>
                                        {!! formError($errors,'date_to') !!}
                                    </div>

                                    <div class="form-group col-sm-6{!! formError($errors,'km_after',true) !!}">
                                        <div class="controls">
                                            {!! Form::label('km_after', __('#No of KM That the Bus Moved')) !!}
                                            {!! Form::number('km_after',isset($result->id) ? $result->km_after:old('km_before'),['class'=>'form-control']) !!}
                                        </div>
                                        {!! formError($errors,'km_after') !!}
                                    </div>

                                    <div class="form-group col-md-6{!! formError($errors,'project_id',true) !!}">
                                        <div class="controls">
                                            {{ Form::label('project_id',__(' Project:')) }}
                                            {!! Form::select('project_id',isset($result->id) ? [$result->project_id =>$result->project->name]:[''=>__('Select Project')],isset($result->id) ? $result->project_id:old('project_id'),['style'=>'width: 100%;' ,'id'=>'project_id','class'=>'form-control col-md-12']) !!}

                                        </div>
                                        {!! formError($errors,'project_id') !!}
                                    </div>


                                    {{--<div class="form-group col-sm-12{!! formError($errors,'date',true) !!}">--}}
                                        {{--<div class="controls">--}}
                                            {{--{!! Form::label('date', __('Date of Tracking')) !!}--}}
                                            {{--{!! Form::text('date',isset($result->id) ? $result->date:old('date'),['class'=>'form-control datepicker']) !!}--}}
                                        {{--</div>--}}
                                        {{--{!! formError($errors,'date') !!}--}}
                                    {{--</div>--}}


                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12" style="padding-top: 20px;">
                            <div class="card-header">
                                <div class="card-body">
                                    <div class="card-block card-dashboard">
                                        {!! Form::submit(__('Save'),['class'=>'btn btn-success pull-right']) !!}
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
    <!-- ////////////////////////////////////////////////////////////////////////////-->
@endsection
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/datetime/bootstrap-datetimepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/pickadate/pickadate.css')}}">
@endsection



@section('footer')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/extensions/pace.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/daterange/daterangepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/datetime/bootstrap-datetimepicker.css')}}">
    <script src="{{asset('assets/system')}}/vendors/js/forms/select/select2.full.min.js" type="text/javascript"></script>

    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{asset('assets/system/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/dateTime/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/daterange/daterangepicker.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        ajaxSelect2('#staffSelect2','staff','',"{{route('system.ajax.get')}}");
        ajaxSelect2('#bus_id','bus','',"{{route('system.ajax.get')}}");
        ajaxSelect2('#project_id','project','',"{{route('system.ajax.get')}}");

        $(function(){
            $('.datepicker').datetimepicker({
                viewMode: 'months',
                format: 'YYYY-MM-DD HH:mm:ss'
            });
        });


    </script>
@endsection