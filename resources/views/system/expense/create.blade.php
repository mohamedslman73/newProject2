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
                            {!! Form::open(['route' => isset($result->id) ? ['system.expenses.update',$result->id]:'system.expenses.store','method' => isset($result->id) ?  'PATCH' : 'POST']) !!}

                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h2>{{__('Expense Causes Data')}}</h2>
                                    </div>
                                    <div class="card-block card-dashboard">

                                        <div class="form-group col-sm-6{!! formError($errors,'expense_causes_id',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('expense_causes_id', __('Select Expense Causes')) !!}
                                                {!! Form::select('expense_causes_id',['0'=>'Select Expense Causes']+$expense_causes,isset($result->id) ? $result->expense_causes_id:old('expense_causes_id'),['class'=>'form-control expense_causes_id']) !!}
                                            </div>
                                            {!! formError($errors,'expense_causes_id') !!}
                                        </div>

                                        <div class="form-group col-md-6{!! formError($errors,'date',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('date', __('Date').':') !!}
                                                {!! Form::text('date',isset($result->id) ? $result->date:old('date'),['class'=>'form-control datepicker']) !!}
                                            </div>
                                            {!! formError($errors,'date') !!}
                                        </div>

                                        <div class="form-group supplier col-sm-12{!! formError($errors,'supplier_id',true) !!}" style="display:none;">
                                            <div class="controls">
                                                {{ Form::label('supplier_id',__('Supplier')) }}
                                                {!! Form::select('supplier_id',isset($result->id) ? [$result->supplier_id =>$result->supplier->name]:[''=>__('Select Supplier')],isset($result->id) ? $result->supplier_id:old('supplier_id'),['style'=>'width: 100%;' ,'id'=>'supplier','class'=>'form-control col-md-12']) !!}
                                            </div>
                                            {!! formError($errors,'supplier_id') !!}
                                        </div>
                                        <div class="form-group col-sm-12{!! formError($errors,'amount',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('amount', __('Amount')) !!}
                                                {!! Form::number('amount',isset($result->id) ? $result->amount:old('amount'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'amount') !!}
                                        </div>
                                        <div class="form-group col-sm-12{!! formError($errors,'description',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('description', __('Description (Option):')) !!}
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
            ajaxSelect2('#supplier','supplier','',"{{route('system.ajax.get')}}");
            $(document).ready(function() {
                type();
                $('.expense_causes_id').select2();
            });
            function type(){
                var expence_id = $('.expense_causes_id').val();
                if (expence_id ==1) {
                    $('.supplier').show();
                }else {
                    $('.supplier').hide();
                }
            }
            $('.expense_causes_id').change(function(){
                type();
            });
            $(function(){

                $('.datepicker').datetimepicker({
                    viewMode: 'months',
                    format: 'YYYY-MM-DD'
                });
            });
        </script>
@endsection