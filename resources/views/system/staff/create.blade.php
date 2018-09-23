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
                            {!! Form::open(['route' => isset($result->id) ? ['system.staff.update',$result->id]:'system.staff.store','files'=>true, 'method' => isset($result->id) ?  'PATCH' : 'POST']) !!}
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-block card-dashboard">
                                        <div class="form-group col-sm-6{!! formError($errors,'firstname',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('firstname', __('First Name').':') !!}
                                                {!! Form::text('firstname',isset($result->id) ? $result->firstname:old('firstname'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'firstname') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'lastname',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('lastname', __('Last Name').':') !!}
                                                {!! Form::text('lastname',isset($result->id) ? $result->lastname:old('lastname'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'lastname') !!}
                                        </div>


                                        <div class="form-group col-sm-6{!! formError($errors,'visa_status',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('visa_status', __('Visa Status').':') !!}
                                                {!! Form::select('visa_status',[''=>'Select Visa Status','arrived'=>__('Arrived'),'cancelled'=>__('Cancelled'),'pending'=>__('Pending')],isset($result->id) ? $result->visa_status:old('visa_status'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'visa_status') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'visa_number',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('visa_number', __('Visa Number').':') !!}
                                                {!! Form::number('visa_number',isset($result->id) ? $result->visa_number:old('visa_number'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'visa_number') !!}
                                        </div>




                                        <div class="form-group col-sm-6{!! formError($errors,'passport_number',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('passport_number', __('Passport Number ').':') !!}
                                                {!! Form::number('passport_number',isset($result->id) ? $result->passport_number:old('passport_number'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'passport_number') !!}
                                        </div>


                                        <div class="form-group col-sm-6{!! formError($errors,'birthdate',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('birthdate', __('Birthdate').':') !!}
                                                {!! Form::date('birthdate',isset($result->id) ? $result->birthdate:old('birthdate'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'birthdate') !!}
                                        </div>

                                      <div class="form-group col-sm-6{!! formError($errors,'date_of_visa_issue',true) !!}">
                                        <div class="controls">
                                            {!! Form::label('date_of_visa_issue', __('Date of Visa Issue').':') !!}
                                            {!! Form::date('date_of_visa_issue',isset($result->id) ? $result->date_of_visa_issue:old('date_of_visa_issue'),['class'=>'form-control']) !!}
                                        </div>
                                        {!! formError($errors,'date_of_visa_issue') !!}
                                    </div>



                                        <div class="form-group col-sm-6{!! formError($errors,'address',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('bank_account', __('Bank Account').':') !!}
                                                {!! Form::number('bank_account',isset($result->id) ? $result->bank_account:old('bank_account'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'bank_account') !!}
                                        </div>
                                        <div class="form-group col-sm-12{!! formError($errors,'job_title',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('job_title', __('Job Title').':') !!}
                                                {!! Form::text('job_title',isset($result->id) ? $result->job_title:old('job_title'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'job_title') !!}
                                        </div>
                                        <div class="form-group col-sm-6{!! formError($errors,'salary',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('salary', __('Salary').':') !!}
                                                {!! Form::text('salary',isset($result->id) ? $result->salary:old('salary'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'salary') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'gender',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('gender', __('Gender').':') !!}
                                                {!! Form::select('gender',['male'=>__('Male'),'female'=>__('Female')],isset($result->id) ? $result->gender:old('gender'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'gender') !!}
                                        </div>

                                        <div  class="form-group col-sm-12{!! formError($errors,'',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('nationality', __('Nationality').':') !!}
                                                {!! Form::select('nationality',['Select Country']+$country_name,isset($result->id) ? $result->nationality:old('nationality'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'nationality') !!}
                                        </div>
                                        <div class="form-group col-sm-4{!! formError($errors,'avatar',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('avatar', __('Avatar').':') !!}
                                                {!! Form::file('avatar',['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'avatar') !!}
                                        </div>

                                        <div class="form-group col-sm-4{!! formError($errors,'weight',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('weight', __('weight (By Kg)').':') !!}
                                                {!! Form::number('weight',isset($result->id) ? $result->weight:old('weight'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'weight') !!}
                                        </div>
                                        <div class="form-group col-sm-4{!! formError($errors,'length',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('length', __('Height (By Cm)').':') !!}
                                                {!! Form::number('length',isset($result->id) ? $result->length:old('length'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'length') !!}
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'date_of_visa_issue',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('joining_date', __('Joining Date').':') !!}
                                                {!! Form::date('joining_date',isset($result->id) ? $result->joining_date:old('joining_date'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'joining_date') !!}
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'weekly_vacations',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('gender', __('Select Vacation Days').':') !!}
                                                {!! Form::select('weekly_vacations[]',['saturday'=>__('Saturday'),'sunday'=>__('Sunday'),'monday'=>__('Monday'),'tuesday'=>__('Tuesday'),'wednesday'=>__('Wednesday'),'thursday'=>__('Thursday'),'friday'=>__('Friday')],isset($result->id) ? $result->weekly_vacations:old('weekly_vacations'),['class'=>'form-control weekly_vacations','multiple'=>'multiple']) !!}
                                            </div>
                                            {!! formError($errors,'weekly_vacations') !!}
                                        </div>


                                        <div class="form-group col-sm-6{!! formError($errors,'permission_group_id',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('permission_group_id', __('Permission Group').':') !!}
                                                {!! Form::select('permission_group_id',[__('Select Permission Group')]+$PermissionGroup,isset($result->id) ? $result->permission_group_id:old('permission_group_id'),['class'=>'form-control','onchange'=>'permissionGroupChange();']) !!}
                                            </div>
                                            {!! formError($errors,'permission_group_id') !!}
                                        </div>
                                        <div class="form-group col-sm-6{!! formError($errors,'certificate_id',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('certificate_id', __('Certificate').':') !!}
                                                {!! Form::select('certificate_id[]',[__('Select Certificate')]+$certificate,isset($result->id) ? $result->certificate_id:old('certificate_id'),['class'=>'form-control certificate_id','multiple'=>'multiple']) !!}
                                            </div>
                                            {!! formError($errors,'certificate_id') !!}
                                        </div>
                                        <div id="cleaners" >

                                            <div class="col-sm-12 "  >
                                                <button onclick="addRow()" type="button" class="btn btn-primary fa fa-plus addinputfile">
                                                    <span>{{__('add Clothes')}}</span>
                                                </button>
                                            </div>

                                            @if(old('clothe_id') && old('size'))
                                                @foreach(old('clothe_id') as $key=> $row)

                                                    <div class="cleanersRow">

                                                        <div class="form-group col-sm-5{!! formError($errors,'clothe_id',true) !!}">
                                                            <div class="controls">
                                                                {!! Form::label('Clothes', __('Clothes').':') !!}
                                                                {{--{!! Form::select('clothe_id[]',[__('Select Clothes')]+$clothe,isset($result->id) ? $result->clothe_id:old('clothe_id')[$key],['class'=>'form-control clothe_id','multiple'=>'multiple']) !!}--}}
                                                            {{--</div>--}}
                                                                {!! Form::select('clothe_id[]',[$row=>"Clothes is Selected"]+$clothe,old('clothe_id')[$key],['style'=>'width: 100%;','class'=>'form-control clothe_id col-md-12'.$key]) !!}
                                                            </div>
                                                            {!! formError($errors,'clothe_id') !!}
                                                        </div>


                                                        <div class="form-group col-sm-5{!! formError($errors,'size',true) !!}">
                                                            <div class="controls">
                                                                {{ Form::label('size',__('size')) }}
                                                                {!! Form::text('size[]',old('size')[$key],['class'=>'form-control']) !!}
                                                            </div>
                                                            {!! formError($errors,'size') !!}
                                                        </div>

                                                        <div class="col-sm-2 form-group">
                                                            <a href="javascript:void(0);" onclick="$(this).closest('.cleanersRow').remove();" class="text-danger">
                                                                <i class="fa fa-lg fa-trash mt-3"></i>
                                                            </a>
                                                        </div>
                                                    </div>


                                                @endforeach

                                            @elseif(isset($result->id))

                                                @foreach($staff_clothes as $key=> $row)

                                                    <div class="cleanersRow" >

                                                        <div class="form-group col-sm-5{!! formError($errors,'clothe_id',true) !!}">
                                                            <div class="controls">
                                                                {!! Form::label('Clothes', __('Clothes').':') !!}
                                                                {!! Form::select('clothe_id[]',[__('Select Clothes')]+$clothe,isset($result->id) ? $row['clothe_id']:old('clothe_id'),['class'=>'form-control clothe_id']) !!}
                                                            </div>

                                                            {!! formError($errors,'clothe_id') !!}
                                                        </div>


                                                        <div class="form-group col-sm-5{!! formError($errors,'size',true) !!}">
                                                            <div class="controls">
                                                                {{ Form::label('size',__('size')) }}
                                                                {!! Form::number('size[]',isset($result->id)?$row['size']:old('size')[$key],['class'=>'form-control']) !!}
                                                            </div>
                                                            {!! formError($errors,'size') !!}
                                                        </div>

                                                        <div class="col-sm-2 form-group">
                                                            <a href="javascript:void(0);" onclick="$(this).closest('.cleanersRow').remove();" class="text-danger">
                                                                <i class="fa fa-lg fa-trash mt-3"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            @else
                                                <div class="cleanersRow" style="display: table;width: 100%">

                                                    <div class="form-group col-sm-5{!! formError($errors,'clothe_id',true) !!}">
                                                        <div class="controls">
                                                            {!! Form::label('Clothes', __('Clothes').':') !!}
                                                            {!! Form::select('clothe_id[]',[__('Select Clothes')]+$clothe,isset($result->id) ? $result->clothe_id:old('clothe_id'),['class'=>'form-control clothe_id']) !!}
                                                        </div>

                                                        {!! formError($errors,'clothe_id') !!}
                                                    </div>

                                                    <div class="form-group col-sm-5{!! formError($errors,'size',true) !!}">
                                                        <div class="controls">
                                                            {{ Form::label('size',__('size')) }}
                                                            {!! Form::text('size[]',old('size'),['class'=>'form-control']) !!}
                                                        </div>
                                                        {!! formError($errors,'size') !!}
                                                    </div>

                                                    <div class="col-sm-2 form-group">
                                                        <a href="javascript:void(0);" onclick="$(this).closest('.cleanersRow').remove();count_total_price();" class="text-danger">
                                                            <i class="fa fa-lg fa-trash mt-3"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                            @endif
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


                <div id="cleanersTemp" style="visibility: hidden;display: table;width:100%;" >

                    <div class="cleanersRow" >

                        <div class="form-group col-sm-5{!! formError($errors,'clothe_id',true) !!}">
                            <div class="controls">
                                {!! Form::label('Clothes', __('Clothes').':') !!}
                                {!! Form::select('clothe_id[]',[__('Select Clothes')]+$clothe,isset($result->id) ? $result->clothe_id:old('clothe_id'),['id'=>'','class'=>'form-control clothe','style'=>'width:100%;']) !!}
                            </div>

                            {!! formError($errors,'clothe_id') !!}
                        </div>

                        <div class="form-group col-sm-5{!! formError($errors,'size',true) !!}">
                            <div class="controls">
                                {{ Form::label('size',__('size')) }}
                                {!! Form::text('size[]','',['id'=>'','class'=>'form-control']) !!}
                            </div>
                            {!! formError($errors,'size') !!}
                        </div>


                        <div class="col-sm-2 form-group">
                            <a href="javascript:void(0);" onclick="$(this).closest('.cleanersRow').remove();" class="text-danger">
                                <i class="fa fa-lg fa-trash mt-3"></i>
                            </a>
                        </div>
                    </div>

                </div>
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
        function addRow(){

            var length = $('#cleaners .cleanersRow').length;
            var clonedRow = $('#cleanersTemp').clone();
            clonedRow.find('.clothe').attr('id','clothe_'+length);
            $('#cleaners').append(clonedRow.html());
            $('#clothe_'+length).select2();

        }

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