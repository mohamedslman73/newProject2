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
                            {!! Form::open(['route' => isset($result->id) ? ['system.attendance-group-update',$result->id]:['system.attendance-group-store'],'method' => isset($result->id) ?  'PATCH' : 'POST']) !!}

                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h2>{{__('Project Data')}}</h2>
                                    </div>
                                    <div class="card-block card-dashboard">
                                        <div class="form-group col-sm-6{!! formError($errors,'type',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('type',__('Type')) }}
                                                {!! Form::select('type',isset($result->id) ? [$result->type =>$result->type]:[''=>__('Select type'),'absence'=>__('Absence'),'presence'=>__('Presence')],isset($result->id) ? $result->type:old('type'),['style'=>'width: 100%;' ,'id'=>'type','class'=>'form-control col-md-12']) !!}
                                            </div>
                                            {!! formError($errors,'type') !!}
                                        </div>
                                        <div class="form-group col-sm-6{!! formError($errors,'permission_group_id',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('permission_group_id', __('Permission Group')) !!}
                                                {!! Form::select('permission_group_id',['0'=>'Select Permission Group']+$permissionGroup,isset($result->id) ? $result->permission_group_id:old('permission_group_id'),['class'=>'form-control permission_group_id','onchange'=>'permission()']) !!}
                                            </div>

                                            {!! formError($errors,'permission_group_id') !!}
                                        </div>

                                     <div id="attendance"></div>

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
    function permission() {
        var permission_group_id = $('#permission_group_id').val();
        //alert(created_at1);
        $.ajax({
            type : 'post',
            dataType : 'html',
            url : '{{route('system.attendance-group-ajax')}}',
            data : "permission_group_id=" + permission_group_id,
            success: function (response) {
                console.log(response);
                $('#attendance').html(response);
            }
        });
    }
            $(function () {
            permission();
                $('.permission_group_id').select2();

        });
    </script>
@endsection