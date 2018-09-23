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
                            {!! Form::open(['route' => isset($permission_group->id) ? ['system.permission-group.update',$permission_group->id]:'system.permission-group.store','files'=>true, 'method' => isset($permission_group->id) ?  'PATCH' : 'POST']) !!}
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-block card-dashboard">

                                        <div class="form-group col-sm-12{!! formError($errors,'name',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('name', __('Name').':') !!}
                                                {!! Form::text('name',isset($permission_group->id) ? $permission_group->name:old('name'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'name') !!}
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'is_supervisor',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('is_supervisor', __('Supervisor').':') !!}
                                                {!! Form::select('is_supervisor',['yes'=>__('Yes'),'no'=>__('No')],isset($permission_group->id) ? $permission_group->is_supervisor:old('is_supervisor'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'is_supervisor') !!}
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'whitelist_ip',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('whitelist_ip', __('Whitelist IPs').':') !!}
                                                {!! Form::textarea('whitelist_ip',isset($permission_group->id) ? $permission_group->whitelist_ip:old('whitelist_ip'),['class'=>'form-control','placeholder'=>__('Add 1 IP per line or leave it blank to access from anywhere')]) !!}
                                            </div>
                                            {!! formError($errors,'whitelist_ip') !!}
                                        </div>



                                        <div class="form-group col-sm-12">
                                            <div class="mb-2 col-center">
                                                <a href="javascript:void(0);" class="btn btn-primary text-center" onclick="$('input[name=\'permissions[]\']').prop('checked',true)">
                                                    <i class="fa fa-star"></i> {{__('Select All')}}
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-outline-warning text-center" onclick="$('input[name=\'permissions[]\']').prop('checked',false)">
                                                    <i class="fa fa-star-o"></i> {{__('Deselect All')}}
                                                </a>
                                                @if(staffCan('download.permission-group.excel'))
                                                    <a onclick="filterFunction($('#filterForm'),true)"  class="btn btn-outline-primary"><i class="ft-download"></i> {{__('Download Excel')}}</a>
                                                @endif
                                            </div>
                                                @foreach($permissions as $permission)
                                                    <div style="margin-bottom: 20px;" class="bs-callout-primary callout-border-left callout-bordered p-2 permissions">
                                                        <h4 class="primary pull-left">{{ucfirst($permission['name'])}}</h4>
                                                        <label class="pull-right">
                                                            <input type="checkbox" onclick="CheckPerms(this);">
                                                        </label>
                                                        <p class="primary col-sm-12">{!! $permission['description']!!}</p>
                                                        <div class="row">
                                                            @foreach($permission['permissions'] as $key=>$val)
                                                                <label class="col-sm-4">
                                                                    {!! Form::checkbox("permissions[]", "$key", isset($permission_group->id) ? !array_diff($val,$currentpermissions) : false) !!}
                                                                    {!! ucfirst(str_replace('-',' ',$key)) !!}
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                @endforeach
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
                    </div>
                </section>
                <!--/ Javascript sourced data -->
            </div>
        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->
@endsection
@section('footer')
    <script>
        function CheckPerms(perm) {
            var permessions = $(perm).parents('.permissions').find('input[type=\'checkbox\']');
            //console.log(permessions);
            if($(perm).is(':checked')){
                $(permessions).prop('checked',true);
            } else {
                $(permessions).prop('checked',false);
            }
        }
        function filterFunction($this,downloadExcel= false){
            if($this == false) {
                $url = '{{url()->full()}}?isDataTable=true&downloadExcel='+downloadExcel;
            }else {
                $url = '{{url()->full()}}?isDataTable=true&'+$this.serialize()+'&downloadExcel='+downloadExcel;
            }
            if(downloadExcel == true)
                window.location = $url;
            else {
                $dataTableVar.ajax.url($url).load();
                $('#filter-modal').modal('hide');
            }
        }

    </script>
@endsection