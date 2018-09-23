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
                        {!! Form::open(['route' => isset($result->id) ? ['system.attendance.update',$result->id]:'system.attendance.store','method' => isset($result->id) ?  'PATCH' : 'POST','files'=> true]) !!}
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-block card-dashboard">
                                    <div class="form-group col-sm-6{!! formError($errors,'project_id',true) !!}">
                                        <div class="controls">
                                            {!! Form::label('project_id', __('Project')) !!}
                                            {!! Form::select('project_id',isset($result->id)? [$result->project_id=>$result->project->name] :['0'=>'Select project'],isset($result->id) ? $result->project_id:old('project_id'),['id'=>'project_id','class'=>'form-control']) !!}
                                         </div>
                                        {!! formError($errors,'project_id') !!}
                                    </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'bus_brand_id',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('cleaner_id', __('Cleaner')) !!}
                                                {!! Form::select('cleaner_id',isset($result->id)? [$result->cleaner_id=>$result->cleaner->Fullname] :['0'=>'Select Cleaner'],isset($result->id) ? $result->cleaner_id:old('cleaner_id'),['id'=>'cleaner_id','class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'cleaner_id') !!}
                                        </div>


                                        <div class="form-group col-sm-12{!! formError($errors,'date',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('date', __('Date')) !!}
                                                {!! Form::text('date',isset($result->id)?$result->date:old('date'),['class'=>'form-control datepicker','id'=>'date']) !!}
                                               </div>
                                            {!! formError($errors,'date') !!}
                                        </div>




                                        <div class="form-group col-sm-12{!! formError($errors,'date',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('notes', __('Notes')) !!}
                                                {!! Form::textarea('notes',isset($result->id)?$result->notes:old('notes'),['class'=>'form-control']) !!}
                                               </div>
                                            {!! formError($errors,'notes') !!}
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
    <!-- END PAGE VENDOR JS-->

    <script src="{{asset('assets/system/js/scripts/pickers/dateTime/picker-date-time.js')}}" type="text/javascript"></script>



  <script type="text/javascript">
        ajaxSelect2('#project_id','project','',"{{route('system.ajax.get')}}");
        ajaxSelect2('#cleaner_id','staff','',"{{route('system.ajax.get')}}");

        $(function(){
            $('.datepicker').datetimepicker({
                viewMode: 'months',
                format: 'YYYY-MM-DD'
            });
        });
    </script>
@endsection