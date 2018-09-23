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
                        {!! Form::open(['route' => isset($result->id) ? ['system.maintenance.update',$result->id]:'system.maintenance.store','method' => isset($result->id) ?  'PATCH' : 'POST','files'=> true]) !!}
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h2>{{__('Maintenance Data')}}</h2>
                                    </div>
                                    <div class="card-block card-dashboard">


                                        <div class="form-group col-md-6{!! formError($errors,'bus_id',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('bus_id',__('Bus :')) }}
                                                {!! Form::select('bus_id',isset($result->id) ? [$result->bus_id =>$result->bus->bus_number]:[''=>__('Select Bus')],isset($result->id) ? $result->bus_id:old('bus_id'),['style'=>'width: 100%;' ,'id'=>'bus_id','class'=>'form-control col-md-12']) !!}

                                            </div>
                                            {!! formError($errors,'bus_id') !!}
                                        </div>


                                        <div class="form-group col-sm-6{!! formError($errors,'maintenance_date',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('maintenance_date', __('Maintenance Date')) !!}
                                                {!! Form::text('maintenance_date',isset($result->id) ? $result->maintenance_date:old('maintenance_date'),['class'=>'form-control datepicker']) !!}
                                            </div>
                                            {!! formError($errors,'maintenance_date') !!}
                                        </div>


                                        <div class="form-group col-sm-12{!! formError($errors,'price',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('price', __('Price')) !!}
                                                {!! Form::number('price',isset($result->id) ? $result->price:old('price'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'price') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'no_of_km_oil',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('no_of_km_oil', __('#NO Of Km Oil(Require If Maintenance For Oil Change)')) !!}
                                                {!! Form::number('no_of_km_oil',isset($result->id) ? $result->no_of_km_oil:old('no_of_km_oil'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'no_of_km_oil') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'no_km_moving',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('no_km_moving', __('#NO Of Km Moving By This Oil qt(Require If Maintenance For Oil Change)')) !!}
                                                {!! Form::number('no_km_moving',isset($result->id) ? $result->no_km_moving:old('no_km_moving'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'no_km_moving') !!}
                                        </div>


                                        <div class="form-group col-sm-12{!! formError($errors,'note',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('note', __('Note (Optional):')) !!}
                                                {!! Form::textarea('note',isset($result->id) ? $result->note:old('note'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'note') !!}
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
        ajaxSelect2('#bus_id','bus','',"{{route('system.ajax.get')}}");
        staffSelect('#staffSelect2')

        $(function(){
            $('.datepicker').datetimepicker({
                viewMode: 'months',
                format: 'YYYY-MM-DD'
            });
        });
    </script>
@endsection