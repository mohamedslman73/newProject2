@extends('system.layouts')
<div class="modal fade text-xs-left" id=""  role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <label class="modal-title text-text-bold-600" id="myModalLabel33">{{__('Add Managed Staff')}}</label>
            </div>
            {{--{!! Form::open(['route' => ['system.staff.add-managed-staff'],'method' => 'POST','id'=>'add-managed-staff-form','onsubmit'=>'addManagedStaffPOST();return false;']) !!}--}}
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

<!-- Button trigger modal -->

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
                                    <h4 class="card-title">
                                        {{__('Visa Traking Info')}}
                                        <span style="float: right;"><a class="btn btn-outline-primary"  href="javascript:void(0);" onclick="urlIframe('{{route('system.visa-tracking.edit',$result->id)}}')"><i class="fa fa-pencil"></i> {{__('Edit')}}</a></span>
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
                                                        {{$result->staff_name}}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Nationality')}}</td>
                                                    <td>
                                                        {{$result->nationality}}
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td>{{__('Gender')}}</td>
                                                    <td>
                                                        {{ucfirst($result->gender)}}
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td>{{__('Passport Number')}}</td>
                                                    <td>
                                                        <code>{{$result->passport_no}}</code>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('Date Of Visa Issue')}}</td>
                                                    <td>
                                                        <code>{{$result->date_of_visa_issue}}</code>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Visa Number')}}</td>
                                                    <td>
                                                        {{$result->visa_no}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('Visa Status')}}</td>
                                                    <td>
                                                        {{$result->visa_status}}
                                                    </td>
                                                </tr>
                                                     <tr>
                                                    <td>{{__('Joining Date')}}</td>
                                                    <td>
                                                        {{$result->joining_date}}
                                                    </td>
                                                </tr>
                                                 <tr>
                                                    <td>{{__('Created At')}}</td>
                                                    <td>
                                                        {{$result->created_at->diffForHumans()}}
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

    {{--<div class="app-content content container-fluid">--}}
        {{--<div class="content-wrapper">--}}
            {{--<div class="content-header row"></div>--}}
            {{--<div class="content-body">--}}
                {{--<div id="user-profile">--}}
                    {{--<div class="row">--}}
                        {{--<div class="col-xs-12">--}}
                            {{--<div class="card profile-with-cover">--}}
                                {{--<div class="card-img-top img-fluid bg-cover height-300" style="background: url('{{asset('assets/system/images/carousel/22.jpg')}}') 50%;"></div>--}}
                                {{--<div class="media profil-cover-details">--}}
                                    {{--@if($result->image)--}}
                                        {{--<div class="media-left pl-2 pt-2">--}}
                                            {{--<a href="jaascript:void(0);" class="profile-image">--}}
                                                {{--<img title="{{$result->firstname}} {{$result->lastname}}" src="{{asset('storage/app/'.imageResize($result->avatar,70,70))}}"  class="rounded-circle img-border height-100"  />--}}
                                            {{--</a>--}}
                                        {{--</div>--}}
                                    {{--@endif--}}
                                    {{--<div class="media-body media-middle row">--}}
                                        {{--<div class="col-xs-6">--}}
                                            {{--<h3 class="card-title" style="margin-bottom: 0.5rem;">--}}
                                                {{--{{$result->firstname}} {{$result->lastname}}--}}
                                                {{--@if($result->status == 'in-active')--}}
                                                    {{--<b style="color: red;">(IN-ACTIVE)</b>--}}
                                                {{--@endif--}}
                                            {{--</h3>--}}
                                            {{--<span>{{$result->address}}</span>--}}
                                        {{--</div>--}}
                                        {{--<div class="col-xs-6 text-xs-right">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<nav class="navbar navbar-light navbar-profile">--}}
                                    {{--<button class="navbar-toggler hidden-sm-up" type="button" data-toggle="collapse" data-target="#exCollapsingNavbar2" aria-controls="exCollapsingNavbar2" aria-expanded="false" aria-label="Toggle navigation"></button>--}}
                                    {{--<div class="collapse navbar-toggleable-xs" id="exCollapsingNavbar2">--}}
                                        {{--<ul class="nav navbar-nav float-xs-right">--}}
                                            {{--<li class="nav-item active">--}}
                                                {{--<a class="nav-link"  href="javascript:void(0);" onclick="urlIframe('{{route('system.staff-target.create',['id'=>$result->id])}}')"><i class="fa fa-dot-circle-o"></i> {{__('Add Target to :name',['name'=>$result->firstname.' '.$result->lastname])}} <span class="sr-only">(current)</span></a>--}}
                                            {{--</li>--}}

                                            {{--<li class="nav-item active">--}}
                                                {{--<a class="nav-link"  href="javascript:void(0);" onclick="urlIframe('{{route('system.staff.edit',$result->id)}}')"><i class="fa fa-pencil-square-o"></i> {{__('Edit Staff info')}} <span class="sr-only">(current)</span></a>--}}
                                            {{--</li>--}}


                                            {{--@if($supervisor  && staffCan('system.staff.change-merchant-sales',Auth::id()))--}}
                                                {{--<li class="nav-item active">--}}
                                                    {{--<a id="changeSupervisor-link"  data-toggle="modal" data-target="#changeSupervisor-modal" class="btn btn-sm btn-outline-primary"><i class="fa fa-pencil-square"></i> {{__('change Supervisor')}}</a>--}}
                                                {{--</li>--}}
                                            {{--@endif--}}
                                            {{--@if($sales  && staffCan('system.staff.change-merchant-sales',Auth::id()))--}}
                                                {{--<li class="nav-item active">--}}
                                                    {{--<a id="changeSales-link" data-toggle="modal" data-target="#changeSales-modal" class="btn btn-sm btn-outline-primary"><i class="fa fa-pencil-square"></i> {{__('change Sales')}}</a>--}}
                                                {{--</li>--}}
                                            {{--@endif--}}

                                            {{--@if(($sales   || $supervisor )  && staffCan('merchant.merchant.edit',Auth::id()))--}}
                                                {{--<li class="nav-item active">--}}
                                                    {{--<a id="changeStatus-link" data-toggle="modal" data-target="#changeStatus-modal" class="btn btn-sm btn-outline-primary"><i class="fa fa-pencil-square"></i> {{__('change status')}}</a>--}}
                                                {{--</li>--}}
                                            {{--@endif--}}
                                        {{--</ul>--}}
                                    {{--</div>--}}
                                {{--</nav>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}



                    {{--<div class="row">--}}
                        {{--<div class="col-md-4">--}}
                            {{--<section id="spacing" class="card">--}}
                                {{--<div class="card-header">--}}
                                    {{--<h4 class="card-title">--}}
                                        {{--{{__('Staff Info')}}--}}
                                        {{--<span style="float: right;"><a class="btn btn-outline-primary"  href="javascript:void(0);" onclick="urlIframe('{{route('system.staff.edit',$result->id)}}')"><i class="fa fa-pencil"></i> {{__('Edit')}}</a></span>--}}
                                    {{--</h4>--}}

                                {{--</div>--}}
                                {{--<div class="card-body collapse in">--}}
                                    {{--<div class="card-block">--}}
                                        {{--<div class="table-responsive">--}}
                                            {{--<table class="table table-hover">--}}
                                                {{--<thead>--}}
                                                {{--<tr>--}}
                                                    {{--<th>#</th>--}}
                                                    {{--<th>{{__('Value')}}</th>--}}
                                                {{--</tr>--}}
                                                {{--</thead>--}}
                                                {{--<tbody>--}}

                                                {{--<tr>--}}
                                                    {{--<td>{{__('ID')}}</td>--}}
                                                    {{--<td>{{$result->id}}</td>--}}
                                                {{--</tr>--}}


                                                {{--<tr>--}}
                                                    {{--<td>{{__('Name')}}</td>--}}
                                                    {{--<td>--}}
                                                        {{--{{$result->firstname}} {{$result->lastname}} ( {{$result->job_title}} )--}}
                                                    {{--</td>--}}
                                                {{--</tr>--}}

                                                {{--<tr>--}}
                                                    {{--<td>{{__('E-Mail')}}</td>--}}
                                                    {{--<td>--}}
                                                        {{--<a href="mailto:{{$result->email}}">{{$result->email}}</a>--}}
                                                    {{--</td>--}}
                                                {{--</tr>--}}

                                                {{--<tr>--}}
                                                    {{--<td>{{__('Mobile')}}</td>--}}
                                                    {{--<td>--}}
                                                        {{--<a href="tel:{{$result->mobile}}">{{$result->mobile}}</a>--}}
                                                    {{--</td>--}}
                                                {{--</tr>--}}

                                                {{--<tr>--}}
                                                    {{--<td>{{__('Gender')}}</td>--}}
                                                    {{--<td>--}}
                                                        {{--{{ucfirst($result->gender)}}--}}
                                                    {{--</td>--}}
                                                {{--</tr>--}}

                                                {{--<tr>--}}
                                                    {{--<td>{{__('National ID')}}</td>--}}
                                                    {{--<td>--}}
                                                        {{--{{$result->national_id}}--}}
                                                    {{--</td>--}}
                                                {{--</tr>--}}

                                                {{--<tr>--}}
                                                    {{--<td>{{__('Birthdate')}}</td>--}}
                                                    {{--<td>--}}
                                                        {{--{{$result->birthdate}}--}}
                                                    {{--</td>--}}
                                                {{--</tr>--}}


                                                {{--<tr>--}}
                                                    {{--<td>{{__('Description')}}</td>--}}
                                                    {{--<td>--}}
                                                        {{--<code>{{$result->description}}</code>--}}
                                                    {{--</td>--}}
                                                {{--</tr>--}}

                                                {{--<tr>--}}
                                                    {{--<td>{{__('Permission Group')}}</td>--}}
                                                    {{--<td>--}}
                                                        {{--<a href="{{route('system.permission-group.edit',$result->permission_group_id)}}">{{$result->permission_group->name}}</a>--}}
                                                    {{--</td>--}}
                                                {{--</tr>--}}



                                                {{--<tr>--}}
                                                    {{--<td>{{__('Last Login')}}</td>--}}
                                                    {{--<td>--}}
                                                        {{--@if($result->lastlogin == null)--}}
                                                            {{------}}
                                                        {{--@else--}}
                                                            {{--{{$result->lastlogin->diffForHumans()}}--}}
                                                        {{--@endif--}}
                                                    {{--</td>--}}
                                                {{--</tr>--}}


                                                {{--</tbody>--}}
                                            {{--</table>--}}


                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</section>--}}

                        {{--</div>--}}
                        {{--<div class="col-md-8 col-xs-12">--}}

                            {{--<div class="row">--}}

                                {{--<div class="col-xl-4 col-lg-6 col-xs-12">--}}
                                    {{--<div class="card">--}}
                                        {{--<div class="card-body">--}}
                                            {{--<div class="card-block">--}}
                                                {{--<div class="media">--}}
                                                    {{--<div class="media-body text-xs-left">--}}
                                                        {{--<h3 class="primary">{{number_format($result->merchant->count())}}</h3>--}}
                                                        {{--<span>--}}
                                                            {{--<a href="javascript:void(0);" onclick="urlIframe('{{route('merchant.merchant.index',['staff_id'=>$result->id])}}');">{{__('Merchant')}}</a>--}}
                                                        {{--</span>--}}
                                                    {{--</div>--}}
                                                    {{--<div class="media-right media-middle">--}}
                                                        {{--<i class="icon-user-follow primary font-large-2 float-xs-right"></i>--}}
                                                    {{--</div>--}}
                                                {{--</div>--}}
                                                {{--<progress class="progress progress-sm progress-primary mt-1 mb-0" value="{{ @round(($result->merchant->count()*100)/$totalMerchants) }}" max="100"></progress>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}

                                {{--<div class="col-xl-4 col-lg-6 col-xs-12">--}}
                                    {{--<div class="card">--}}
                                        {{--<div class="card-body">--}}
                                            {{--<div class="card-block">--}}
                                                {{--<div class="media">--}}
                                                    {{--<div class="media-body text-xs-left">--}}
                                                        {{--<h3 class="danger">{{number_format($result->activity_log->count())}}</h3>--}}
                                                        {{--<span>--}}
                                                            {{--<a href="javascript:void(0);" onclick="urlIframe('{{route('system.activity-log.index',['causer_type'=>'App\Models\Staff','causer_id'=>$result->id])}}');">{{__('System Action')}}</a>--}}
                                                        {{--</span>--}}
                                                    {{--</div>--}}
                                                    {{--<div class="media-right media-middle">--}}
                                                        {{--<i class="icon-social-dropbox danger font-large-2 float-xs-right"></i>--}}
                                                    {{--</div>--}}
                                                    {{--<progress class="progress progress-sm progress-danger mt-1 mb-0" value="{{ @round(($result->activity_log->count()*100)/$totalActivity) }}" max="100"></progress>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="col-xl-4 col-lg-6 col-xs-12">--}}
                                    {{--<div class="card">--}}
                                        {{--<div class="card-body">--}}
                                            {{--<div class="card-block">--}}
                                                {{--<div class="media">--}}
                                                    {{--<div class="media-body text-xs-left">--}}
                                                        {{--@php--}}
                                                            {{--$walletTransaction = $result->paymentWallet->allTransaction()->count();--}}
                                                        {{--@endphp--}}

                                                        {{--<h3 class="success">{{number_format($walletTransaction)}}</h3>--}}
                                                        {{--<span>--}}
                                                            {{--<a href="javascript:void(0);" onclick="urlIframe('{{route('system.wallet.show',$result->paymentWallet->id)}}');">{{__('Wallet Transaction')}}</a>--}}
                                                        {{--</span>--}}
                                                    {{--</div>--}}
                                                    {{--<div class="media-right media-middle">--}}
                                                        {{--<i class="icon-layers success font-large-2 float-xs-right"></i>--}}
                                                    {{--</div>--}}
                                                    {{--<progress class="progress progress-sm progress-success mt-1 mb-0" value="{{ @round(($walletTransaction*100)/$totalWalletsTransaction) }}" max="100"></progress>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}

                                {{--<div class="col-md-6">--}}
                                    {{--<div class="card">--}}
                                        {{--<div class="card-body">--}}
                                            {{--<div class="card-block">--}}
                                                {{--<div class="media">--}}
                                                    {{--<div class="media-body text-xs-left">--}}
                                                        {{--<h3 class="success">{{amount($result->paymentWallet->balance,true)}}</h3>--}}
                                                        {{--<span>{{__('Balance')}}</span>--}}
                                                    {{--</div>--}}
                                                    {{--<div class="media-right media-middle">--}}
                                                        {{--<i class="icon-layers success font-large-2 float-xs-right"></i>--}}
                                                    {{--</div>--}}
                                                    {{--<progress class="progress progress-sm progress-success mt-1 mb-0" value="{{ @round(($walletTransaction*100)/$totalWalletsTransaction) }}" max="100"></progress>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}


                                {{--<div class="col-md-6">--}}
                                    {{--<div class="card">--}}
                                        {{--<div class="card-body">--}}
                                            {{--<div class="card-block">--}}
                                                {{--<div class="media">--}}
                                                    {{--<div class="media-body text-xs-left">--}}
                                                        {{--<h3 class="success">--}}
                                                            {{--{{amount($payment,true)}}--}}
                                                            {{--<span style="float: right">--}}
                                                                {{--<a data-toggle="modal" data-target="#filter-modal" class="btn btn-sm btn-outline-primary"><i class="ft-search"></i> {{__('Filter')}}</a>--}}
                                                            {{--</span>--}}
                                                        {{--</h3>--}}
                                                        {{--<span>--}}
                                                            {{--{{__('Total Consumed')}}--}}
                                                        {{--</span>--}}
                                                    {{--</div>--}}
                                                    {{--<div class="media-right media-middle">--}}
                                                        {{--<i class="icon-layers success font-large-2 float-xs-right"></i>--}}
                                                    {{--</div>--}}
                                                    {{--<progress class="progress progress-sm progress-success mt-1 mb-0" value="100" max="100"></progress>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}



                                {{--@if($result->is_supervisor())--}}
                                    {{--<div class="col-md-12">--}}
                                        {{--<section id="spacing" class="card">--}}
                                            {{--<div class="card-header">--}}
                                                {{--<h4 class="card-title">--}}
                                                    {{--{{__('Managed Staff')}}--}}
                                                    {{--@if(staffCan('system.staff.add-managed-staff',Auth::id()))--}}
                                                        {{--<span style="float: right;"><a class="btn btn-outline-primary"  href="javascript:void(0);" onclick="addManagedStaff();"><i class="fa fa-plus"></i> {{__('Add')}}</a></span>--}}
                                                    {{--@endif--}}
                                                {{--</h4>--}}
                                            {{--</div>--}}
                                            {{--<div class="card-body collapse in">--}}
                                                {{--<div class="card-block">--}}
                                                    {{--<div class="table-responsive">--}}
                                                        {{--<table class="table table-hover">--}}
                                                            {{--<thead>--}}
                                                            {{--<tr>--}}
                                                                {{--<th>{{__('ID')}}</th>--}}
                                                                {{--<th>{{__('Image')}}</th>--}}
                                                                {{--<th>{{__('Name')}}</th>--}}
                                                                {{--<th>{{__('Mobile')}}</th>--}}
                                                                {{--<th>{{__('E-mail')}}</th>--}}
                                                                {{--<th>{{__('Permission Group')}}</th>--}}
                                                                {{--<th>{{__('Action')}}</th>--}}
                                                            {{--</tr>--}}
                                                            {{--</thead>--}}
                                                            {{--<tbody>--}}
                                                            {{--@foreach($result->managed_staff as $key => $value)--}}
                                                                {{--<tr>--}}
                                                                    {{--<td>{{$value->id}}</td>--}}
                                                                    {{--<td>--}}
                                                                        {{--@if(!$value->image)--}}
                                                                            {{------}}
                                                                        {{--@else--}}
                                                                            {{--<img src="{{asset('storage/'.image($value->image,70,70))}}" />--}}
                                                                        {{--@endif--}}
                                                                    {{--</td>--}}

                                                                    {{--<td>{{$value->firstname}} {{$value->lastname}}</td>--}}
                                                                    {{--<td><a href="tel:{{$value->mobile}}">{{$value->mobile}}</a></td>--}}
                                                                    {{--<td><a href="tel:{{$value->email}}">{{$value->email}}</a></td>--}}
                                                                    {{--<td>--}}
                                                                        {{--<a href="{{route('system.permission-group.edit',$value->permission_group->id)}}">{{$value->permission_group->name}}</a>--}}

                                                                    {{--</td>--}}
                                                                    {{--<td>--}}
                                                                        {{--<div class="dropdown">--}}
                                                                            {{--<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><i class="ft-cog icon-left"></i>--}}
                                                                                {{--<span class="caret"></span></button>--}}
                                                                            {{--<ul class="dropdown-menu">--}}
                                                                                {{--<li class="dropdown-item"><a href="{{route('system.staff.show',$value->id)}}">{{__('View')}}</a></li>--}}
                                                                                {{--<li class="dropdown-item"><a href="{{route('system.staff.edit',$value->id)}}">{{__('Edit')}}</a></li>--}}

                                                                                {{--@if(staffCan('system.staff.delete-managed-staff',Auth::id()))--}}
                                                                                    {{--<li class="dropdown-item"><a onclick="deleteRecord({{route('system.staff.delete-managed-staff',['id'=>$value->id])}})" href="javascript:void(0)">{{__('Remove')}}</a></li>--}}
                                                                                {{--@endif--}}
                                                                            {{--</ul>--}}
                                                                        {{--</div>--}}
                                                                    {{--</td>--}}
                                                                {{--</tr>--}}
                                                            {{--@endforeach--}}
                                                            {{--</tbody>--}}
                                                        {{--</table>--}}


                                                    {{--</div>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</section>--}}

                                    {{--</div>--}}
                                {{--@endif--}}

                            {{--</div>--}}



                        {{--</div>--}}

                    {{--</div>--}}






                {{--</div>--}}

            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    <!-- ////////////////////////////////////////////////////////////////////////////-->

    <div class="modal fade text-xs-left" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <label class="modal-title text-text-bold-600" id="myModalLabel33">{{__('Filter')}}</label>
                </div>
                {!! Form::open(['method'=>'GET'])!!}
                <div class="modal-body">

                    <div class="card-body">
                        <div class="card-block">
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        {{ Form::label('created_at1',__('Created From')) }}
                                        {!! Form::text('created_at1',null,['class'=>'form-control datepicker','id'=>'created_at1']) !!}
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        {{ Form::label('created_at2',__('Created To')) }}
                                        {!! Form::text('created_at2',null,['class'=>'form-control datepicker','id'=>'created_at2']) !!}
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

    <div class="modal fade" id="modal-map" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">View Map</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8" id="map"></div>
                    <div class="list-group-item col-md-12" id="instructions"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    </div>
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

        {{--function addManagedStaff(){--}}
            {{--$('#addManagedStaff-modal').modal('show');--}}
        {{--}--}}

        {{--function addManagedStaffPOST(){--}}
            {{--$formData = $('#add-managed-staff-form').serialize();--}}

            {{--$('#addManagedStaff-button').text('{{__('Loading...')}}').attr('disabled');--}}

            {{--$.post('{{route('system.staff.add-managed-staff')}}',$formData,function($data){--}}
                {{--$('#addManagedStaff-button').text('{{__('Submit')}}').removeAttr('disabled');--}}

                {{--if($data.status == false){--}}
                    {{--$('#addManagedStaff-alert').removeClass('alert-success')--}}
                        {{--.removeClass('alert-danger')--}}
                        {{--.addClass('alert-danger')--}}
                        {{--.text($data.msg);--}}
                {{--}else{--}}
                    {{--$('#addManagedStaff-alert').removeClass('alert-success')--}}
                        {{--.removeClass('alert-danger')--}}
                        {{--.addClass('alert-success')--}}
                        {{--.text($data.msg);--}}

                    {{--setTimeout(function(){--}}
                        {{--location.reload();--}}
                    {{--},2000);--}}

                {{--}--}}
            {{--},'json');--}}
        {{--}--}}



        function filterFunction($this){
            if($this == false) {
                $url = '{{url()->full()}}?is_total=true';
            }else {
                $url = '{{url()->full()}}?is_total=true&'+$this.serialize();
            }

            $dataTableVar.ajax.url($url).load();
            $('#filter-modal').modal('hide');
        }

        $(function(){
            $('.datepicker').datetimepicker({
                viewMode: 'months',
                format: 'YYYY-MM-DD'
            });
        });
        $('#staff-edit').on('submit',function (e) {
                e.preventDefault();
                var data = $(this).serialize();
                var  url = $(this).attr('action');
                $.post(url,data,function (data) {
                    $('#exampleModal').modal('hide');
                    if (data.status == true){

                        var messages = $('.messages');

                        var successHtml = '<div class="alert alert-success">'+
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>'+ data.msg +
                            '</div>';

                        $(messages).html(successHtml);
                    }else {

                        var messages = $('.messages');
                        var msg = '';
                        for(var i in data.error){
                            var value = data.error[i];
                            msg += '<p>'+value+'</p>';
                        }
                        var successHtml = '<div class="alert alert-danger">'+
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>'+ msg +
                            '</div>';
                        $(messages).html(successHtml);
                    }
                },'json')
            }
        );
    </script>
@endsection