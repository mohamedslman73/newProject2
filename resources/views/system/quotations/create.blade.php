@extends('system.layouts')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/forms/selects/select2.min.css')}}">
@endsection
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
            <div class="content-body">
                <!-- Server-side processing -->
                <section id="server-processing">
                    <div class="row">
                        @if($errors->any())
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="alert alert-danger">
                                        {{__('Some fields are invalid please fix them')}}
                                        <ul>
                                            @foreach($errors->all() as $key => $value)
                                                <li>{{$key}}: {{$value}}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @elseif(Session::has('status'))
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="alert alert-{{Session::get('status')}}">
                                        {{ Session::get('msg') }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        {!! Form::open(['route' => isset($result->id) ? ['system.quotations.update',$result->id]:'system.quotations.store','method' => isset($result->id) ?  'PATCH' : 'POST','files'=> true]) !!}
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-block card-dashboard">


                                        <div class="form-group col-sm-6{!! formError($errors,'type',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('type',__('Type')) }}
                                                {!! Form::select('type', ['company' =>'company','individual'=>'individual'],isset($result->id)? $result['type'] : old('type'),['style'=>'width: 100%;' ,'id'=>'type','class'=>'form-control col-md-12']) !!}
                                            </div>
                                            {!! formError($errors,'type') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'client_type',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('client_type',__('Client Type')) }}
                                                {!! Form::select('client_type', ['exsistClient' =>'client','newClient'=>'new Client'],isset($result->id)? $result['client_type'] : old('client_type'),['onchange'=>'changeClientType()','style'=>'width: 100%;' ,'id'=>'client_type','class'=>'form-control col-md-12']) !!}
                                            </div>
                                            {!! formError($errors,'type') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'name',true) !!} newClient client">
                                            <div class="controls">
                                                {!! Form::label('name', __('Name')) !!}
                                                {!! Form::text('name',isset($result->id) ? $result->name:old('name'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'name') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'phone',true) !!} newClient client">
                                            <div class="controls">
                                                {!! Form::label('phone', __('phone')) !!}
                                                {!! Form::text('phone',isset($result->id) ? $result->phone:old('phone'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'phone') !!}
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'address',true) !!} newClient client">
                                            <div class="controls">
                                                {!! Form::label('address', __('Address')) !!}
                                                {!! Form::textArea('address',isset($result->id) ? $result->address:old('address'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'address') !!}
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'client_id',true) !!} exsistClient client">
                                            <div class="controls">
                                                {{ Form::label('client',__('client')) }}
                                                {!! Form::select('client_id',isset($result->id) ? [$result->client_id =>$result->client_name]:[''=>__('Select client')],isset($result->id) ? $result->client_id:old('client_id'),['style'=>'width: 100%;' ,'id'=>'client','class'=>'form-control col-md-12']) !!}
                                            </div>
                                            {!! formError($errors,'client_id') !!}
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'description',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('description',__('description:')) }}
                                                {!! Form::textArea('description',isset($result->id) ? $result->description:old('description'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'description') !!}
                                        </div>


                                        <div class="form-group col-sm-12{!! formError($errors,'price_per_cleaner',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('price_per_cleaner', __('price per cleaner')) !!}
                                                {!! Form::text('price_per_cleaner',isset($result->id) ? $result->price_per_cleaner:old('price_per_cleaner'),['class'=>'form-control','onchange'=>'count_total_cleaner("gilrs_0")']) !!}
                                            </div>
                                            {!! formError($errors,'price_per_cleaner') !!}
                                        </div>


                                        <div class="form-group col-sm-12{!! formError($errors,'file',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('file', __('File')) !!}
                                                {!! Form::file('file',['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'file') !!}
                                        </div>




                                        <div id="cleaners" >

                                            <div class="col-sm-12 "  >
                                                <button onclick="addRow()" type="button" class="btn btn-primary fa fa-plus addinputfile">
                                                    <span>{{__('add Cleaners Row')}}</span>
                                                </button>
                                            </div>

                                            @if(old('department_id') && old('girles') && old('boys'))
                                                @foreach(old('department_id') as $key=> $row)

                                            <div class="cleanersRow" >

                                        <div class="form-group col-sm-4{!! formError($errors,'department_id',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('department_id',__('department')) }}
                                                {!! Form::select('department_id[]',[''=>__('Select')]+array_column($department->toArray(),'name','id'),old('department_id')[$key],['class'=>'form-control']) !!}

                                            </div>
                                            {!! formError($errors,'department_id') !!}
                                        </div>

                                            <div class="form-group col-sm-2{!! formError($errors,'name',true) !!}">
                                                <div class="controls">
                                                    {{ Form::label('girles',__('girles')) }}
                                                    {!! Form::number('girles[]',old('girles')[$key],['class'=>'form-control']) !!}
                                                </div>
                                                {!! formError($errors,'girles') !!}
                                            </div>

                                            <div class="form-group col-sm-2{!! formError($errors,'boys',true) !!}">
                                                <div class="controls">
                                                    {{ Form::label('boys',__('boys')) }}
                                                    {!! Form::number('boys[]',old('boys')[$key],['class'=>'form-control']) !!}
                                                </div>
                                                {!! formError($errors,'boys') !!}
                                            </div>
                                                <div class="form-group col-sm-2{!! formError($errors,'total',true) !!}">
                                                    <div class="controls">
                                                        {{ Form::label('total',__('total')) }}
                                                        {!! Form::number('total[]','',['id'=>'total_cleaner_0','class'=>'form-control  total','disabled'=>'disabled']) !!}
                                                    </div>
                                                    {!! formError($errors,'price') !!}
                                                </div>

                                            <div class="col-sm-2 form-group">
                                                <a href="javascript:void(0);" onclick="$(this).closest('.cleanersRow').remove();" class="text-danger">
                                                    <i class="fa fa-lg fa-trash mt-3"></i>
                                                </a>
                                            </div>
                                            </div>


                                                @endforeach

                                                    @elseif(isset($result->department_id) && isset($result->girles) && isset($result->boys))
                                                        @foreach($result->department_id as $key=> $row)

                                                            <div class="cleanersRow" >

                                                                <div class="form-group col-sm-4{!! formError($errors,'name',true) !!}">
                                                                    <div class="controls">
                                                                        {{ Form::label('department_id',__('department')) }}
                                                                        {!! Form::select('department_id[]',[''=>__('Select')]+array_column($department->toArray(),'name','id'), $result->department_id[$key],['class'=>'form-control']) !!}
                                                                    </div>
                                                                    {!! formError($errors,'department_id') !!}
                                                                </div>

                                                                <div class="form-group col-sm-2{!! formError($errors,'name',true) !!}">
                                                                    <div class="controls">
                                                                        {{ Form::label('name',__('girles')) }}
                                                                        {!! Form::number('girles[]',$result->girles[$key],['class'=>'form-control','onchange'=>'count_total(this.id)']) !!}
                                                                    </div>
                                                                    {!! formError($errors,'girles') !!}
                                                                </div>

                                                                <div class="form-group col-sm-2{!! formError($errors,'boys',true) !!}">
                                                                    <div class="controls">
                                                                        {{ Form::label('boys',__('boys')) }}
                                                                        {!! Form::number('boys[]',$result->boys[$key],['class'=>'form-control','onchange'=>'count_total(this.id)']) !!}

                                                                    </div>
                                                                    {!! formError($errors,'boys') !!}
                                                                </div>
                                                                <div class="form-group col-sm-2{!! formError($errors,'total',true) !!}">
                                                                    <div class="controls">
                                                                        {{ Form::label('total',__('total')) }}
                                                                        {!! Form::number('total[]',($result->boys[$key] * $result->price_per_cleaner) + ($result->girles[$key] * $result->price_per_cleaner),['id'=>'total_cleaner_'.$key,'class'=>'form-control  total','disabled'=>'disabled']) !!}

                                                                    </div>
                                                                    {!! formError($errors,'price') !!}
                                                                </div>

                                                                <div class="col-sm-2 form-group">
                                                                    <a href="javascript:void(0);" onclick="$(thist).closest('.cleanersRow').remove();" class="text-danger">
                                                                        <i class="fa fa-lg fa-trash mt-3"></i>
                                                                    </a>
                                                                </div>
                                                            </div>

                                                        @endforeach

                                                @else
                                                <div class="cleanersRow" >

                                                    <div class="form-group col-sm-4{!! formError($errors,'name',true) !!}">
                                                        <div class="controls">
                                                            {{ Form::label('department_id',__('department')) }}
                                                            {!! Form::select('department_id[]',[''=>__('Select')]+array_column($department->toArray(),'name','id'), null,['class'=>'form-control']) !!}                                                        </div>
                                                        {!! formError($errors,'department_id') !!}
                                                    </div>

                                                    <div class="form-group col-sm-2{!! formError($errors,'name',true) !!}">
                                                        <div class="controls">
                                                            {{ Form::label('name',__('girles')) }}
                                                            {!! Form::number('girles[]',null,['id'=>'girls_0','class'=>'form-control','onchange'=>'count_total_cleaner(this.id)']) !!}
                                                        </div>
                                                        {!! formError($errors,'girles') !!}
                                                    </div>

                                                    <div class="form-group col-sm-2{!! formError($errors,'boys',true) !!}">
                                                        <div class="controls">
                                                            {{ Form::label('boys',__('boys')) }}
                                                            {!! Form::number('boys[]',null,['id'=>'boys_0','class'=>'form-control','onchange'=>'count_total_cleaner(this.id)']) !!}
                                                        </div>
                                                        {!! formError($errors,'boys') !!}
                                                    </div>

                                                    <div class="form-group col-sm-2{!! formError($errors,'total',true) !!}">
                                                        <div class="controls">
                                                            {{ Form::label('total',__('total')) }}
                                                            {!! Form::number('total[]','',['id'=>'total_cleaner_0','class'=>'form-control  total','disabled'=>'disabled']) !!}
                                                        </div>
                                                        {!! formError($errors,'price') !!}
                                                    </div>

                                                </div>


                                            @endif


                                        </div>

                                    <div id="Items" >
                                        <div class="col-sm-12 "  >
                                            <button onclick="addItemRow()" type="button" class="btn btn-primary fa fa-plus addinputfile">
                                                <span>{{__('add Item Row')}}</span>
                                            </button>
                                        </div>

                                        @if(old('item_id') && old('count') && old('price'))
                                            @foreach ( old('item_id') as $key=>$value)
                                                <div class="itemRow" >

                                                    <div class="form-group col-sm-4{!! formError($errors,'item_id',true) !!}">
                                                        <div class="controls">
                                                            {{ Form::label('item_id',__('Item')) }}
                                                            {!! Form::select('item_id[]',[$value=>"Item is Selected"],old('item_id')[$key],['style'=>'width: 100%;' ,'id'=>'item_id','class'=>'form-control col-md-12 item_id'.$key]) !!}
                                                        </div>
                                                        {!! formError($errors,'item_id') !!}
                                                    </div>

                                                    <div class="form-group col-sm-3{!! formError($errors,'count',true) !!}">
                                                        <div class="controls">
                                                            {{ Form::label('count',__('Count')) }}
                                                            {!! Form::number('count[]',old('count')[$key],['class'=>'form-control']) !!}
                                                        </div>
                                                        {!! formError($errors,'count') !!}
                                                    </div>

                                                    <div class="form-group col-sm-3{!! formError($errors,'price',true) !!}">
                                                        <div class="controls">
                                                            {{ Form::label('price',__('price')) }}
                                                            {!! Form::number('price[]',old('price')[$key],['class'=>'form-control']) !!}
                                                        </div>
                                                        {!! formError($errors,'price') !!}
                                                    </div>

                                                    <div class="col-sm-2 form-group">
                                                        <a href="javascript:void(0);" onclick="$(this).closest('.itemRow').remove();" class="text-danger">
                                                            <i class="fa fa-lg fa-trash mt-3"></i>
                                                        </a>
                                                    </div>
                                                </div>


                                            @endforeach

                                        @elseif(isset($result->item_id) && isset($result->count) && isset($result->price))

                                            @foreach($result->item_id as $key=> $row)


                                                <div class="itemsRow" >

                                                    <div class="form-group col-sm-4{!! formError($errors,'item_id',true) !!}">
                                                        <div class="controls">
                                                            {{ Form::label('item_id',__('Item')) }}
                                                            {!! Form::select('item_id[]',[$result->item_id[$key] =>$names[$key]],$result->item_id[$key],['style'=>'width: 100%;' ,'id'=>'item_id','class'=>'form-control col-md-12 item_id'.$key]) !!}
                                                        </div>
                                                        {!! formError($errors,'item_id') !!}
                                                    </div>

                                                    <div class="form-group col-sm-2{!! formError($errors,'count',true) !!}">
                                                        <div class="controls">
                                                            {{ Form::label('count',__('Count')) }}
                                                            {!! Form::number('count[]',$result->count[$key],['id'=>'count_'.$key,'class'=>'form-control','onchange'=>'count_total(this.id)']) !!}
                                                        </div>

                                                        {!! formError($errors,'count') !!}
                                                    </div>

                                                    <div class="form-group col-sm-2{!! formError($errors,'price',true) !!}">
                                                        <div class="controls">
                                                            {{ Form::label('price',__('Price')) }}
                                                            {!! Form::number('price[]',$result->price[$key],['id'=>'price_'.$key,'class'=>'form-control','onchange'=>'count_total(this.id)']) !!}
                                                        </div>
                                                        {!! formError($errors,'price') !!}
                                                    </div>

                                                    <div class="form-group col-sm-2{!! formError($errors,'price',true) !!}">
                                                        <div class="controls">
                                                            {{ Form::label('total',__('total')) }}
                                                            {!! Form::number('total[]',$result->count[$key] * $result->price[$key],['id'=>'total_'.$key,'class'=>'form-control price total','disabled'=>'disabled']) !!}
                                                        </div>
                                                        {!! formError($errors,'total') !!}
                                                    </div>

                                                    <div class="col-sm-2 form-group">
                                                        <a href="javascript:void(0);" onclick="$(this).closest('.itemRow').remove();" class="text-danger">
                                                            <i class="fa fa-lg fa-trash mt-3"></i>
                                                        </a>
                                                    </div>
                                                </div>


                                            @endforeach

                                        @else
                                            <div class="itemsRow" style="display: table;width: 100%">

                                                <div class="form-group col-sm-4{!! formError($errors,'item_id',true) !!}">
                                                    <div class="controls">
                                                        {{ Form::label('item_id',__('Item')) }}
                                                        {!! Form::select('item_id[]',isset($result->id) ? [$result->item_id =>$result->item_id]:[''=>__('Select Item')],isset($result->id) ? $result->item_id:old('item_id'),['style'=>'width: 100%;' ,'id'=>'item_id','class'=>'form-control col-md-12 item_id0']) !!}
                                                    </div>
                                                    {!! formError($errors,'item_id') !!}
                                                </div>

                                                <div class="form-group col-sm-2{!! formError($errors,'count',true) !!}">
                                                    <div class="controls">
                                                        {{ Form::label('count',__('Count')) }}
                                                        {!! Form::number('count[]',isset($result->id) ? $result->count:old('count'),['id'=>'count_0','class'=>'form-control','onchange'=>'count_total(this.id)']) !!}
                                                    </div>
                                                    {!! formError($errors,'count') !!}
                                                </div>

                                                <div class="form-group col-sm-2{!! formError($errors,'price',true) !!}">
                                                    <div class="controls">
                                                        {{ Form::label('price',__('Price')) }}
                                                        {!! Form::number('price[]',isset($result->id) ? $result->price:old('price'),['id'=>'price_0','class'=>'form-control','onchange'=>'count_total(this.id)']) !!}
                                                    </div>
                                                    {!! formError($errors,'price') !!}
                                                </div>
                                                <div class="form-group col-sm-2{!! formError($errors,'price',true) !!}">
                                                    <div class="controls">
                                                        {{ Form::label('total',__('total')) }}
                                                        {!! Form::number('total[]','',['id'=>'total_0','class'=>'form-control total','disabled'=>'disabled']) !!}
                                                    </div>
                                                    {!! formError($errors,'total') !!}
                                                </div>
                                                <div class="col-sm-2 form-group">

                                                </div>
                                            </div>

                                        @endif
                                    </div>

                                    </div>


                                    </div>
                                </div>
                            </div>

                    <div class="col-xs-12">
                        <div class="card-header">
                            <div class="card-body">
                                <div class="card-block card-dashboard col-md-12"  style="float: right">
                                    <div class="controls">
                                        {{ Form::label('price',__('total:')) }}
                                        {{--{{dd($result->total_price)}}--}}
                                        {!! Form::number('total_price',isset($result->id) ? $result->total_price:old('total_price'),['id'=>'total_price','class'=>'form-control','readonly'=>'readonly']) !!}
                                        {{--<span style="padding-top: 50px"><code>{{$result->total_price}}</code></span>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                        <div class="col-xs-12" style="padding-top: 20px;">
                            <div class="card-header">
                                <div class="card-body">
                                    <div class="card-block card-dashboard">
                                        {!! Form::submit(__('Save'),['class'=>'btn btn-success pull-right']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </section>





                <div id="cleanersTemp" style="display: none" >

                    <div class="cleanersRow" >
                    <div class="form-group col-sm-4{!! formError($errors,'department_id',true) !!}">
                        <div class="controls">
                            {{ Form::label('department_id',__('Department')) }}
                            {!! Form::select('department_id[]',[''=>__('Select')]+array_column($department->toArray(),'name','id'),null,['class'=>'form-control']) !!}
                        </div>


                        {!! formError($errors,'department_id') !!}

                    </div>
                    <div class="form-group col-sm-2{!! formError($errors,'girles',true) !!}">
                        <div class="controls">
                            {{ Form::label('girles',__('girles')) }}
                            {!! Form::number('girles[]','',['class'=>'form-control girls','onchange'=>'count_total_cleaner(this.id)']) !!}
                        </div>
                        {!! formError($errors,'girles') !!}
                    </div>

                    <div class="form-group col-sm-2{!! formError($errors,'boys',true) !!}">
                        <div class="controls">
                            {{ Form::label('boys',__('boys')) }}
                            {!! Form::number('boys[]','',['class'=>'form-control boys','onchange'=>'count_total_cleaner(this.id)']) !!}
                        </div>
                        {!! formError($errors,'boys') !!}
                    </div>
                        <div class="form-group col-sm-2{!! formError($errors,'total',true) !!}">
                            <div class="controls">
                                {{ Form::label('total',__('total')) }}
                                {!! Form::number('total[]','',['id'=>'','class'=>'form-control  total_cleaner','disabled'=>'disabled']) !!}
                            </div>
                            {!! formError($errors,'price') !!}
                        </div>

                    <div class="col-sm-2 form-group">
                        <a href="javascript:void(0);" onclick="$(this).closest('.cleanersRow').remove();count_total_price();" class="text-danger">
                            <i class="fa fa-lg fa-trash mt-3"></i>
                        </a>
                    </div>

                    </div>
                </div>

                <div id="itemTemp" style="display: none" >

                    <div class="itemRow" >




                            <div class="form-group col-sm-4{!! formError($errors,'item_id',true) !!}">
                                <div class="controls">
                                    {{ Form::label('item_id',__('Item')) }}
                                    {!! Form::select('item_id[]',[''=>__('Select Item')],null,['style'=>'width: 100%;' ,'class'=>'form-control col-md-12 item_id']) !!}
                                </div>
                                {!! formError($errors,'item_id') !!}
                            </div>

                            <div class="form-group col-sm-2{!! formError($errors,'count',true) !!}">
                                <div class="controls">
                                    {{ Form::label('count',__('Count')) }}
                                    {!! Form::number('count[]','',['id'=>'','class'=>'form-control count','onchange'=>'count_total(this.id)']) !!}
                                </div>
                                {!! formError($errors,'count') !!}
                            </div>

                            <div class="form-group col-sm-2{!! formError($errors,'price',true) !!}">
                                <div class="controls">
                                    {{ Form::label('price',__('Price')) }}
                                    {!! Form::number('price[]','',['id'=>'','class'=>'form-control price','onchange'=>'count_total(this.id)']) !!}
                                </div>
                                {!! formError($errors,'price') !!}
                            </div>

                            <div class="form-group col-sm-2{!! formError($errors,'total',true) !!}">
                                <div class="controls">
                                    {{ Form::label('total',__('total')) }}
                                    {!! Form::number('total[]','',['id'=>'','class'=>'form-control  total','disabled'=>'disabled']) !!}
                                </div>
                                {!! formError($errors,'price') !!}
                            </div>

                            <div class="col-sm-2 form-group">
                                <a href="javascript:void(0);" onclick="$(this).closest('.itemRow').remove();count_total_price();" class="text-danger">
                                    <i class="fa fa-lg fa-trash mt-3"></i>
                                </a>
                            </div>



                </div>

            </div>





                <!--/ Javascript sourced data -->

        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->
@endsection
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/datetime/bootstrap-datetimepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/pickadate/pickadate.css')}}">
@endsection



@section('footer')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/extensions/pace.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/daterange/daterangepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/pickers/datetime/bootstrap-datetimepicker.css')}}">
    <script src="{{asset('assets/system')}}/vendors/js/forms/select/select2.full.min.js" type="text/javascript"></script>

    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{asset('assets/system/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/dateTime/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/pickers/daterange/daterangepicker.js')}}" type="text/javascript"></script>
    <script type="text/javascript">

        ajaxSelect2('#client','client','',"{{route('system.ajax.get')}}");

        @if (isset($result->id))
@foreach ($result->item_id as $key=>$value)
ajaxSelect2('.item_id{{$key}}','item','',"{{route('system.ajax.get')}}");
        @endforeach
       @endif

       @if( !empty(old('item_id')) )
       @foreach (old('item_id') as $key=>$value)
ajaxSelect2('.item_id{{$key}}','item','',"{{route('system.ajax.get')}}");
        $('.item_id{{$key}}').val('{{$value}}').trigger("change");
        @endforeach
       @else
        ajaxSelect2('.item_id0','item','',"{{route('system.ajax.get')}}");
        @endif

        function addRow(){
            var length = $('#cleaners .cleanersRow').length;
            var clonedRow = $('#cleanersTemp').clone();
            clonedRow.find('.girls').attr('id','girls_'+length);
            clonedRow.find('.boys').attr('id','boys_'+length);
            clonedRow.find('.total_cleaner').attr('id','total_cleaner_'+length);
            $('#cleaners').append(clonedRow.html());

        }


        function addItemRow(){
            var length = $('#Items .itemRow').length + 1;

            var clonedRow = $('#itemTemp').clone();
            clonedRow.find('select').addClass('item_id'+length);
            clonedRow.find('.total').attr('id','total_'+length);
            clonedRow.find('.count').attr('id','count_'+length);
            clonedRow.find('.price').attr('id','price_'+length);

            $('#Items').append(clonedRow.html());
            ajaxSelect2('.item_id'+length,'item','',"{{route('system.ajax.get')}}");
            $('.item_id'+length).next().next().remove();
        }

        @if (isset($result->id))
       @foreach ($result->item_id as $key=>$value)
        ajaxSelect2('.item_id{{$key}}','item','',"{{route('system.ajax.get')}}");
        @endforeach
       @endif

       @if( !empty(old('item_id')) )
       @foreach (old('item_id') as $key=>$value)
        ajaxSelect2('.item_id{{$key}}','item','',"{{route('system.ajax.get')}}");
        $('.item_id{{$key}}').val('{{$value}}').trigger("change");
        @endforeach
       @else
        ajaxSelect2('.item_id0','item','',"{{route('system.ajax.get')}}");
        @endif



        function count_total_cleaner(id){
            var num = id.split("_");
            var ids = num[1];

            var price = $('#price_per_cleaner').val();

            var girl = $('#girls_'+ids).val();
            var boy = $('#boys_'+ids).val();
            var total = ( +girl * +price ) + ( +boy * +price );

            if (total !=0) {
                $('#total_cleaner_'+ids).val(total);

                count_total_price();
            }

        }

        function count_total(id){
            var num = id.split("_");
            var ids = num[1];

            var count = $('#count_'+ids).val() ;
            var price = $('#price_'+ids).val() ;
            var  total = count * price ;


            if (total !=0) {
                $('#total_'+ids).val(total);

                count_total_price();
            }

        }

        function count_total_price(){
            var total_price = 0;

            var count = 0 ;
            var price = 0;

            var length = $('#Items .itemRow').length + 1;

            for(var x = 0; x < length ; x++ ){

                count = $('#count_'+x).val();
                price = $('#price_'+x).val();
                total_price +=  +count * +price ;
            }

            var boys = 0;
            var girls = 0;
            var total_price2 = 0;
            var lengthClenner = $('#cleaners .cleanersRow').length;

            for(var x = 0; x < lengthClenner ; x++ ){

                boys = $('#boys_'+x).val();
                girls = $('#girls_'+x).val();
                var price_per_cleaner = $('#price_per_cleaner').val();

                total_price2 +=  (+boys * +price_per_cleaner) + (+girls * +price_per_cleaner) ;
            }

            $('#total_price').val( +total_price + +total_price2 );


        }

        function changeClientType(){
            $('.client').hide();
            $('.'+$('#client_type').val()).show();
        }

        $(function(){
            changeClientType();
            count_total_price();
        });

    </script>
@endsection