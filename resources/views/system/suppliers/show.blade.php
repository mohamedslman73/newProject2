@extends('system.layouts')
@if(staffCan('system.staff.add-managed-staff',Auth::id()))
    <div class="modal fade text-xs-left" id="addManagedStaff-modal"  role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <label class="modal-title text-text-bold-600" id="myModalLabel33">{{__('Add Managed Staff')}}</label>
                </div>

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
                                        {{__('Supplier Info')}}
                                        <span style="float: right;"><a class="btn btn-outline-primary"  href="javascript:void(0);" onclick="urlIframe('{{route('system.supplier.edit',$result->id)}}')"><i class="fa fa-pencil"></i> {{__('Edit')}}</a></span>
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
                                                        {{$result->name}}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Company Name')}}</td>
                                                    <td>
                                                        {{$result->company_name}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('E-Mail')}}</td>
                                                    <td>
                                                        <a href="mailto:{{$result->email}}">{{$result->email}}</a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Mobile')}}</td>
                                                    <td>
                                                        <a href="tel:{{$result->mobile1}}">{{$result->mobile1}}</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('Phone1')}}</td>
                                                    <td>
                                                        <a href="tel:{{$result->phone1}}">{{$result->phone1}}</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('status')}}</td>
                                                    <td>
                                                        @if($result->status == 'in-active')
                                                            <b style="color: red;">(IN-ACTIVE)</b>
                                                        @else
                                                            <b style="color: green;">(ACTIVE)</b>
                                                        @endif
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td>{{__('Description')}}</td>
                                                    <td>
                                                        <code>{{$result->description}}</code>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('Created At')}}</td>
                                                    <td>
                                                        <code>{{$result->created_at->diffForHumans()}}</code>
                                                    </td>
                                                </tr>
                                                {{--<tr><td colspan="2" >Analysis</td></tr>--}}
                                                {{--<tr>--}}
                                                    {{--<td>Num Orders</td>--}}
                                                    {{--<td><a target="_blank" href="{{route('system.order.index',['supplier_id'=>$result->id])}}">{{$order_count}} Orders</a></td>--}}
                                                {{--</tr>--}}

                                                {{--<tr>--}}
                                                    {{--<td>Initial Credit</td>--}}
                                                    {{--<td>{{amount($result->init_credit,true)}}</td>--}}
                                                {{--</tr>--}}
                                                {{--<tr>--}}
                                                    {{--<td>Total Orders Amount</td>--}}
                                                    {{--<td>{{$total_orders}}</td>--}}
                                                {{--</tr>--}}
                                                {{--<tr>--}}
                                                    {{--<td>Total Paid</td>--}}
                                                    {{--<td>{{amount($total_expence,true)}}</td>--}}
                                                {{--</tr>--}}
                                                {{--<tr>--}}
                                                    {{--<td>Rest</td>--}}
                                                    {{--<td>@php echo  amount(( $result->ini_credit + $total_orders ) - $total_expence,true); @endphp</td>--}}
                                                {{--</tr>--}}
                                                </tbody>
                                            </table>


                                        </div>
                                    </div>
                                </div>


                                <!-- report -->
                                <div class="card-header">
                                <h4 class="card-title">
                                    {{__('credit report')}}
                                   </h4>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block card-dashboard">
                                        <table style="text-align: center;" id="egpay-datatable-report" class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                @foreach($tableColumns as  $value)
                                                    <td>{{$value}}</td>
                                                @endforeach
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>
                                                @foreach($tableColumns as $key => $value)
                                                    <td>{{$value}}</td>
                                                @endforeach
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>


                                <!-- Orders -->
                                <div class="card-header">
                                    <h4 class="card-title">
                                        {{__('Orders')}}
                                       </h4>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block card-dashboard">
                                        <table style="text-align: center;" id="egpay-datatable-order" class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                @foreach($orderTableColumns as  $value)
                                                    <td>{{$value}}</td>
                                                @endforeach
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>
                                                @foreach($orderTableColumns as $key => $value)
                                                    <td>{{$value}}</td>
                                                @endforeach
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>





                                <!-- Orders Back -->
                                <div class="card-header">
                                    <h4 class="card-title">
                                        {{__('Orders Back')}}
                                    </h4>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block card-dashboard">
                                        <table style="text-align: center;" id="egpay-datatable-order-back" class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                @foreach($orderBackTableColumns as  $value)
                                                    <td>{{$value}}</td>
                                                @endforeach
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>
                                                @foreach($orderBackTableColumns as $key => $value)
                                                    <td>{{$value}}</td>
                                                @endforeach
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>


                                <!-- deposits -->
                                <div class="card-header">
                                    <h4 class="card-title">
                                        {{__('deposits')}}
                                    </h4>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block card-dashboard">
                                        <table style="text-align: center;" id="egpay-datatable-deposits" class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                @foreach($depositsTableColumns as  $value)
                                                    <td>{{$value}}</td>
                                                @endforeach
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>
                                                @foreach($depositsTableColumns as $key => $value)
                                                    <td>{{$value}}</td>
                                                @endforeach
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>


                            </section>

                        </div>

                    </div>






                </div>

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

        $dataTableVar = $('#egpay-datatable-report').DataTable({
            "iDisplayLength": 25,
            processing: true,
            serverSide: true,
            "order": [[ 0, "desc" ]],
            "ajax": {
                "url": "{{route('system.supplier-report')}}?supplier_id={{$result->id}}",
                "type": "GET",
                "data": function(data){
                    data.isDataTable = "true";
                }
            }
        });

        $dataTableVar = $('#egpay-datatable-order').DataTable({
            "iDisplayLength": 25,
            processing: true,
            serverSide: true,
            "order": [[ 0, "desc" ]],
            "ajax": {
                "url": "{{route('system.order.index')}}?supplier_id={{$result->id}}",
                "type": "GET",
                "data": function(data){
                    data.isDataTable = "true";
                }
            }
        });

        $dataTableVar = $('#egpay-datatable-order-back').DataTable({
            "iDisplayLength": 25,
            processing: true,
            serverSide: true,
            "order": [[ 0, "desc" ]],
            "ajax": {
                "url": "{{route('system.supplier-order-back.index')}}?supplier_id={{$result->id}}",
                "type": "GET",
                "data": function(data){
                    data.isDataTable = "true";
                }
            }
        });


        $dataTableVar = $('#egpay-datatable-deposits').DataTable({
            "iDisplayLength": 25,
            processing: true,
            serverSide: true,
            "order": [[ 0, "desc" ]],
            "ajax": {
                "url": "{{route('system.expenses.index')}}?supplier_id={{$result->id}}",
                "type": "GET",
                "data": function(data){
                    data.isDataTable = "true";
                }
            }
        });


    </script>
@endsection