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
                                    <a class="heading-elements-toggle"><i
                                                class="fa fa-ellipsis-v font-medium-3"></i></a>

                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <td>{{__('ID')}}</td>
                                                    <td>{{__('Info')}}</td>
                                                    <td>{{__('Price')}}</td>
                                                    <td>{{__('Credit')}}</td>
                                                    <td>{{__('Date')}}</td>
                                                    <td>{{__('Action')}}</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php

                                                    $credit = $supplier['init_credit'];
                                                @endphp
                                                <tr style="background-color: #00BCD4">
                                                    <td colspan="3">{{__('INITIAL Credit')}}</td>
                                                    <td colspan="3">{{$credit}}</td>
                                                </tr>
                                                @foreach($tableColumns as $value)

                                                    @if($value['type'] == 'supplier_order')
                                                        <tr style="background-color:yellow">
                                                            <td>{{$value['id']}}</td>
                                                            <td>Supplier Order</td>
                                                            <td>{{amount($value['total_price'],true)}}</td>
                                                            <td>{{amount($credit += $value['total_price'],true)}}</td>
                                                            <td>{{$value['date']}}</td>
                                                            <td><a target="_blank"
                                                                   href="{{route('system.order.show',$value['id'])}}">View</a>
                                                            </td>
                                                        </tr>
                                                    @elseif($value['type'] == 'supplier_expence')
                                                        <tr style="background-color:greenyellow">
                                                            <td>{{$value['id']}}</td>
                                                            <td>Supplier Expense</td>
                                                            <td>{{$value['amount']}}</td>
                                                            <td>{{ amount($credit -= $value['amount'],true) }}</td>
                                                            <td>{{$value['date']}}</td>
                                                            <td><a target="_blank"
                                                                   href="{{route('system.expenses.show',$value['id'])}}">View</a>
                                                            </td>
                                                        </tr>
                                                    @elseif($value['type'] == 'supplier_order_back')
                                                        <tr style="background-color:greenyellow">
                                                            <td>{{$value['id']}}</td>
                                                            <td>Order Back</td>
                                                            <td>{{amount($value['total_price'],true)}}</td>
                                                            <td>{{amount($credit -= $value['total_price'],true)}}</td>
                                                            <td>{{$value['date']}}</td>
                                                            <td><a target="_blank"
                                                                   href="{{route('system.supplier-order-back.show',$value['id'])}}">View</a>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        {{--{{ $tableColumns->links() }}--}}
                                        {{--{{$stopWorkingMerchant->appends()->links() }}--}}
                                        {{--{{$stopWorkingMerchant->render()}}--}}
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
    <link rel="stylesheet" type="text/css"
          href="{{asset('assets/system/vendors/css/pickers/daterange/daterangepicker.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('assets/system/vendors/css/pickers/datetime/bootstrap-datetimepicker.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('assets/system/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/forms/selects/select2.min.css')}}">
@endsection;

@section('footer')
    <script src="{{asset('assets/system/vendors/js/forms/select/select2.full.min.js')}}"
            type="text/javascript"></script>
    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{asset('assets/system/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/dateTime/bootstrap-datetimepicker.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.date.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.time.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/daterange/daterangepicker.js')}}"
            type="text/javascript"></script>
    <!-- END PAGE VENDOR JS-->

    <script src="{{asset('assets/system/js/scripts/pickers/dateTime/picker-date-time.js')}}"
            type="text/javascript"></script>

    <script type="text/javascript">
        ajaxSelect2('#staffSelect2', 'staff', '', "{{route('system.ajax.get')}}");
        {{--$dataTableVar = $('#egpay-datatable').DataTable({--}}
        {{--"iDisplayLength": 25,--}}
        {{--processing: true,--}}
        {{--serverSide: true,--}}
        {{--"order": [[ 0, "desc" ]],--}}
        {{--"ajax": {--}}
        {{--"url": "{{url()->full()}}",--}}
        {{--"type": "GET",--}}
        {{--"data": function(data){--}}
        {{--data.isDataTable = "true";--}}
        {{--}--}}
        {{--}--}}
        //            ,
        //            "fnPreDrawCallback": function(oSettings) {
        //                for (var i = 0, iLen = oSettings.aoData.length; i < iLen; i++) {
        //                    if(oSettings.aoData[i]._aData[6] != ''){
        //                        oSettings.aoData[i].nTr.className = oSettings.aoData[i]._aData[6];
        //                    }
        //                }
        //            }

        })
        ;
        function filterFunction($this, downloadExcel= false) {
            if ($this == false) {
                $url = '{{url()->full()}}?isDataTable=true&downloadExcel=' + downloadExcel;
            } else {
                $url = '{{url()->full()}}?isDataTable=true&' + $this.serialize() + '&downloadExcel=' + downloadExcel;
            }
            if (downloadExcel == true)
                window.location = $url;
            else {
                $dataTableVar.ajax.url($url).load();
                $('#filter-modal').modal('hide');
            }
        }

        $(function () {
            $('.datepicker').datetimepicker({
                viewMode: 'months',
                format: 'YYYY-MM-DD'
            });
        });

    </script>
@endsection
