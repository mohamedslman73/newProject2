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
            <div class="content-body"><!-- Spacing -->
                <div class="row">
                    <div class="col-md-4">
                        <section id="spacing" class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    {{__('Target Data')}}
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
                                                <td>{{__('Staff')}}</td>
                                                <td>
                                                    <a href="{{route('system.staff.show',$result->staff_id)}}" target="_blank">{{$result->staff->firstname}} {{$result->staff->lastname}}</a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Month')}}</td>
                                                <td><code>{{$result->month}}/{{$result->year}}</code></td>
                                            </tr>


                                            <tr>
                                                <td>{{__('Target')}}</td>
                                                <td>{{amount($result->amount,true)}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Description')}}</td>
                                                <td><code>{{$result->description}}</code></td>
                                            </tr>


                                            <tr>
                                                <td>{{__('Created At')}}</td>
                                                <td>
                                                    @if($result->created_at == null)
                                                        --
                                                    @else
                                                        {{$result->created_at->diffForHumans()}}
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Updated At')}}</td>
                                                <td>
                                                    @if($result->updated_at == null)
                                                        --
                                                    @else
                                                        {{$result->updated_at->diffForHumans()}}
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
                    <div class="col-md-8">
                        <section id="spacing" class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    {{__('Target Data')}}
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

                                            @if($result->is_supervisor == 'no')

                                                @php
                                                $paymentInvoices = $result->paymentInvoices();
                                                @endphp

                                                <tr>
                                                    <td>{{__('Total Sales')}}</td>
                                                    <td>{{amount($paymentInvoices->total,true)}} / {{amount($result->amount,true)}} ({{round( ($paymentInvoices->total*100)/$result->amount )}}%)</td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Num. Invoices')}}</td>
                                                    <td>
                                                        <a href="javascript:void(0);" onclick="urlIframe('{{route('payment.invoice.index',['ids'=>$paymentInvoices->payment_invoices_id])}}','{{__('Invoices')}}')">
                                                            {{number_format($paymentInvoices->count)}}
                                                        </a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Commission')}}</td>
                                                    <td>
                                                        @php
                                                            $sales_commission = @unserialize($result->sales_commission);
                                                            $doneShowCommission = false;
                                                        @endphp

                                                        @if(!is_array($sales_commission)|| count($sales_commission['payment_sales_target']) != count($sales_commission['payment_sales_commission_rate']))
                                                            --
                                                        @else
                                                            @foreach(collect($sales_commission['payment_sales_target'])->reverse() as $key => $value)
                                                                @if(round( ($paymentInvoices->total*100)/$result->amount ) >= $value)
                                                                    {{amount( ($paymentInvoices->total*$sales_commission['payment_sales_commission_rate'][$key])/100 ,true)}} ({{$sales_commission['payment_sales_commission_rate'][$key]}}%)
                                                                    @php $doneShowCommission = true; break; @endphp
                                                                @endif
                                                            @endforeach

                                                            @if(!$doneShowCommission)
                                                                --
                                                            @endif

                                                        @endif


                                                    </td>
                                                </tr>

                                            @else
                                                @php
                                                    $paymentInvoicesTotal = [];
                                                    $paymentInvoicesCount = [];
                                                    $paymentInvoicesIDs   = [];
                                                    foreach($result->managedStaffTarget() as $key => $value){
                                                        $invoiceData = $value->paymentInvoices();
                                                        $paymentInvoicesTotal[] = $invoiceData->total;
                                                        $paymentInvoicesCount[] = $invoiceData->count;
                                                        $paymentInvoicesIDs  [] = $invoiceData->payment_invoices_id;
                                                    }

                                                    $paymentInvoicesTotal = array_sum($paymentInvoicesTotal);
                                                    $paymentInvoicesCount = array_sum($paymentInvoicesCount);

                                                @endphp

                                                <tr>
                                                    <td>{{__('Total Sales')}}</td>
                                                    <td>{{amount($paymentInvoicesTotal,true)}} / {{amount($result->amount,true)}} ({{round( ($paymentInvoicesTotal*100)/$result->amount )}}%)</td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Num. Invoices')}}</td>
                                                    <td>
                                                        <a href="javascript:void(0);" onclick="urlIframe('{{route('payment.invoice.index',['ids'=>implode(',',$paymentInvoicesIDs)])}}','{{__('Invoices')}}')">
                                                            {{number_format($paymentInvoicesCount)}}
                                                        </a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Commission')}}</td>
                                                    <td>
                                                        @php
                                                        $sales_commission   = @unserialize($result->sales_commission);
                                                        $doneShowCommission = false;
                                                        @endphp

                                                        @if(!is_array($sales_commission)|| count($sales_commission['payment_sales_target']) != count($sales_commission['payment_sales_commission_rate']))
                                                            --
                                                        @else
                                                            @foreach(collect($sales_commission['payment_sales_target'])->reverse() as $key => $value)
                                                                @if(round( ($paymentInvoicesTotal*100)/$result->amount ) >= $value)
                                                                    {{amount( ($paymentInvoicesTotal*$sales_commission['payment_sales_commission_rate'][$key])/100 ,true)}} ({{$sales_commission['payment_sales_commission_rate'][$key]}}%)
                                                                    @php $doneShowCommission = true; break; @endphp
                                                                @endif
                                                            @endforeach

                                                            @if(!$doneShowCommission)
                                                                --
                                                            @endif

                                                        @endif


                                                    </td>
                                                </tr>
                                            @endif

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                    </div>

                    @if($result->is_supervisor == 'yes')
                    <div class="col-md-8">
                        <section id="spacing" class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    {{__('Sales Team')}}
                                </h4>
                            </div>
                            <div class="card-body collapse in">
                                <div class="card-block">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>{{__('ID')}}</th>
                                                    <th>{{__('Staff')}}</th>
                                                    <th>{{__('Target')}}</th>
                                                    <th>{{__('Commission')}}</th>
                                                    <th>{{__('Num. Invoices')}}</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                @foreach($result->managedStaffTarget() as $key => $value)
                                                    @php
                                                        $invoiceData = $value->paymentInvoices();
                                                    @endphp
                                                    <tr>
                                                        <td><a href="{{route('system.staff-target.show',$value->id)}}">{{$value->id}}</a></td>
                                                        <td><a href="{{route('system.staff.show',$value->staff->id)}}">{{$value->staff->firstname}} {{$value->staff->lastname}}</a></td>
                                                        <td>
                                                            {{amount($invoiceData->total,true)}} / {{amount($value->amount,true)}} ({{round( ($invoiceData->total*100)/$value->amount )}}%)
                                                        </td>


                                                        <td>

                                                            @php
                                                                $sales_commission   = @unserialize($value->sales_commission);
                                                                $doneShowCommission = false;
                                                            @endphp

                                                            @if(!is_array($sales_commission)|| count($sales_commission['payment_sales_target']) != count($sales_commission['payment_sales_commission_rate']))
                                                                --
                                                            @else
                                                                @foreach(collect($sales_commission['payment_sales_target'])->reverse() as $key1 => $value1)
                                                                    @if(round( ($invoiceData->total*100)/$value->amount ) >= $value1)
                                                                        {{amount( ($invoiceData->total*$sales_commission['payment_sales_commission_rate'][$key1])/100 ,true)}} ({{$sales_commission['payment_sales_commission_rate'][$key1]}}%)
                                                                        @php $doneShowCommission = true; break; @endphp
                                                                    @endif
                                                                @endforeach

                                                                @if(!$doneShowCommission)
                                                                    --
                                                                @endif

                                                            @endif

                                                        </td>

                                                        <td>
                                                            <a href="javascript:void(0);" onclick="urlIframe('{{route('payment.invoice.index',['ids'=>$invoiceData->payment_invoices_id])}}','{{__('Invoices')}}')">
                                                                {{number_format($invoiceData->count)}}
                                                            </a>
                                                        </td>
                                                    </tr>

                                                @endforeach


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('header')
@endsection;

@section('footer')
    <script type="text/javascript">
        $('#merchant-table').DataTable({
            "iDisplayLength": 25,
            processing: true,
            serverSide: true,
            "order": [[ 0, "desc" ]],
            "ajax": {
                "url": "{{url()->full()}}",
                "type": "GET",
                "data": function(data){
                    data.isMerchant = "true";
                }
            }
        });

        $('#contract-table').DataTable({
            "iDisplayLength": 25,
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
    </script>
@endsection
