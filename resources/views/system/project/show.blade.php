@extends('system.layouts')


@if( staffCan('system.project.edit',Auth::id()))
    <form action="#" id="addProjectCleaners-form" onsubmit="addProjectCleaners();return false;" >
        <div class="modal fade" id="addProjectCleaners-modal" tabindex="-1" role="dialog" aria-labelledby="transactions" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">{{__('Add Project Cleaners')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="project_id" value="{{$result->id}}">
                        <div class="form-group col-sm-12{!! formError($errors,'date',true) !!}">
                            <div class="controls">
                                {{ Form::label('Department',__('Department')) }}
                                {!! Form::select('department_id',[''=>__('Select Department')],'',['style'=>'width: 100%;' ,'id'=>'department','class'=>'form-control col-md-12']) !!}

                            </div>
                            {!! formError($errors,'Cleaner') !!}
                        </div>
                        <div class="form-group col-sm-12{!! formError($errors,'date',true) !!}">
                            <div class="controls">
                                {{ Form::label('Cleaner',__('Cleaner')) }}
                                {!! Form::select('cleaner_id',[''=>__('Select Cleaner')],'',['style'=>'width: 100%;' ,'id'=>'cleaners','class'=>'form-control col-md-12']) !!}

                            </div>
                            {!! formError($errors,'Cleaner') !!}
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endif

