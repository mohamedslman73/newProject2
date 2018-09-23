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
                            {!! Form::open(['route' => isset($result->id) ? ['system.staff-target.update',$result->id]:'system.staff-target.store','files'=>true, 'method' => isset($result->id) ?  'PATCH' : 'POST']) !!}
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-block card-dashboard">

                                        <div class="form-group col-sm-12{!! formError($errors,'staff_id',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('staff', __('Staff').':') !!}
                                                {!! Form::text('staff',isset($result->id) ? __('#ID:').$result->staff->id .' '. $result->staff->firstname.' '.$result->staff->lastname: __('#ID:').$staff_data->id .' '.$staff_data->firstname.' '.$staff_data->lastname,['class'=>'form-control','readonly']) !!}
                                            </div>
                                            {!! formError($errors,'staff') !!}
                                        </div>

                                        {!! Form::hidden('staff_id',isset($result->id) ? $result->staff->id : $staff_data->id) !!}

                                        <div class="form-group {{iif( (isset($result->id) && $result->staff->is_supervisor()) || ( !isset($result->id) && $staff_data->is_supervisor() ) ,'col-sm-6','col-sm-4')}}{!! formError($errors,'month',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('month', __('Month').':') !!}
                                                {!! Form::number('month',isset($result->id) ? $result->month:old('month'),isset($result->id) ? ['class'=>'form-control','readonly']:['class'=>'form-control','max'=> 12]) !!}
                                            </div>
                                            {!! formError($errors,'month') !!}
                                        </div>

                                        <div class="form-group {{iif( (isset($result->id) && $result->staff->is_supervisor()) || ( !isset($result->id) && $staff_data->is_supervisor() ) ,'col-sm-6','col-sm-4')}}{!! formError($errors,'year',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('year', __('Year').':') !!}
                                                {!! Form::number('year',isset($result->id) ? $result->year:old('year'),isset($result->id) ? ['class'=>'form-control','readonly']:['class'=>'form-control','max'=> date('Y')]) !!}
                                            </div>
                                            {!! formError($errors,'year') !!}
                                        </div>

                                        @if( !( (isset($result->id) && $result->staff->is_supervisor()) || ( !isset($result->id) && $staff_data->is_supervisor() )) )
                                        <div class="form-group col-sm-4{!! formError($errors,'amount',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('amount', __('Target').':') !!}
                                                {!! Form::number('amount',isset($result->id) ? $result->amount:old('amount'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'amount') !!}
                                        </div>
                                        @else
                                            {!! Form::hidden('amount',0) !!}
                                        @endif

                                        <div class="form-group col-sm-12{!! formError($errors,'description',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('description', __('Description').':') !!}
                                                {!! Form::textarea('description',isset($result->id) ? $result->description:old('description'),['class'=>'form-control','rows'=>3]) !!}
                                            </div>
                                            {!! formError($errors,'description') !!}
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
                </section>
                <!--/ Javascript sourced data -->
            </div>
        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->
@endsection
@section('footer')
    <script src="{{asset('assets/system')}}/vendors/js/forms/select/select2.full.min.js" type="text/javascript"></script>
    <script src="{{asset('assets/system')}}/js/scripts/select2/select2.custom.js" type="text/javascript"></script>
    <script src="//maps.googleapis.com/maps/api/js?key={{env('gmap_key')}}&libraries=places&callback=initAutocomplete" type="text/javascript" async defer></script>
    <script src="{{asset('assets/system')}}/vendors/js/charts/gmaps.min.js" type="text/javascript"></script>
@endsection