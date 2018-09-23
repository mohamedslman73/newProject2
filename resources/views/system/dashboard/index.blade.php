@extends('system.layouts')
@section('content')

    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body"><!-- Analytics spakline & chartjs  -->

                <div class="row">
                    <div class="col-xl-9 col-lg-12 col-xs-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-block">
                                    <div class="media">
                                        <div class="media-body text-xs-center">
                                            <h3 class="danger">{{$itemCategoryCount}}</h3>
                                            <span>{{__('Items Categories')}}</span>
                                        </div>
                                        <div class="media-body text-xs-center">
                                            <h3 class="danger">{{$itemCount}}</h3>
                                            <span>{{__('Item')}}</span>
                                        </div>
                                        <div class="media-right media-middle">
                                            <i class="icon-social-dropbox danger font-large-2 float-xs-right"></i>
                                        </div>
                                        <progress class="progress progress-sm progress-danger mt-1 mb-0" value="100" max="100"></progress>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-xs-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-block">
                                    <div class="media">
                                        <div class="media-body text-xs-left">
                                            <h3 class="primary">{{$clientCount}}</h3>
                                            <span>{{__('Clients')}}</span>
                                        </div>
                                        <div class="media-right media-middle">
                                            <i class="icon-user-follow primary font-large-2 float-xs-right"></i>
                                        </div>
                                    </div>
                                    <progress class="progress progress-sm progress-primary mt-1 mb-0" value="100" max="100"></progress>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>


                <div class="row">
                    <div class="col-lg-3 col-md-12 col-xs-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-block">
                                    <div class="media">
                                        <div class="media-body text-xs-left">
                                            <h3 class="primary">{{$projectCount}}</h3>
                                            <span>{{__('Total Projects')}}</span>
                                        </div>
                                        <div class="media-right media-middle">
                                            <i class="icon-globe warning font-large-2 float-xs-right"></i>
                                        </div>
                                        <progress class="progress progress-sm progress-primary mt-1 mb-0" value="100" max="100"></progress>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-xs-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-block">
                                    <div class="media">

                                        <div class="media-body text-xs-left">
                                            <h3 class="warning">{{$projectThisMonth}}</h3>
                                            <span>{{__('Projects This Month')}}</span>
                                        </div>
                                        <div class="media-right media-middle">
                                            <i class="icon-globe warning font-large-2 float-xs-right"></i>
                                        </div>
                                        <progress class="progress progress-sm progress-warning mt-1 mb-0" value="100" max="100"></progress>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12 col-xs-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-block">
                                    <div class="media">
                                        <div class="media-body text-xs-center">
                                            <h3 class="danger">{{$supplierCategoryCount}}</h3>
                                            <span>{{__('Supplier Categories')}}</span>
                                        </div>
                                        <div class="media-body text-xs-center">
                                            <h3 class="danger">{{$supplierCount}}</h3>
                                            <span>{{__('Suppliers')}}</span>
                                        </div>
                                        <div class="media-right media-middle">
                                            <i class="icon-social-dropbox danger font-large-2 float-xs-right"></i>
                                        </div>
                                        <progress class="progress progress-sm progress-danger mt-1 mb-0" value="100" max="100"></progress>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">

                    <div class="col-lg-3 col-md-12 col-xs-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-block">
                                    <div class="media">
                                        <div class="media-body text-xs-left">
                                            <h3 class="primary">{{$supplierOrderCount}}</h3>
                                            <span>{{__('Total Suppliers')}}</span>
                                        </div>
                                        <div class="media-right media-middle">
                                            <i class="icon-globe warning font-large-2 float-xs-right"></i>
                                        </div>
                                        <progress class="progress progress-sm progress-primary mt-1 mb-0" value="100" max="100"></progress>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-12 col-xs-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-block">
                                    <div class="media">

                                        <div class="media-body text-xs-left">
                                            <h3 class="warning">{{$supplierOrderThisMonth}}</h3>
                                            <span>{{__('Supplier Orders This Month')}}</span>
                                        </div>
                                        <div class="media-right media-middle">
                                            <i class="icon-globe warning font-large-2 float-xs-right"></i>
                                        </div>
                                        <progress class="progress progress-sm progress-warning mt-1 mb-0" value="100" max="100"></progress>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12 col-xs-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-block">
                                    <div class="media">
                                        <div class="media-body text-xs-left">
                                            <h3 class="primary">{{$staffCount}}</h3>
                                            <span>{{__('Total Staff')}}</span>
                                        </div>
                                        <div class="media-right media-middle">
                                            <i class="icon-globe warning font-large-2 float-xs-right"></i>
                                        </div>
                                        <progress class="progress progress-sm progress-primary mt-1 mb-0" value="100" max="100"></progress>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!--/ Analytics spakline & chartjs  -->


                <!--stats-->
                <div class="row">
                    @if(staffCan('view-dashboard-analytics'))
                    <div class="col-xl-12 col-lg-12 col-xs-12">
                        <div class="card">
                            <div class="card-header no-border-bottom">
                                <h4 class="card-title">{{__('System Load AVG')}}</h4>
                                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body collapse in">
                                <div class="card-block">
                                    <div id="graph" class="height-400"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                        {{--<div class="col-xl-6 col-lg-12 col-xs-12">--}}
                            {{--<div class="card">--}}
                                {{--<div class="card-body">--}}
                                    {{--<div class="card-block">--}}
                                        {{--<div class="media">--}}
                                            {{--<div class="media-body text-xs-center">--}}
                                                {{--<h3 class="danger">{{Auth::id()}}</h3>--}}
                                                {{--<span>{{__('Staff ID')}}</span>--}}
                                            {{--</div>--}}
                                            {{--<div class="media-body text-xs-center">--}}
                                                {{--<h3 class="danger">{{Auth::user()->paymentWallet->id}}</h3>--}}
                                                {{--<span>{{__('Payment Wallet ID')}}</span>--}}
                                            {{--</div>--}}
                                            {{--<div class="media-body text-xs-center">--}}
                                                {{--<h3 class="danger">{{amount(Auth::user()->paymentWallet->balance,true)}}</h3>--}}
                                                {{--<span>{{__('Payment Wallet Balance')}}</span>--}}
                                            {{--</div>--}}
                                            {{--<div class="media-right media-middle">--}}
                                                {{--<i class="icon-social-dropbox danger font-large-2 float-xs-right"></i>--}}
                                            {{--</div>--}}
                                            {{--<progress class="progress progress-sm progress-danger mt-1 mb-0" value="100" max="100"></progress>--}}

                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                    <div class="col-xl-6 col-lg-12 col-xs-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-block">
                                    <div height="120" id="yahooWeather">
                                        <p style="font-size: 1.51rem;">{{__('Weather')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->

@endsection




@section('header')

    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.9/css/weather-icons.min.css">

@endsection

@section('footer')

    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{asset('assets/system/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/charts/chart.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/js/yahoo-weather-jquery-plugin.js')}}" type="text/javascript"></script>



    <script src="//cdn.plot.ly/plotly-latest.min.js" type="text/javascript"></script>

    <script src="{{asset('assets/system/js/scripts/charts/flot/line/realtime.js')}}" type="text/javascript"></script>



    <script type="text/javascript">


        $(document).ready(function(){
            $('#yahooWeather').yahooWeather();
        });
        $(window).on("load", function() {
                new Chart(document.getElementById("payment-chart"), {
                    type: 'bar',
                    data: {
                        labels: [
                            @foreach(range(1,12) as $month)
                                "{{$month}} / {{date('Y')}}",
                            @endforeach
                        ],
                        datasets: [
                            {
                                label: "{{__('Paid Invoice')}}",
                                backgroundColor: "#3e95cd",
                                data: [
                                    @foreach(range(1,12) as $month)
                                        "{{$paymentInvoicePaidCount[$month] ?? 0}}",
                                    @endforeach
                                ]
                            }, {
                                label: "{{__('Success Transactions')}}",
                                backgroundColor: "#8e5ea2",
                                data: [
                                    @foreach(range(1,12) as $month)
                                        "{{$paymentTransactionCount[$month] ?? 0}}",
                                    @endforeach
                                ]
                            }
                        ]
                    },
                    options: {
                        title: {
                            display: true,
                            text: '{{__('Payment Overview')}}'
                        }
                    }
                });

            }

        );

    </script>


@endsection
