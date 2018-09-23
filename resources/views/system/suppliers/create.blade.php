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
                        {!! Form::open(['route' => isset($result->id) ? ['system.supplier.update',$result->id]:'system.supplier.store','method' => isset($result->id) ?  'PATCH' : 'POST','files'=> true]) !!}
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-block card-dashboard">

                                        <div class="form-group col-sm-6{!! formError($errors,'supplier_category_id',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('supplier_category_id', __('Supplier Category')) !!}
                                                {!! Form::select('supplier_category_id',['0'=>'Select Category']+$supplier_categories,isset($result->id) ? $result->supplier_category_id:old('supplier_category_id'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'supplier_category_id') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'company_name',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('company_name', __('Company Name')) !!}
                                                {!! Form::text('company_name',isset($result->id) ? $result->company_name:old('company_name'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'company_name') !!}
                                        </div>

                                    <div class="form-group col-sm-6{!! formError($errors,'name',true) !!}">
                                        <div class="controls">
                                            {!! Form::label('name', __('Name')) !!}
                                            {!! Form::text('name',isset($result->id) ? $result->name:old('name'),['class'=>'form-control']) !!}
                                        </div>
                                        {!! formError($errors,'name') !!}
                                    </div>


                                        <div class="form-group col-sm-6{!! formError($errors,'email',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('email', __('Email')) !!}
                                                {!! Form::email('email',isset($result->id) ? $result->email:old('email'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'email') !!}
                                        </div>






                                        <div class="form-group col-sm-4{!! formError($errors,'phone1',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('phone1', __('Phone1')) !!}
                                                {!! Form::text('phone1',isset($result->id) ? $result->phone1:old('phone1'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'phone1') !!}
                                        </div>
                                        <div class="form-group col-sm-4{!! formError($errors,'phone2',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('phone2', __('Phone2')) !!}
                                                {!! Form::text('phone2',isset($result->id) ? $result->phone2:old('phone2'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'phone2') !!}
                                        </div>
                                        <div class="form-group col-sm-4{!! formError($errors,'phone3',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('phone3', __('Phone3')) !!}
                                                {!! Form::text('phone3',isset($result->id) ? $result->phone3:old('phone3'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'phone3') !!}
                                        </div>

                                        <div class="form-group col-sm-4{!! formError($errors,'mobile1',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('mobile1', __('Mobile1')) !!}
                                                {!! Form::text('mobile1',isset($result->id) ? $result->mobile1:old('mobile1'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'mobile1') !!}
                                        </div>
                                        <div class="form-group col-sm-4{!! formError($errors,'mobile2',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('mobile2', __('Mobile2').':') !!}
                                                {!! Form::number('mobile2',isset($result->id) ? $result->mobile2:old('mobile2'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'mobile2') !!}
                                        </div>
                                        <div class="form-group col-sm-4{!! formError($errors,'mobile3',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('mobile3', __('Mobile3').':') !!}
                                                {!! Form::text('mobile3',isset($result->id) ? $result->mobile3:old('mobile3'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'mobile3') !!}
                                        </div>





                                        <div class="form-group col-sm-6{!! formError($errors,'status',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('status', __('Status')) !!}
                                                {!! Form::select('status',[''=>__('Select Status'),'active'=>__('Active'),'in-active'=>__('In-active')],isset($result->id) ? $result->status:old('status'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'status') !!}
                                        </div>
                                        <div class="form-group col-sm-6{!! formError($errors,'init_credit',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('init_credit', __('Init Credit')) !!}
                                                {!! Form::number('init_credit',isset($result->id) ? $result->init_credit:old('init_credit'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'init_credit') !!}
                                        </div>
                                        <div class="form-group col-sm-6{!! formError($errors,'address',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('address', __('Address')) !!}
                                                {!! Form::textarea('address',isset($result->id) ? $result->address:old('address'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'address') !!}
                                        </div>
                                        <div class="form-group col-sm-6{!! formError($errors,'description_en',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('description', __('Description')) !!}
                                                {!! Form::textarea('description',isset($result->id) ? $result->description:old('description'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'description') !!}
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
        staffSelect('#staffSelect2')

    </script>
@endsection