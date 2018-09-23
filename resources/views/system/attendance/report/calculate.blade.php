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
                                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>

                                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-xs-12" style="padding-top: 20px;">
                                    <div class="card-header">
                                        <div class="card-body">
                                            <div class="card-block card-dashboard" id="buttonDev">
                                                <a id="" href="javascript:;" onclick="saveReport()" class="btn btn-success pull-right" >Save</a>

                                            </div>
                                            <div class="card-block card-dashboard" id="msgDev">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block card-dashboard">
                                        <table style="text-align: center;" id="egpay-datatable" class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Info</th>
                                                <th>Days</th>
                                                <th>Money</th>
                                            </tr>
                                            </thead>
                                            <tr style="word-wrap: break-word;">
                                                <th>{{__('Presence')}}</th>
                                                <td>{{$result['totalDaysPresence']}}</td>
                                                <td>{{$result['totalMoneyPresence']}}</td>
                                            </tr>
                                            <tr>
                                                <th>{{__('Weekly Vacation')}}</th>
                                                <td>{{$result['totalDaysWeeklyVacation']}}</td>
                                                <td>{{$result['totalMoneyWeeklyVacation']}}</td>
                                            </tr>

                                            <tr>
                                                <th>{{__('Absence')}}</th>
                                                <td>{{$result['totalDaysAbsence']}}</td>
                                                <td>{{$result['totalMoneyAbsence']}}</td>
                                            </tr>

                                            <tr>
                                                <th>{{__('Paid Vacations')}}</th>
                                                <td>{{$result['totalDaysPaidVacations']}}</td>
                                                <td>{{$result['totalMoneyPaidVacations']}}</td>
                                            </tr>

                                            <tr>
                                                <th>{{__('UnPaid Vacations')}}</th>
                                                <td>{{$result['totalDaysUnPaidVacations']}}</td>
                                                <td>{{$result['totalMoneyUnPaidVacations']}}</td>
                                            </tr>

                                                <tr>
                                                    <th>{{__('Overtime')}}</th>
                                                    <td>{{$result['totalHoursOvertime']}} Hours</td>
                                                    <td>{{$result['totalMoneyOvertime']}}</td>
                                                </tr>

                                            <tr>
                                                <th>{{__('Deduction')}}</th>
                                                <td>{{$result['totalDaysDeduction']}}</td>
                                                <td>{{$result['totalMoneyDeduction']}}</td>
                                            </tr>

                                               <tr>
                                                <th>{{__('Total Money')}}</th>
                                                   <td colspan="2">{{$result['totalMoneyStaff']}}</td>
                                               </tr>

                                            <input type="hidden" id="data" value="{{$result['serialized']}}">
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


<script>

    function saveReport(){
        $.post('{{route('system.attendance.monthly-report-save')}}',{'data':$('#data').val()},function(out){
            if(out.status == true){
                $('#msgDev').html('');
                $('#buttonDev').html('<div class="alert alert-success">'+out.msg+'</div>');
            }else{
            $('#msgDev').html('<div class="alert alert-danger">'+out.msg+'</div>');
            }

        });
        }


</script>


