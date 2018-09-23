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
                            {!! Form::open(['route' => isset($result->id) ? ['system.projects.attendance-update',$result->id]:['system.projects.attendance-store',$project->id],'method' => isset($result->id) ?  'PATCH' : 'POST']) !!}

                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h2>{{__('Project Data')}}</h2>
                                    </div>
                                    <div class="card-block card-dashboard">

                                        <div class="form-group col-sm-6{!! formError($errors,'type',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('type',__('Type')) }}
                                                @if(!isset($result->id))
                                                {!! Form::select('type',[''=>__('Select type'),'absence'=>__('Absence'),'presence'=>__('Presence')],(!empty(old('type')))? old('type'): '',['style'=>'width: 100%;' ,'id'=>'type','class'=>'form-control col-md-12']) !!}
                                                @else
                                                    {!! Form::select('type',[  'presence'=>__('Presence')],(!empty(old('type')))? old('type'): '',['style'=>'width: 100%;' ,'id'=>'type','class'=>'form-control col-md-12']) !!}

                                                @endif
                                            </div>
                                            {!! formError($errors,'type') !!}
                                        </div>




                                        <div class="col-xs-12" >

                                            <a  href="javascript:;" class="btn btn-primary text-center" onclick="$('input[name=\'cleaner_id[]\']').prop('checked',true)" >{{__('select All')}} <i class="fa fa-star" ></i> </a>
                                            <a  href="javascript:;" class="btn btn-outline-warning text-center" onclick="$('input[name=\'cleaner_id[]\']').prop('checked',false)" >{{__('Deselect All')}} <i class="fa fa-star-o" ></i> </a>
                                        </div>

                                        <div class="form-group col-sm-12">

                                            @if(isset($result->id))
                                        @foreach($cleaners as $key => $row)
                                        <div class="form-group col-sm-3 {!! formError($errors,'cleaner_id',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('cleaner_id',$row->cleaner->Fullname) }}
                                                {!! Form::checkbox('cleaner_id[]', $cleaners[$key]->cleaner_id ,($cleaners[$key]->type == 'presence' )? true:false,['style'=>'width: 30%;' ,'id'=>'cleaner_id[]','class'=>'form-control col-md-4']) !!}

                                            </div>
                                            {!! formError($errors,'cleaner_id') !!}
                                        </div>
                                        @endforeach
                                                @elseif(!empty(old('cleaner_id'))   )
                                                @foreach($cleaners as $key => $row)
                                                <div class="form-group col-sm-3 {!! formError($errors,'cleaner_id',true) !!}">
                                                    <div class="controls">
                                                        {{ Form::label('cleaner_id',$row->cleaner->Fullname) }}
                                                        {!! Form::checkbox('cleaner_id[]',$row->cleaner_id,(in_array($row->cleaner_id,old('cleaner_id')))? true:false,['style'=>'width: 30%;' ,'id'=>'cleaner_id[]','class'=>'form-control col-md-4']) !!}

                                                    </div>
                                                    {!! formError($errors,'cleaner_id') !!}
                                                </div>
                                                    @endforeach
                                            @else
                                                        @foreach($cleaners as $key => $row)
                                                <div class="form-group col-sm-3 {!! formError($errors,'cleaner_id',true) !!}">
                                                    <div class="controls">
                                                        {{ Form::label('cleaner_id',$row->cleaner->Fullname) }}
                                                        {!! Form::checkbox('cleaner_id[]',$row->cleaner_id,false,['style'=>'width: 30%;' ,'id'=>'cleaner_id[]','class'=>'form-control col-md-4']) !!}

                                                    </div>
                                                    {!! formError($errors,'cleaner_id') !!}
                                                </div>
                                                @endforeach


                                            @endif
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

        ajaxSelect2('#client','client','',"{{route('system.ajax.get')}}");


    </script>
@endsection