@section('content')

    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                <div id="user-profile">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="card profile-with-cover">
                                <div class="media profil-cover-details">
                                    @if($result->logo)
                                        <div class="media-left pl-2 pt-2">
                                            <a href="javascript:void(0);" class="profile-image">
                                                <img title="{{$result->{'name_'.\DataLanguage::get()} }}" src="{{asset('storage/app/'.imageResize($result->logo,70,70))}}"  class="rounded-circle img-border height-100"  />
                                            </a>
                                        </div>
                                    @endif
                                    <div class="media-body media-middle row">
                                        <div class="col-xs-6">
                                            <h3 class="card-title" style="margin-bottom: 0.5rem;">
                                                {{$result->{'name_'.\DataLanguage::get()} }}
                                                @if($result->status == 'in-active')
                                                    <b style="color: red;">(IN-ACTIVE)</b>
                                                @endif

                                                @if($result->status == 'hold')
                                                    <b style="color: yellow;">(Hold)</b>
                                                @endif
                                            </h3>
                                            <span>{{$result->{'description_'.\DataLanguage::get()} }}</span>
                                        </div>
                                        <div class="col-xs-6 text-xs-right">
                                                       </div>
                                    </div>
                                </div>
                                <nav class="navbar navbar-light navbar-profile">
                                    <button class="navbar-toggler hidden-sm-up" type="button" data-toggle="collapse" data-target="#exCollapsingNavbar2" aria-controls="exCollapsingNavbar2" aria-expanded="false" aria-label="Toggle navigation"></button>
                                    <div class="collapse navbar-toggleable-xs" id="exCollapsingNavbar2">
                                        <ul class="nav navbar-nav float-xs-right">

                                        </ul>

                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <section id="spacing" class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        {{__('Project Info')}}
                                        <span style="float: right;"><a class="btn btn-outline-primary"  href="{{route('system.project.edit',$result->id)}}"  ><i class="fa fa-pencil"></i> {{__('Edit')}}</a></span>
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
                                                    <td>{{__('Client')}} </td>
                                                    <td><code>{{$result->client->name}}</code></td>
                                                </tr>


                                                <tr>
                                                    <td>{{__('Created By')}}</td>
                                                    <td>
                                                        <a href="{{route('system.staff.show',$result->staff_id)}}" target="_blank">
                                                            {{__('#ID')}}:{{$result->staff_id}} <br >{{$result->staff->firstname .' '. $result->staff->lastname}}
                                                        </a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Created At')}}</td>
                                                    <td>
                                                        @if($result->created_at == null)
                                                            --
                                                        @else
                                                            {{$result->created_at}}
                                                        @endif
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Updated At')}}</td>
                                                    <td>
                                                        @if($result->updated_at == null)
                                                            --
                                                        @else
                                                            {{$result->updated_at}}
                                                        @endif
                                                    </td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </section>

                        </div>

                        <div class="col-md-4">

                            <section class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{__('Last Contract')}}
                                        @if(!empty($result->lastContract()->id))
                                        <span style="float: right;"><a class="btn btn-outline-primary" target="_blank" href="{{route('system.contract.edit',$result->lastContract()->id)}}" ><i class="fa fa-pencil"></i> {{__('Edit')}}</a></span>
                                            @endif
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
                                                @if(!empty($result->lastContract()->id))
                                                <tbody>
                                                <tr>
                                                    <td>{{__('ID')}}</td>
                                                    <td>{{$result->lastContract()->id}} ( <a href="{{route('system.contract.show',$result->lastContract()->id)}}" target="_blank">View</a> ) </td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('description')}}</td>
                                                    <td><code>{{$result->lastContract()->description}}</code></td>
                                                </tr>



                                                <tr>
                                                    <td>{{__('Start At')}}</td>
                                                    <td>{{explode(' ',$result->lastContract()->date_from)[0]}} ( {{$result->lastContract()->date_from}} )</td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('End At')}}</td>
                                                    <td>{{explode(' ',$result->lastContract()->date_to)[0]}}</td>
                                                </tr>




                                                <tr>
                                                    <td>{{__('Created By')}}</td>
                                                    <td>
                                                        <a href="{{url('system/staff/'.$result->lastContract()->staff_id)}}" target="_blank">
                                                            {{__('#ID')}}:{{$result->lastContract()->staff_id}} <br >{{$result->lastContract()->staff->firstname .' '. $result->lastContract()->staff->lastname}}
                                                        </a>
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td>{{__('Created At')}}</td>
                                                    <td>
                                                        @if($result->lastContract()->created_at == null)
                                                            --
                                                        @else
                                                            {{$result->lastContract()->created_at}}
                                                        @endif
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Updated At')}}</td>
                                                    <td>
                                                        @if($result->lastContract()->updated_at == null)
                                                            --
                                                        @else
                                                            {{$result->lastContract()->updated_at}}
                                                        @endif
                                                    </td>
                                                </tr>

                                                </tbody>
                                                    @endif
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </section>


                        </div>
                        <div class="col-md-3">

                            <section class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{__('Attendance Links')}}

                                    </h4>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block">
                                        <div class="table-responsive">
                                             <a class="btn btn-outline-danger" style="width: 100%;" href="{{route('system.projects.attendance',$result->id)}}" target="_blank">Add Attendance</a><br><br>
                                             <a class="btn btn-outline-primary" style="width: 100%;" href="{{route('system.attendance.index')."?project_id=".$result->id}}" target="_blank">Show Attendance</a><br><br>
                                             <a class="btn btn-outline-success" style="width: 100%; "href="{{route('system.attendance.index')."?date1=".date('y-m-d')."&date2=".date('y-m-d')}}" target="_blank">Attendance Today</a>

                                        </div>

                                    </div>
                                </div>
                            </section>


                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <section id="spacing" class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        {{__('Project Contracts')}}
                                        </h4>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block">
                                        <div class="table-responsive">
                                            <table class="table" id="contract-table">
                                                <thead>
                                                <tr>
                                                    <th>{{__('ID')}}</th>
                                                    <th>{{__('Description')}}</th>
                                                    <th>{{__('Start At')}}</th>
                                                    <th>{{__('End At')}}</th>
                                                    <th>{{__('Created By')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-md-12">
                            <section id="spacing" class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        {{__('Project Cleaners')}}
                                        <span style="float: right;"><a class="btn btn-outline-primary" target="_blank" data-toggle="modal" data-target="#addProjectCleaners-modal"   href="javascript:;" ><i class="fa fa-pencil"></i> {{__('Add Cleaners')}}</a></span>

                                    </h4>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block">
                                        <div class="table-responsive">
                                            <table class="table" id="cleaners-table">
                                                <thead>
                                                <tr>
                                                    <th>{{__('ID')}}</th>
                                                    <th>{{__('Department')}}</th>
                                                    <th>{{__('Cleaner')}}</th>
                                                    <th>{{__('Created At')}}</th>
                                                    <th>{{__('Action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <section id="spacing" class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        {{__('Orders')}}

                                    </h4>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block">
                                        <div class="table-responsive">
                                            <table class="table" id="orders-table">
                                                <thead>
                                                <tr>
                                                    <th>{{__('ID')}}</th>
                                                    <th>{{__('Total Price')}}</th>
                                                    <th>{{__('Created By')}}</th>
                                                    <th>{{__('Created At')}}</th>
                                                    <th>{{__('Action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <section id="spacing" class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        {{__('Attendance')}}

                                    </h4>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block">
                                        <div class="table-responsive">
                                            <table class="table" id="attendance-table">
                                                <thead>
                                                <tr>
                                                    <th>{{__('ID')}}</th>
                                                    <th>{{__('Date')}}</th>
                                                    <th>{{__('Created By')}}</th>
                                                    <th>{{__('Created At')}}</th>
                                                    <th>{{__('Action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
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
    </div>


        @endsection



        @section('header')

            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/extensions/pace.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/daterange/daterangepicker.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/datetime/bootstrap-datetimepicker.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/pickadate/pickadate.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/core/menu/menu-types/vertical-menu.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/core/menu/menu-types/vertical-overlay-menu.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/pages/users.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/pages/timeline.css')}}">

            <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/forms/selects/select2.min.css')}}">

        @endsection

        @section('footer')

            <script type="text/javascript" src="{{asset('assets/system/treegrid/jquery.treegrid.js')}}"></script>
            <script type="text/javascript" src="{{asset('assets/system/treegrid/jquery.treegrid.bootstrap3.js')}}"></script>
            <script src="{{asset('assets/system/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/system/vendors/js/pickers/dateTime/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/system/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/system/vendors/js/pickers/daterange/daterangepicker.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/system/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>


            <script type="text/javascript">
                ajaxSelect2('#cleaners','staff','',"{{route('system.ajax.get')}}");
                ajaxSelect2('#department','department','',"{{route('system.ajax.get')}}");

        function addProjectCleaners(){
             $.post('{{route('system.projects.add-project-cleaners')}}',$('#addProjectCleaners-form').serialize(),function (out) {
             if(out.status)
             {
            $('#addProjectCleaners-form')[0].reset();
            ajaxSelect2('#cleaners','staff','',"{{route('system.ajax.get')}}");
            ajaxSelect2('#department','department','',"{{route('system.ajax.get')}}");
            toastr.success(out.msg, 'Success', {"closeButton": true});
           setTimeout(function(){
               location.reload();
           },3000)

        }else{
            toastr.error(out.msg, 'Error !', {"closeButton": true});

        }
    },'json');
}
                $(function(){

                    $('#contract-table').DataTable({
                        "iDisplayLength": 10,
                        processing: true,
                        serverSide: true,
                        "order": [[ 0, "desc" ]],
                        "ajax": {
                            "url": "{{url()->full()}}",
                            "type": "GET",
                            "data": function(data){
                                data.isContract= "true";
                            }
                        }
                    });

                    $('#cleaners-table').DataTable({
                        paging: false,
                        "iDisplayLength": 10,
                        processing: true,
                        serverSide: true,
                        "order": [[ 0, "desc" ]],
                        "ajax": {
                            "url": "{{url()->full()}}",
                            "type": "GET",
                            "data": function(data){
                                data.isCleaners= "true";
                            }
                        }
                    });

                    $('#orders-table').DataTable({
                        "iDisplayLength": 10,
                        processing: true,
                        serverSide: true,
                        "order": [[ 0, "desc" ]],
                        "ajax": {
                            "url": "{{url()->full()}}",
                            "type": "GET",
                            "data": function(data){
                                data.isOrders= "true";
                            }
                        }
                    });
                    $('#attendance-table').DataTable({
                        "iDisplayLength": 10,
                        processing: true,
                        serverSide: true,
                        "order": [[ 0, "desc" ]],
                        "ajax": {
                            "url": "{{url()->full()}}",
                            "type": "GET",
                            "data": function(data){
                                data.isAttendance= "true";
                            }
                        }
                    });



                    $('.datepicker').datetimepicker({
                        viewMode: 'months',
                        format: 'YYYY-MM-DD'
                    });
                });

            </script>
@endsection
