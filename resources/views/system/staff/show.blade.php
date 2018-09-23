@extends('system.layouts')
<div class="modal fade text-xs-left" id=""  role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <label class="modal-title text-text-bold-600" id="myModalLabel33">{{__('Add Managed Staff')}}</label>
            </div>
            {{--{!! Form::open(['route' => ['system.staff.add-managed-staff'],'method' => 'POST','id'=>'add-managed-staff-form','onsubmit'=>'addManagedStaffPOST();return false;']) !!}--}}
            <div class="modal-body">

                <div class="card-body">
                    <div class="card-block">
                        <div class="row">
                            <div class="alert" id="addManagedStaff-alert"></div>

                            <div class="col-md-12">
                                <fieldset class="form-group">
                                    {{ Form::label('staff_id',__('Staff ID')) }}
                                    {!! Form::number('staff_id',null,['class'=>'form-control']) !!}
                                </fieldset>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" id="addManagedStaff-button" class="btn btn-outline-primary btn-md">{{__('Submit')}}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- Button trigger modal -->


<!-- Modal -->
<form action="{{route('system.staff.edit-info')}}" method="post" id="staff-edit">
{{csrf_field()}}
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Staff Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group col-sm-6{!! formError($errors,'medical',true) !!}">
                    <div class="controls">
                        {!! Form::label('medical', __('Medical Status').':') !!}
                        {!! Form::select('medical',[''=>'Medical','yes'=>__('Yes'),'no'=>__('No')],isset($result->id) ? $result->medical:old('medical'),['class'=>'form-control']) !!}

                    </div>
                    {!! formError($errors,'medical') !!}
                </div>
                <div class="form-group col-sm-6{!! formError($errors,'blood',true) !!}">
                    <div class="controls">
                        {!! Form::label('blood', __('Blood Test Status').':') !!}
                        {!! Form::select('blood',[''=>'Select Blood Test Status','yes'=>__('Yes'),'no'=>__('No')],isset($result->id) ? $result->blood:old('blood'),['class'=>'form-control']) !!}
                    </div>
                    {!! formError($errors,'blood') !!}
                </div>
                <div class="form-group col-sm-6{!! formError($errors,'finger_print',true) !!}">
                    <div class="controls">
                        {!! Form::label('finger_print', __('Finger Print Test Status').':') !!}
                        {!! Form::select('finger_print',[''=>'Select Finger Print Test Status','yes'=>__('Yes'),'no'=>__('No')],isset($result->id) ? $result->finger_print:old('finger_print'),['class'=>'form-control']) !!}
                    </div>
                    {!! formError($errors,'finger_print') !!}
                </div>
                <div class="form-group col-sm-6{!! formError($errors,'government_id',true) !!}">
                    <div class="controls">
                        {!! Form::label('government_id', __('Government ID').':') !!}
                        {!! Form::text('government_id',isset($result->id) ? $result->government_id:old('government_id'),['class'=>'form-control']) !!}
                    </div>
                    {!! formError($errors,'visa_status') !!}
                </div>
                {!! Form::hidden('id',isset($result->id) ? $result->id:old('id'),['class'=>'form-control ar']) !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" onsubmit="staffEdit()" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
{{--</form>--}}
@section('content')
    <div class="messages" style="text-align: center"></div>
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                <div id="user-profile">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="card profile-with-cover">
                                <div class="card-img-top img-fluid bg-cover height-300" style="background: url('{{asset('assets/system/images/carousel/22.jpg')}}') 50%;"></div>
                                <div class="media profil-cover-details">
                                    @if($result->image)
                                        <div class="media-left pl-2 pt-2">
                                            <a href="jaascript:void(0);" class="profile-image">
                                                <img title="{{$result->firstname}} {{$result->lastname}}" src="{{asset('storage/app/'.imageResize($result->avatar,70,70))}}"  class="rounded-circle img-border height-100"  />
                                            </a>
                                        </div>
                                    @endif
                                    <div class="media-body media-middle row">
                                        <div class="col-xs-6">
                                            <h3 class="card-title" style="margin-bottom: 0.5rem;">
                                                {{$result->firstname}} {{$result->lastname}}
                                                @if($result->status == 'in-active')
                                                    <b style="color: red;">(IN-ACTIVE)</b>
                                                @endif
                                            </h3>
                                            <span>{{$result->address}}</span>
                                        </div>
                                        <div class="col-xs-6 text-xs-right">
                                        </div>
                                    </div>
                                </div>
                                <nav class="navbar navbar-light navbar-profile">
                                    <button class="navbar-toggler hidden-sm-up" type="button" data-toggle="collapse" data-target="#exCollapsingNavbar2" aria-controls="exCollapsingNavbar2" aria-expanded="false" aria-label="Toggle navigation"></button>
                                    <div class="collapse navbar-toggleable-xs" id="exCollapsingNavbar2">
                                        <ul class="nav navbar-nav float-xs-right">

                                            <li class="nav-item active">
                                                <a class="nav-link"  href="javascript:void(0);" data-toggle="modal"  data-target="#exampleModal"><i class="fa fa-pencil-square-o"></i> {{__('Edit Staff info')}} <span class="sr-only">(current)</span></a>
                                            </li>


                                        </ul>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <section id="spacing" class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        {{__('Staff Info')}}
                                     @if(empty($result->termination_date))
                                        <span style="float: right"><a  class="btn btn-outline-danger" onclick="staffTerminate( '{{route('system.staff-terminate',$result->id)}}')" href="javascript:void(0)">Terminate</a></span>
                                       @endif
                                        <a class="btn btn-outline-primary"  href="{{route('system.attendance.index')."?cleaner_id=".$result->id}}" target="_blank">Show Attendance</a><br><br>




                                    </h4>

                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{__('Value')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                <tr>
                                                    <td>{{__('ID')}}</td>
                                                    <td>{{$result->id}}</td>
                                                </tr>


                                                <tr>
                                                    <td>{{__('Name')}}</td>
                                                    <td>
                                                        {{$result->firstname}} {{$result->lastname}} ( {{$result->job_title}} )
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Nationality')}}</td>
                                                    <td>
                                                        <a href="mailto:{{$result->email}}">{{$result->email}}</a>
                                                        {{$result->nationality}}
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td>{{__('Gender')}}</td>
                                                    <td>
                                                        {{ucfirst($result->gender)}}
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td>{{__('Birthdate')}}</td>
                                                    <td>
                                                        {{$result->birthdate}}
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td>{{__('Passport Number')}}</td>
                                                    <td>
                                                        <code>{{$result->passport_number}}</code>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('Bank Account')}}</td>
                                                    <td>
                                                        <code>{{$result->bank_account}}</code>
                                                    </td>
                                                </tr>

                                                     <tr>
                                                    <td>{{__('Job Title')}}</td>
                                                    <td>
                                                        {{$result->job_title}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('Lenth Of Services')}}</td>
                                                    <td>
                                                        {{$lenth_of_services}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('Permission Group')}}</td>
                                                    <td>
                                                        @if($result->permission_group_id)
                                                        {{$result->permission_group->name}}
                                                            @else
                                                            --
                                                        @endif
                                                    </td>
                                                </tr>

                                                   <tr>
                                                    <td>{{__('Height')}}</td>
                                                    <td>
                                                        @if($result->length)
                                                        {{$result->length}}
                                                            @else
                                                            --
                                                        @endif
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Weight')}}</td>
                                                    <td>
                                                        @if($result->weight)
                                                        {{$result->weight}}
                                                            @else
                                                        --
                                                            @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('Contact In Home Country')}}</td>
                                                    <td>
                                                        @if($result->contact_in_home_country)
                                                        {{$result->contact_in_home_country}}
                                                            @else
                                                      <code>---</code>
                                                        @endif
                                                    </td>


                                                </tr>
                                                <tr>
                                                    <td>{{__('Weekly Vacations')}}</td>
                                                    <td>
                                                        {{--{{explode(',',$result->weekly_vacations)}}--}}
                                                        @foreach($weekly_vacations as $vacation)
                                                       <code>{{$vacation}}</code><br>
                                                            @endforeach
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td> {{__('Visa Data')}}</td>
                                                    <td>
                                                        <table>
                                                            <thead>
                                                            <th>{{__('Date Of Visa Issue')}}</th>
                                                            <th>{{__('Visa Number')}}</th>
                                                            <th>{{__('Visa Status ')}}</th>
                                                            </thead>
                                                            <tbody>


                                                                <tr>
                                                                    <td>{{$result->date_of_visa_issue}}</td>
                                                                    <td>{{$result->visa_number}}</td>
                                                                    <td>{{$result->visa_status}}</td>
                                                                </tr>

                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td> {{__('Clothes')}}</td>
                                                    <td>
                                                        <table>
                                                            <thead>
                                                            <th>{{__('Name')}}</th>
                                                            <th>{{__('Size')}}</th>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($result->staff_clothes as  $value)

                                                                <tr>
                                                                    <td>{{$value->clothe['name']}}</td>
                                                                    <td>{{$value['size']}}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td> {{__('More Staff Data')}}</td>
                                                    <td>
                                                        <table>
                                                            <thead>
                                                            <th>{{__('Medical')}}</th>
                                                            <th>{{__('Finger Print')}}</th>
                                                            <th>{{__('Blood')}}</th>
                                                            <th>{{__('Termination Date')}}</th>
                                                            </thead>
                                                            <tbody>


                                                            <tr>
                                                                @if($result->medical)
                                                                <td>{{$result->medical}}</td>
                                                                @else
                                                                  <td>--</td>
                                                                    @endif
                                                                    @if($result->finger_print)
                                                                <td>{{$result->finger_print}}</td>
                                                                    @else
                                                                        <td>--</td>
                                                                        @endif
                                                                    @if($result->blood)
                                                                <td>{{$result->blood}}</td>
                                                                        @else
                                                                <td>--</td>
                                                                        @endif
                                                                    @if($result->termination_date)
                                                                <td>
                                                                    <code>{{$result->termination_date}}</code>
                                                                </td>
                                                                        @else
                                                                <td>--</td>
                                                                        @endif
                                                            </tr>

                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>

                                                </tbody>
                                            </table>


                                        </div>
                                    </div>
                                </div>
                            </section>

                        </div>

                    </div>

                </div>

            </div>
        </div>

    <div class="modal fade text-xs-left" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <label class="modal-title text-text-bold-600" id="myModalLabel33">{{__('Filter')}}</label>
                </div>
                {!! Form::open(['method'=>'GET'])!!}
                <div class="modal-body">

                    <div class="card-body">
                        <div class="card-block">
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        {{ Form::label('created_at1',__('Created From')) }}
                                        {!! Form::text('created_at1',null,['class'=>'form-control datepicker','id'=>'created_at1']) !!}
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        {{ Form::label('created_at2',__('Created To')) }}
                                        {!! Form::text('created_at2',null,['class'=>'form-control datepicker','id'=>'created_at2']) !!}
                                    </fieldset>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">
                    <input type="submit" class="btn btn-outline-primary btn-md" value="{{__('Filter')}}">
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-map" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">View Map</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8" id="map"></div>
                    <div class="list-group-item col-md-12" id="instructions"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/extensions/pace.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/daterange/daterangepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/datetime/bootstrap-datetimepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/forms/selects/select2.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/core/menu/menu-types/vertical-overlay-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/pages/users.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/pages/timeline.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/treegrid/jquery.treegrid.css')}}">

    <style>
        #map{
            height: 500px !important;
            width: 100% !important;
        }
    </style>
