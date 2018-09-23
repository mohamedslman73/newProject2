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
                        {!! Form::open(['route' => isset($result->id) ? ['system.bus.update',$result->id]:'system.bus.store','method' => isset($result->id) ?  'PATCH' : 'POST','files'=> true]) !!}
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-block card-dashboard">
                                    <div class="form-group col-sm-12{!! formError($errors,'bus_number',true) !!}">
                                        <div class="controls">
                                            {!! Form::label('bus_number', __('Bus Number')) !!}
                                            {!! Form::text('bus_number',isset($result->id) ? $result->bus_number:old('bus_number'),['class'=>'form-control']) !!}
                                        </div>
                                        {!! formError($errors,'bus_number') !!}
                                    </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'bus_brand_id',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('bus_brand_id', __('Bus Brand')) !!}
                                                {!! Form::select('bus_brand_id',['0'=>'Select Brand']+$brand,isset($result->id) ? $result->bus_brand_id:old('bus_brand_id'),['class'=>'form-control bus_brand_id']) !!}
                                            </div>

                                            {!! formError($errors,'bus_brand_id') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'gas',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('gas', __('Petrol')) !!}
                                                {!! Form::select('gas',[''=>__('Select Gas Type'),'gas-80'=>__('Gas-80'),'gas-90'=>__('Gas-90'),'gas-95'=>__('Gas-95'),'solar'=>__('Solar')],isset($result->id) ? $result->gas:old('gas'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'gas') !!}
                                        </div>



                                        <div class="form-group col-sm-6{!! formError($errors,'oil_change_rate',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('fixed_distance', __('Number Of Km That the Bus Moved it')) !!}
                                                {!! Form::number('fixed_distance',isset($result->id) ? $result->fixed_distance:old('fixed_distance'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'fixed_distance') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'variable_distance',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('variable_distance', __('Number Of Km for Change Oil')) !!}
                                                {!! Form::number('variable_distance',isset($result->id) ? $result->variable_distance:old('variable_distance'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'variable_distance') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'available',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('available', __('Availability').':') !!}
                                                {!! Form::select('available',[''=>'Select ','available'=>__('Available'),'unavailable'=>__('Unavailable')],isset($result->id) ? $result->available:old('available'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'available') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'driver',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('driver',__('Driver (optional):')) }}
                                                {!! Form::select('driver',[''=>__('Select Staff')],null,['style'=>'width: 100%;' ,'id'=>'driver','class'=>'form-control col-md-12']) !!}
                                            </div>
                                            {!! formError($errors,'driver') !!}
                                        </div>

                                        {!! Form::hidden('id',isset($result->id) ? $result->id:old('id'),['class'=>'form-control']) !!}
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
    <script type="text/javascript">
        ajaxSelect2('#driver','staff','',"{{route('system.ajax.get')}}");

        $(document).ready(function() {
            $('.bus_brand_id').select2();
        });

    </script>
@endsection