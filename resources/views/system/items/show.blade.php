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
                                            <tr>
                                                <td>{{__('Status')}} </td>
                                                <td>
                                                    @if($result->status == 'active')
                                                        <b style="color: green">{{__('Active')}}</b>
                                                    @else
                                                        <b style="color: red">{{__('In-Active')}}</b>
                                                    @endif

                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Number Of Supplier For this Item')}}</td>
                                                <td>
                                                  <code>{{$supplierCount}}</code>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Number Of Supplier Orders For this Item')}}</td>
                                                <td>
                                                    <code>{{$supplierOrdersCount}}</code>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Number Of Client Orders For this Item')}}</td>
                                                <td>
                                                    <code>{{$clientOrderCount}}</code>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Number Of Projects For this Item')}}</td>
                                                <td>
                                                    <code>{{$projectCount}}</code>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Name')}}</td>
                                                <td>
                                                    {{$result->name}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Created_By')}}</td>
                                                <td>
                                                    <a target="_blank" href="{{route('system.staff.show',$result->staff_id)}}" target="_blank">{{$result->staff->Fullname}}</a>
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
        </div>
    </div>

@endsection

@section('header')
@endsection

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
    </script>
@endsection
