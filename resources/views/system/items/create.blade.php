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
                        {!! Form::open(['route' => isset($result->id) ? ['system.item.update',$result->id]:'system.item.store','method' => isset($result->id) ?  'PATCH' : 'POST','files'=> true]) !!}
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-block card-dashboard">

                                    <div class="form-group col-sm-6{!! formError($errors,'name',true) !!}">
                                        <div class="controls">
                                            {!! Form::label('name', __('Name')) !!}
                                            {!! Form::text('name',isset($result->id) ? $result->name:old('name'),['class'=>'form-control']) !!}
                                        </div>
                                        {!! formError($errors,'name') !!}
                                    </div>


                                        <div class="form-group col-sm-6{!! formError($errors,'code',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('code', __('Code')) !!}
                                                {!! Form::text('code',isset($result->id) ? $result->code:old('code'),['class'=>'form-control ']) !!}
                                            </div>
                                            {!! formError($errors,'code') !!}
                                        </div>
                                        <div class="form-group col-sm-12{!! formError($errors,'item_category_id',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('item_category_id', __('Item Category')) !!}

                                                @if(isset($result->id))
                                                    <select name="item_category_id" id="item_category_id" class="form-control col-md-12" style="width: 100%;"> {{getCategoryTreeSelect(0,'',$result->item_category_id)}} </select>
                                                @else
                                                    <select name="item_category_id" id="item_category_id" class="form-control col-md-12" style="width: 100%;"> {{getCategoryTreeSelect()}} </select>
                                                @endif


                                            </div>
                                            {!! formError($errors,'item_category_id') !!}
                                        </div>

                                        {{--<div class="form-group col-sm-6{!! formError($errors,'count',true) !!}">--}}
                                            {{--<div class="controls">--}}
                                                {{--{!! Form::label('count', __('Count')) !!}--}}
                                                {{--{!! Form::number('count',isset($result->id) ? $result->count:old('count'),['class'=>'form-control']) !!}--}}
                                            {{--</div>--}}
                                            {{--{!! formError($errors,'count') !!}--}}
                                        {{--</div>--}}
                                        <div class="form-group col-sm-6{!! formError($errors,'min_count',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('Min Count', __('Min Count')) !!}
                                                {!! Form::number('min_count',isset($result->id) ? $result->min_count:old('min_count'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'min_count') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'price',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('price', __('Price')) !!}
                                                {!! Form::number('price',isset($result->id) ? $result->price:old('price'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'price') !!}
                                        </div>
                                        <div class="form-group col-sm-6{!! formError($errors,'image',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('image', __('Image').':') !!}
                                                {!! Form::file('image',['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'image') !!}
                                        </div>
                                        <div class="form-group col-sm-6{!! formError($errors,'item_category_id',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('unite', __('Unite')) !!}
                                                {!! Form::select('unite',[''=>__('Select unite'),'kg'=>__('Kg'),'g'=>__('g'),'l'=>__('L'),'pice'=>__('Pice'),'unite'=>__('Unite')],isset($result->id) ? $result->unite:old('unite'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'status') !!}
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'description_en',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('description', __('Description')) !!}
                                                {!! Form::textarea('description',isset($result->id) ? $result->description:old('description'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'description') !!}
                                        </div>
                                        <div class="form-group col-sm-12{!! formError($errors,'status',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('status', __('Status').':') !!}
                                                {!! Form::select('status',[''=>__('Select Status'),'active'=>__('Active'),'in-active'=>__('In-active')],isset($result->id) ? $result->status:old('status'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'status') !!}
                                        </div>

                                    </div>
                                </div>
                            </div>

                                    {!! Form::hidden('id',isset($result->id) ? $result->id:old('id'),['class'=>'form-control ar']) !!}

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
    <!-- END PAGE VENDOR JS-->

    <script src="{{asset('assets/system/js/scripts/pickers/dateTime/picker-date-time.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
   // ajaxSelect2('#item_category_id','item_category','',"{{route('system.ajax.get')}}");
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