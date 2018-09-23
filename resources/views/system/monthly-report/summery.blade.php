@extends('system.layouts')

<div class="modal fade text-xs-left" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <label class="modal-title text-text-bold-600" id="myModalLabel33">{{__('Filter')}}</label>
            </div>
            {{--{!! Form::open(['id'=>'filterForm','action'=>route('system.monthly-report.summery'),'method'=>'get']) !!}--}}
            <form action="{{route('system.monthly-report.summery')}}" method="get" >
            <div class="modal-body">

                <div class="card-body">
                    <div class="card-block">
                        <div class="row">

                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    {{ Form::label('Month',__('Month')) }}
                                    {!! Form::text('date',null,['class'=>'form-control datepicker','id'=>'created_at1']) !!}
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

@section('content')

    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">

                <div class="content-header-left col-md-4 col-xs-12">
                    <h4>
                        {{$pageTitle}}
                        <a data-toggle="modal" data-target="#filter-modal" class="btn btn-outline-primary"><i class="ft-search"></i> {{__('Filter')}}</a>

                    </h4>
                </div>
                <div class="content-header-right col-md-8 col-xs-12 mb-2">
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
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{$pageTitle}}</h4>
                                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>

                                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="card-body collapse in">
                                    <div class="card-block card-dashboard">
                                        <table style="text-align: center;" id="egpay-datatable" class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Report</th>
                                                <th>Value</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr style="word-wrap: break-word;">
                                                <th>{{__('Supplier Orders')}}</th>
                                                <td>{{$data['supplierOrders']}}</td>
                                            </tr>
                                            <tr style="word-wrap: break-word;">
                                                <th>{{__('Supplier Orders Back')}}</th>
                                                <td>{{$data['supplierOrdersBack']}}</td>
                                            </tr>
                                            <tr style="word-wrap: break-word;">
                                                <th>{{__('Client Orders')}}</th>
                                                <td>{{$data['clientOrders']}}</td>
                                            </tr>
                                            <tr style="word-wrap: break-word;">
                                                <th>{{__('Client Orders Back')}}</th>
                                                <td>{{$data['clientOrdersBack']}}</td>
                                            </tr>
                                            <tr style="word-wrap: break-word;">
                                                <th>{{__('Revenue')}}</th>
                                                <td>{{$data['revenue']}}</td>
                                            </tr>
                                            <tr style="word-wrap: break-word;">
                                                <th>{{__('Expense')}}</th>
                                                <td>{{$data['expense']}}</td>
                                            </tr>
                                            <tr style="word-wrap: break-word;">
                                                <th>{{__('Expense Without Supplier Deposit')}}</th>
                                                <td>{{$data['expenseWithOutSupplierDeposit']}}</td>
                                            </tr>
                                            <tr style="word-wrap: break-word;">
                                                <th>{{__('Expense Of Supplier Deposits')}}</th>
                                                <td>{{$data['expenseOfSupplierDeposit']}}</td>
                                            </tr>
                                            <tr style="word-wrap: break-word;">
                                                <th>{{__('Revenue Without Client Deposits')}}</th>
                                                <td>{{$data['revenueWithOutClientDeposit']}}</td>
                                            </tr>
                                            <tr style="word-wrap: break-word;">
                                                <th>{{__('Revenue Of Client Deposit')}}</th>
                                                <td>{{$data['revenueOfClientDeposit']}}</td>
                                            </tr>
                                            </tbody>


                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

@endsection;

@section('footer')

    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{asset('assets/system/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/dateTime/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/daterange/daterangepicker.js')}}" type="text/javascript"></script>
    <!-- END PAGE VENDOR JS-->


<script>
    $(function(){
        $('.datepicker').datetimepicker({
            viewMode: 'months',
            format: 'YYYY-MM'
        });
    });

</script>

@endsection
