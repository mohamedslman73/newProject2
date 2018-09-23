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
                                                <td>{{__('Client')}}</td>
                                                <td>
                                                    <a target="_blank" href="{{route('system.client.show',$result->client_id)}}"> {{$result->client_name}}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Price per cleaner')}}</td>
                                                <td>
                                                        {{$result->price_per_cleaner}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Total Price')}}</td>
                                                <td>
                                                  {{$result->total_price}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('file')}}</td>
                                                <td>
                                                    @if(!empty($result->file))
                                                    <a download="" href="{{asset($result->file)}}">{{__('File')}}</a>
                                                    @else
                                                    --
                                                    @endif
                                                </td>
                                            </tr>
                                            @php
                                            $coutBoys = 0;
                                            $coutGirls = 0;
                                            @endphp
                                            <tr>
                                                <td> {{__('Cleaners')}}</td>
                                                <td>
                                                    <table>
                                                        <thead>
                                                        <th>{{__('Name')}}</th>
                                                        <th>{{__('Boys')}}</th>
                                                        <th>{{__('Girles')}}</th>
                                                        </thead>

                                                        @if(!empty($result->department_id))
                                                        <tbody>
                                                        @foreach($result->department_id as $key => $value)
                                                            @php
                                                            $coutBoys +=$result->boys[$key];
                                                            $coutGirls +=$result->girles[$key];
                                                            @endphp
                                                          <tr>
                                                              <td>{{$names[$key]}}</td>
                                                              <td>{{$result->boys[$key]}}</td>
                                                              <td>{{$result->girles[$key]}}</td>
                                                          </tr>
                                                         @endforeach
                                                        </tbody>
                                                            @endif
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Total Boys :</td>
                                                <td colspan="2"><code>{{$coutBoys}}</code></td>
                                            </tr>
                                            <tr>
                                                <td >Total Girsl :</td>
                                                <td colspan="2"><code>{{$coutGirls}}</code></td>
                                            </tr>
                                            @php
                                                $coutCounts = 0;
                                                $coutPrice = 0;
                                                $countItems = 0;
                                            @endphp
                                            <tr>
                                                <td> {{__('Items')}}</td>
                                                <td>
                                                    <table>
                                                        <thead>
                                                        <th>{{__('Item')}}</th>
                                                        <th>{{__('Count')}}</th>
                                                        <th>{{__('Price')}}</th>
                                                        <th>{{__('Total Price')}}</th>
                                                        </thead>
                                                        @if(!empty($item_id))
                                                        <tbody>
                                                        @foreach($item_id as $key => $value)
                                                            @php
                                                                $coutCounts += $count[$key];
                                                                $coutPrice +=$price[$key];
                                                                $countItems +=$item_id[$key];
                                                            @endphp
                                                            <tr>
                                                                <td>{{$itemNames[$key]}}</td>
                                                                <td>{{$count[$key]}}</td>
                                                                <td>{{$price[$key]}}</td>
                                                                <td>{{$count[$key] * $price[$key]}}</td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                            @endif
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Total Number Of Counts :</td>
                                                <td colspan="2"><code>{{$coutCounts}}</code></td>

                                            </tr>
                                            @php
                                                $itemsTotalPrice =   $result->total_price -(($coutBoys + $coutGirls)* $result->price_per_cleaner);
                                              $cleanerTotalPrice =  $result->total_price -$itemsTotalPrice;
                                            @endphp
                                            <tr>
                                                <td>Total Items Price :</td>
                                                <td><code>{{$itemsTotalPrice}}</code></td>
                                            </tr>
                                            <tr>
                                                <td>Total Cleaners Price :</td>
                                                <td><code>{{$cleanerTotalPrice}}</code></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Created By')}}</td>
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



                </div>
            </div>
        </div>
    </div>

@endsection

@section('header')
@endsection

@section('footer')

@endsection
