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
                        <div class="col-xs-12">
                            @if($errors->any())
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="alert alert-danger">
                                            {{__('Some fields are invalid please fix them')}}
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
                            {!! Form::open(['route' => isset($result->id) ? ['system.visa-tracking.update',$result->id]:'system.visa-tracking.store','files'=>true, 'method' => isset($result->id) ?  'PATCH' : 'POST']) !!}
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-block card-dashboard">
                                        <div class="form-group col-sm-6{!! formError($errors,'staff_name',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('staff_name', __('Staff Name').':') !!}
                                                {!! Form::text('staff_name',isset($result->id) ? $result->staff_name:old('staff_name'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'staff_name') !!}
                                        </div>



                                        <div class="form-group col-sm-6{!! formError($errors,'visa_status',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('visa_status', __('Visa Status').':') !!}
                                                {!! Form::select('visa_status',[''=>'Select Visa Status','arrived'=>__('Arrived'),'cancelled'=>__('Cancelled'),'pending'=>__('Pending')],isset($result->id) ? $result->visa_status:old('visa_status'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'visa_status') !!}
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'visa_no',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('visa_no', __('Visa Number').':') !!}
                                                {!! Form::number('visa_no',isset($result->id) ? $result->visa_no:old('visa_no'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'visa_no') !!}
                                        </div>




                                        <div class="form-group col-sm-6{!! formError($errors,'passport_no',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('passport_no', __('Passport Number ').':') !!}
                                                {!! Form::number('passport_no',isset($result->id) ? $result->passport_no:old('passport_no'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'passport_no') !!}
                                        </div>



                                      <div class="form-group col-sm-6{!! formError($errors,'date_of_visa_issue',true) !!}">
                                        <div class="controls">
                                            {!! Form::label('date_of_visa_issue', __('Date of Visa Issue').':') !!}
                                            {!! Form::text('date_of_visa_issue',isset($result->id) ? $result->date_of_visa_issue:old('date_of_visa_issue'),['class'=>'form-control datepicker']) !!}
                                        </div>
                                        {!! formError($errors,'date_of_visa_issue') !!}
                                    </div>


                                        <div class="form-group col-sm-6{!! formError($errors,'gender',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('gender', __('Gender').':') !!}
                                                {!! Form::select('gender',['male'=>__('Male'),'female'=>__('Female')],isset($result->id) ? $result->gender:old('gender'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'gender') !!}
                                        </div>

                                        <div  class="form-group col-sm-6{!! formError($errors,'',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('nationality', __('Nationality').':') !!}
                                                {!! Form::select('nationality',['Select Country']+$country_name,isset($result->id) ? $result->nationality:old('nationality'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'nationality') !!}
                                        </div>


                                        <div class="form-group col-sm-6{!! formError($errors,'joining_date',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('joining_date', __('Joining Date').':') !!}
                                                {!! Form::text('joining_date',isset($result->id) ? $result->joining_date:old('joining_date'),['class'=>'form-control datepicker']) !!}
                                            </div>
                                            {!! formError($errors,'joining_date') !!}
                                        </div>
                                        <div class="form-group col-sm-6{!! formError($errors,'id_no',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('id_no', __('ID Number').':') !!}
                                                {!! Form::number('id_no',isset($result->id) ? $result->id_no:old('id_no'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'id_no') !!}
                                        </div>


                                    </div>
                                </div>
                            </div>

                                {!! Form::hidden('id',isset($result->id) ? $result->id:old('id'),['class'=>'form-control ']) !!}

                            <div class="col-xs-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-block card-dashboard">
                                            {!! Form::submit(__('Save'),['class'=>'btn btn-success pull-right']) !!}
                                        </div>
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

$(document).ready(function() {
    $('.weekly_vacations').select2();
    $('.certificate_id').select2();
    $('.clothe_id').select2();
});
$(function(){


    $('.datepicker').datetimepicker({
        viewMode: 'months',
        format: 'YYYY-MM-DD'
    });
});

    </script>
@endsection