@endsection

@section('footer')

    <script type="text/javascript" src="{{asset('assets/system/treegrid/jquery.treegrid.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/system/treegrid/jquery.treegrid.bootstrap3.js')}}"></script>

    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{asset('assets/system/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/dateTime/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/daterange/daterangepicker.js')}}" type="text/javascript"></script>
    <!-- END PAGE VENDOR JS-->


    <script src="//maps.googleapis.com/maps/api/js?key={{env('gmap_key')}}" type="text/javascript" async defer></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/gmaps.js/0.4.25/gmaps.min.js" type="text/javascript"></script>

    <script type="text/javascript">



        function filterFunction($this){
            if($this == false) {
                $url = '{{url()->full()}}?is_total=true';
            }else {
                $url = '{{url()->full()}}?is_total=true&'+$this.serialize();
            }

            $dataTableVar.ajax.url($url).load();
            $('#filter-modal').modal('hide');
        }

        $(function(){
            $('.datepicker').datetimepicker({
                viewMode: 'months',
                format: 'YYYY-MM-DD'
            });
        });
        $('#staff-edit').on('submit',function (e) {
                e.preventDefault();
                var data = $(this).serialize();
                var  url = $(this).attr('action');
                $.post(url,data,function (data) {
                    $('#exampleModal').modal('hide');
                    if (data.status == true){

                        var messages = $('.messages');

                        var successHtml = '<div class="alert alert-success">'+
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>'+ data.msg +
                            '</div>';

                        $(messages).html(successHtml);
                    }else {

                        var messages = $('.messages');
                        var msg = '';
                        for(var i in data.error){
                            var value = data.error[i];
                            msg += '<p>'+value+'</p>';
                        }
                        var successHtml = '<div class="alert alert-danger">'+
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>'+ msg +
                            '</div>';
                        $(messages).html(successHtml);
                    }
                },'json')
            }
        );

        function staffTerminate($routeName,$reload){

            if(!confirm("Do you want to Terminate this Staff ?")){
                return false;
            }
            if($reload == undefined){
                $reload = 3000;
            }

            $.post(
                $routeName,
                {
                    '_method':'POST',
                    '_token':$('meta[name="csrf-token"]').attr('content'),
                    'ajax':true
                },
                function(response){
                    //console.log(response);
                    if(isJSON(response)){
                        $data = response;
                        if($data.status == true){
                            toastr.success($data.msg, 'Success !', {"closeButton": true});
                            if($reload){
                                setTimeout(function(){location.reload();},$reload);
                            }
                        }else{
                            toastr.error($data.msg, 'Error !', {"closeButton": true});
                        }
                    }
                }

            )
        }




        function staffEdit(){

//            if(!confirm("Do you want to Terminate this Staff ?")){
//                return false;
//            }
            if($reload == undefined){
                $reload = 3000;
            }

            $.post('{{route('system.staff.edit-info')}}', {
                    '_method':'POST',
                    '_token':$('meta[name="csrf-token"]').attr('content'),
                    'ajax':true
                },
                function(response){
                    //console.log(response);
                    if(isJSON(response)){
                        $data = response;
                        if($data.status == true){
                            toastr.success($data.msg, 'Success !', {"closeButton": true});
                            if($reload){
                                setTimeout(function(){location.reload();},$reload);
                            }
                        }else{
                            toastr.error($data.msg, 'Error !', {"closeButton": true});
                        }
                    }
                }

            )
        }


    </script>
@endsection