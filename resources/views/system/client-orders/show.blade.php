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
                <div class="content-header-right col-md-8 col-xs-12">
                    <div class=" content-header-title mb-0" style="float: right;">
                        @include('system.breadcrumb')
                    </div>
                </div>
            </div>
            <div class="content-body"><!-- Spacing -->
                <div class="row">



                    <div class="col-md-12">
                        <section id="spacing" class="card">
                            <div class="card-header">

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

                                            @if(!empty($result->client_id))
                                            <tr>
                                                <td>{{__('Client')}}</td>
                                                <td>
                                                    <a target="_blank" href="{{route('system.client.show',$result->client->id)}}"> {{$result->client->name}}</a>
                                                </td>
                                            </tr>
                                            @elseif(!empty($result->project_id))
                                            <tr>
                                                <td>{{__('Project')}}</td>
                                                <td>
                                                    <a target="_blank" href="{{route('system.project.show',$result->project->id)}}"> {{$result->project->name}}</a>
                                                </td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td>{{__('Date')}}</td>
                                                <td>
                                                    <code>{{$result->date}}</code>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Total Price')}}</td>
                                                <td>
                                                    {{amount($result->total_price)}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Created_By')}}</td>
                                                <td>
                                                    <a target="_blank" href="{{route('system.staff.show',$result->staff->id)}}" target="_blank">{{$result->staff->Fullname}}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Created At')}}</td>
                                                <td>
                                                    @if($result->created_at == null)
                                                        --
                                                    @else
                                                        {{$result->created_at->format('Y-m-d H:iA')}}
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



                </div>
            </div>
            <!-- Server-side processing -->
            <section id="server-processing">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{__('Client Order Items')}}</h4>
                                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                        <li><a onclick="filterFunction(false);"><i class="ft-rotate-cw"></i></a></li>
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body collapse in">
                                <div class="card-block card-dashboard">
                                    <table style="text-align: center;" id="egpay-datatable" class="table table-striped table-bordered">
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
                        </div>
                    </div>
                </div>
            </section>
            <!--/ Javascript sourced data -->
        </div>
    </div>

@endsection

@section('header')
@endsection

@section('footer')
    <script type="text/javascript">
        $dataTableVar = $('#egpay-datatable').DataTable({
            "iDisplayLength": 25,
            processing: true,
            serverSide: true,
            "order": [[ 0, "desc" ]],
            "ajax": {
                "url": "{{url()->full()}}",
                "type": "GET",
                "data": function(data){
                    data.isItems = "true";
                }
            }

        });

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

    </script>
@endsection
