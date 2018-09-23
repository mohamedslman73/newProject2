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
                            {!! Form::open(['route' => isset($result->id) ? ['system.call.update',$result->id]:'system.call.store', 'method' => isset($result->id) ?  'PATCH' : 'POST']) !!}
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h2>{{__('Call data')}}</h2>
                                    </div>
                                    <div class="card-block card-dashboard">

                                        <div class="form-group col-sm-12{!! formError($errors,'call_type',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('status', __('Client Type').':') !!}
                                                {!! Form::select('call_type',['client'=>__('Client'),'other'=>__('Other')],null,['class'=>'form-control','id'=>'type']) !!}
                                            </div>
                                            {!! formError($errors,'call_type') !!}
                                        </div>
                                        <div id="other" style="display: none">
                                            <div class="form-group col-sm-12{!! formError($errors,'phone_number',true) !!}">
                                                <div class="controls">
                                                    {!! Form::label('phone_number', __('Phone number').':') !!}
                                                    {!! Form::text('phone_number',isset($result->id) ? $result->phone_number:old('phone_number'),['class'=>'form-control']) !!}
                                                </div>
                                                {!! formError($errors,'phone_number') !!}
                                            </div>

                                            <div class="mt-1 form-group col-sm-12{!! formError($errors,'client_name',true) !!}">
                                                <div class="controls">
                                                    {!! Form::label('client_name', __('Caller name').':') !!}
                                                    {!! Form::text('client_name',isset($result->id) ? $result->client_name:old('client_name'),['class'=>'form-control']) !!}
                                                </div>
                                                {!! formError($errors,'client_name') !!}
                                            </div>
                                        </div>


                                        <div id="client">
                                            <div class="form-group col-sm-12{!! formError($errors,'client_id',true) !!}">
                                                <div class="controls">
                                                    {!! Form::label('client_id', __('Select Client').':') !!}
                                                    {!! Form::select('client_id',[''=>__('Select Client')],isset($result->id) ? $result->client_id:old('client_id'),['style'=>'width: 100%;' ,'id'=>'client_id','class'=>'form-control col-md-12']) !!}
                                                </div>
                                                {!! formError($errors,'client_id') !!}
                                            </div>

                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'call_type',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('status', __('Call Type').':') !!}
                                                {!! Form::select('type',['in'=>__('IN'),'out'=>__('OUT')],isset($result->id) ? $result->type:old('type'),['class'=>'form-control','id'=>'call_type']) !!}
                                            </div>
                                            {!! formError($errors,'type') !!}
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'call_propose',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('call_propose', __('Call Propose').':') !!}
                                                {!! Form::select('call_propose',['normal'=>__('Normal'),'complain'=>__('Complain')],null,['class'=>'form-control','id'=>'call_propose']) !!}
                                            </div>
                                            {!! formError($errors,'call_propose') !!}
                                        </div>
                                        <div id="normal">


                                            <div class="form-group col-sm-6{!! formError($errors,'reminder',true) !!}">
                                                <div class="controls">
                                                    {!! Form::label('reminder', __('Reminder').':') !!}
                                                    {!! Form::text('reminder',isset($result->id) ? $result->reminder:old('reminder'),['class'=>'form-control datetimepicker']) !!}
                                                </div>
                                                {!! formError($errors,'reminder') !!}
                                            </div>
                                        </div>


                                        <div  id="complain" style="display: none">


                                            <div class="form-group col-sm-6{!! formError($errors,'project_id',true) !!}">
                                                <div class="controls">
                                                    {!! Form::label('project_id', __('Select Project (Complain)')) !!}
                                                    {!! Form::select('project_id',['0'=>'Select Project']+$projects,isset($result->id) ? $result->project_id:old('project_id'),['class'=>'form-control project_id']) !!}
                                                </div>
                                                {!! formError($errors,'project_id') !!}
                                            </div>

                                            <div class="form-group col-md-6{!! formError($errors,'complain_of_staff_id',true) !!}">
                                                <div class="controls">
                                                    {{ Form::label('staffSelect2',__('Complain of Staff (optional):')) }}
                                                    {!! Form::select('complain_of_staff_id',[''=>__('Select Staff')],null,['style'=>'width: 100%;' ,'id'=>'staffSelect2','class'=>'form-control col-md-12']) !!}
                                                </div>
                                                {!! formError($errors,'complain_of_staff_id') !!}
                                            </div>



                                            <div class="form-group col-md-12{!! formError($errors,'order_date',true) !!}">
                                                <div class="controls">
                                                    {!! Form::label('order_date', __('Date (Complain)').':') !!}
                                                    {!! Form::text('order_date',isset($result->id) ? $result->order_date:old('order_date'),['class'=>'form-control datepicker']) !!}
                                                </div>
                                                {!! formError($errors,'order_date') !!}
                                            </div>


                                        </div>
                                        <div class="form-group col-sm-6{!! formError($errors,'call_time',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('call_time', __('Call Time').':') !!}
                                                {{--{!! Form::date('call_time',date('Y-m-d h:iA'),['class'=>'form-control ']) !!}--}}
                                                {!! Form::text('call_time',date('Y-m-d  h:i:s', time()),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'call_time') !!}
                                        </div>
                                        {{--{!! Form::hidden('calltime',date('Y-m-d H:i:s')) !!}--}}
                                        <div class="form-group col-sm-12{!! formError($errors,'status',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('status', __('Call Status').':') !!}
                                                {!! Form::select('status',[''=>'Select Status','high'=>__('High'),'intermediate'=>__('Intermediate'),'low'=>'Low'],isset($result->id) ? $result->status:old('status'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'status') !!}
                                        </div>

                                        <div class="mt-1 form-group col-sm-12{!! formError($errors,'call_details',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('call_details', __('Call details (Call/Complain)').':') !!}
                                                {!! Form::textarea('call_details',isset($result->id) ? $result->call_details:old('call_details'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'call_details') !!}
                                        </div>

                                    </div>
                                </div>
                            </div>

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
                    {!! Form::close() !!}
                    <!--/ Javascript sourced data -->
                    </div>
                </section>
            </div>
        </div>
        <!-- ////////////////////////////////////////////////////////////////////////////-->
    </div>
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

        ajaxSelect2('#client_id','client','',"{{route('system.ajax.get')}}");
        ajaxSelect2('#client_id2','client','',"{{route('system.ajax.get')}}");
        ajaxSelect2('#staffSelect2','staff','',"{{route('system.ajax.get')}}");

        $(document).ready(function(){

            selectPropose();
        })

        function selectType(){
            if($('#type').val() == 'client'){
                $('#client').show();
                $('#other').hide();
            }else if($('#type').val() == 'other'){
                $('#other').show();
                $('#client').hide();
            }else{
                $('#client').show();
                $('#other').hide();
            }
        }

        $('#type').change(function(){
            selectType();
        });
        function selectPropose(){
            if($('#call_propose').val() == 'normal'){
                $('#normal').show();
                $('#complain').hide();
            }else if($('#call_propose').val() == 'complain'){
                $('#complain').show();
                $('#normal').hide();
            }else{
                $('#normal').show();
                $('#complain').hide();
            }
        }

        $('#call_propose').change(function(){
            selectPropose();
        });

        $('.datetimepicker').datetimepicker({
            viewMode: 'months',
            format: 'YYYY-MM-DD HH:mm:SS'
        });
        $('.datepicker').datetimepicker({
            viewMode: 'months',
            format: 'YYYY-MM-DD'
        });

    </script>
@endsection