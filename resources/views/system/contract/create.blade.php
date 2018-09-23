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
                            {!! Form::open(['route' => isset($result->id) ? ['system.contract.update',$result->id]:'system.contract.store','files'=>true,'method' => isset($result->id) ?  'PATCH' : 'POST']) !!}

                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h2>{{__('Contract Data')}}</h2>
                                    </div>
                                    <div class="card-block card-dashboard">

                                        <div class="form-group col-sm-6{!! formError($errors,'client',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('project',__('project')) }}
                                                {!! Form::select('project_id',isset($result->id) ? [$result->project_id =>$result->project->name]:[''=>__('Select project')],isset($result->id) ? $result->project_id:old('project_id'),['style'=>'width: 100%;' ,'id'=>'project','class'=>'form-control col-md-12']) !!}
                                            </div>
                                            {!! formError($errors,'project') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'name',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('date_from',__('Date From')) }}
                                                {!! Form::text('date_from',isset($result->id) ? $result->date_from: old('date_from'),['class'=>'form-control datepicker','id'=>'date_from']) !!}
                                            </div>
                                            {!! formError($errors,'date_from') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'name',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('date_to',__('Date To')) }}
                                                {!! Form::text('date_to',isset($result->id) ? $result->date_to: old('date_to'),['class'=>'form-control datepicker','id'=>'date_to']) !!}
                                            </div>
                                            {!! formError($errors,'date_to') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'file',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('file', __('File')) !!}
                                                {!! Form::file('file',['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'file') !!}
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'file',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('description', __('description')) !!}
                                                {!! Form::textArea('description',isset($result->id) ? $result->description: old('description'),['class'=>'form-control datepicker','id'=>'description']) !!}
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

        ajaxSelect2('#project','project','',"{{route('system.ajax.get')}}");
        ajaxSelect2('#quotation','quotation','',"{{route('system.ajax.get')}}");


        $(function(){
            $('.datepicker').datetimepicker({
                viewMode: 'months',
                format: 'YYYY-MM-DD'
            });
        });



    </script>
@endsection





