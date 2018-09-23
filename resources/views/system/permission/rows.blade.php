@extends('system.layouts')

@section('content')

    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-xs-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-xs-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">DataTables</a>
                                </li>
                                <li class="breadcrumb-item active">Sources Datatable
                                </li>
                            </ol>
                        </div>
                    </div>
                    <h3 class="content-header-title mb-0">Sources Datatable</h3>
                </div>
                <div class="content-header-right col-md-6 col-xs-12">
                    <div role="group" aria-label="Button group with nested dropdown" class="btn-group float-md-right">
                        <div role="group" class="btn-group">
                            <button id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-outline-primary dropdown-toggle dropdown-menu-right"><i class="ft-cog icon-left"></i> Settings</button>
                            <div aria-labelledby="btnGroupDrop1" class="dropdown-menu"><a href="card-bootstrap.html" class="dropdown-item">Bootstrap Cards</a><a href="component-buttons-extended.html" class="dropdown-item">Buttons Extended</a></div>
                        </div><a href="calendars-clndr.html" class="btn btn-outline-primary"><i class="ft-mail"></i></a><a href="timeline-center.html" class="btn btn-outline-primary"><i class="ft-pie-chart"></i></a>
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
                                    <h4 class="card-title">Server-side processing</h4>
                                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                            <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                            <li><a data-action="close"><i class="ft-x"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block card-dashboard">
                                        <table id="egpay-datatable" class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th>{{__('ID')}}</th>
                                                <th>{{__('Area name (Arabic)')}}</th>
                                                <th>{{__('Area name (English)')}}</th>
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>
                                                <th>{{__('ID')}}</th>
                                                <th>{{__('Area name (Arabic)')}}</th>
                                                <th>{{__('Area name (English)')}}</th>
                                            </tr>
                                            </tfoot>
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



@section('footer')
    <script type="text/javascript">

        $('#egpay-datatable').DataTable({
            "iDisplayLength": 25,
            processing: true,
            serverSide: true,
            "ajax": {
                "url": "{{url()->full()}}",
                "type": "GET",
                "data": function(data){
                    data.isDataTable = "true";
                }
            },
        });
    </script>
@endsection
