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
                            {!! Form::open(['route' => isset($result->id) ? ['system.vacation.update',$result->id]:'system.vacation.store','method' => isset($result->id) ?  'PATCH' : 'POST']) !!}

                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h2>{{__('Vacation Data')}}</h2>
                                    </div>
                                    <div class="card-block card-dashboard">

                                        <div class="form-group col-sm-6{!! formError($errors,'vacation_start',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('from_date', __('Vacation Start At')) !!}
                                                {!! Form::text('from_date',isset($result->id) ? $result->from_date:old('from_date'),['class'=>'form-control datepicker']) !!}
                                            </div>
                                            {!! formError($errors,'from_date') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'to_date',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('to_date', __('Vacation End At')) !!}
                                                {!! Form::text('to_date',isset($result->id) ? $result->to_date:old('to_date'),['class'=>'form-control datepicker']) !!}
                                            </div>
                                            {!! formError($errors,'to_date') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'vacation_type_id',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('vacation_type_id', __('Vacation Type')) !!}
                                                {!! Form::select('vacation_type_id',['0'=>'Select Vacation Type']+$vacation_types,isset($result->id) ? $result->vacation_type_id:old('vacation_type_id'),['class'=>'form-control vacation_type_id']) !!}
                                            </div>
                                            {!! formError($errors,'vacation_type_id') !!}
                                        </div>
                                        <div class="form-group col-sm-6{!! formError($errors,'staff_id',true) !!}">
                                            <div class="controls">
                                                    {!! Form::label('type', __('Select Staff To Add Vacation').':') !!}
                                                {!! Form::select('staff_id',isset($result->id) ? [$result->staff_id =>$result->staff->Fullname]:[''=>__('Select Staff')],isset($result->id) ? $result->staff_id:old('staff_id'),['style'=>'width: 100%;' ,'id'=>'staffSelect2','class'=>'form-control col-md-12']) !!}

                                            </div>
                                            {!! formError($errors,'staff_id') !!}
                                        </div>




                                        @if(isset($result->id))
                                            <div class="form-group col-sm-12{!! formError($errors,'decision',true) !!}">
                                                <div class="controls">
                                                    {!! Form::label('status', __('Status').':') !!}
                                                    {!! Form::select('status',[''=>__('Select Status'),'approved'=>__('Approved'),'rejected'=>__('Rejected')],isset($result->id) ? $result->status:old('status'),['class'=>'form-control']) !!}
                                                </div>
                                                {!! formError($errors,'status') !!}
                                            </div>
                                        @endif

                                        <div class="form-group col-sm-12{!! formError($errors,'comment',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('comment', __('Comment')) !!}
                                                {!! Form::textarea('comment',isset($result->id) ? $result->comment:old('comment'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'comment') !!}
                                        </div>
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

<script>
    $(document).ready(function() {
        $('.vacation_type_id').select2();
    });
    $(function(){
        $('.datepicker').datetimepicker({
            viewMode: 'months',
            format: 'YYYY-MM-DD'
        });
    });
    ajaxSelect2('#staffSelect2','staff','',"{{route('system.ajax.get')}}");
        {{--<script type="text/javascript">--}}

            {{--$(document).ready(function(){--}}
                {{--list_type_function();--}}
            {{--});--}}

            {{--function list_type_function(){--}}
                {{--$value = $('#list_type').val();--}}
                {{--if($value == 'static'){--}}
                    {{--$('#dynamic-point-div').hide();--}}
                    {{--$('#static-point-div').show();--}}
                {{--}else{--}}
                    {{--$('#static-point-div').hide();--}}
                    {{--$('#dynamic-point-div').show();--}}
                {{--}--}}
            {{--}--}}

        </script>
@endsection