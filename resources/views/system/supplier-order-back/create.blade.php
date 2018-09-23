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

                        {!! Form::open(['route' => isset($result->id) ? ['system.supplier-order-back.update',$result->id]:'system.supplier-order-back.store','method' => isset($result->id) ?  'PATCH' : 'POST','files'=> true]) !!}
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-block card-dashboard">


                                        <div class="form-group col-sm-8{!! formError($errors,'supplier_id',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('supplier_id',__('Supplier :')) }}
                                                {!! Form::select('supplier_id',isset($result->id) ? [$result->supplier_id =>$result->supplier->name]:[''=>__('Select Supplier')],isset($result->id) ? $result->supplier_id:old('supplier_id'),['style'=>'width: 100%;' ,'id'=>'supplier','class'=>'form-control col-md-12']) !!}
                                            </div>
                                            {!! formError($errors,'supplier_id') !!}
                                        </div>

                                        <div class="form-group col-sm-4{!! formError($errors,'description',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('date',__('Date:')) }}
                                                {!! Form::text('date',isset($result->id) ? $result->date:old('date'),['class'=>'form-control datepicker']) !!}
                                            </div>
                                            {!! formError($errors,'date') !!}
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'supplier_id',true) !!}">
                                            <div class="controls">
                                                {{ Form::label('supplier_order_id',__('Supplier Order ID :')) }}
                                                {!! Form::select('supplier_order_id',isset($result->id) ? [$result->supplier_order_id =>$result->supplier_order_id]:[''=>__('Select Supplier Order ID')],isset($result->id) ? $result->supplier_order_id:old('supplier_order_id'),['style'=>'width: 100%;' ,'id'=>'supplier_order_id','class'=>'form-control col-md-12']) !!}
                                            </div>
                                            {!! formError($errors,'supplier_order_id') !!}
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'note',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('note', __('Note (Optional) :')) !!}
                                                {!! Form::textarea('note',isset($result->id) ? $result->note:old('note'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'note') !!}
                                        </div>

                                        <div id="cleaners">

                                            <div class="col-sm-12 "  >
                                                <button onclick="addRow()" type="button" class="btn btn-primary fa fa-plus addinputfile">
                                                    <span>{{__('add Item Row')}}</span>
                                                </button>
                                            </div>

                                            @if(old('item_id') && old('count') && old('price'))
                                                @foreach(old('item_id') as $key=> $row)

                                            <div class="cleanersRow" >
                                                <div class="form-group col-sm-4{!! formError($errors,'item_id',true) !!}">
                                                    <div class="controls">
                                                        {{ Form::label('item_id',__('Item')) }}
                                                        {!! Form::select('item_id[]',[$row=>"Item is Selected"],old('item_id')[$key],['style'=>'width: 100%;' ,'id'=>'item_id','class'=>'form-control col-md-12 item_id'.$key]) !!}
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
                                                <a href="javascript:void(0);" onclick="$(this).closest('.cleanersRow').remove();" class="text-danger">
                                                    <i class="fa fa-lg fa-trash mt-3"></i>
                                                </a>
                                            </div>
                                            </div>

                                                @endforeach
                                                @else
                                                <div class="cleanersRow" style="display: table;width: 100%">

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
                                                        {!! formError($errors,'price') !!}
                                                    </div>
                                                    <div class="col-sm-2 form-group">
                                                        <a href="javascript:void(0);" onclick="$(this).closest('.cleanersRow').remove();count_total_price();" class="text-danger">
                                                            <i class="fa fa-lg fa-trash mt-3"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="card-header">
                                    <div class="card-body">
                                        <div class="card-block card-dashboard col-md-12"  style="float: right">
                                            <div class="controls">
                                                {{ Form::label('price',__('total')) }}
                                                {!! Form::number('total_price','',['id'=>'total_price','class'=>'form-control','disabled'=>'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <div class="col-xs-12"  style="padding-top: 20px;">
                            <div class="card-header">
                                <div class="card-body">
                                    <div class="card-block card-dashboard" >
                                        {!! Form::submit(__('Save'),['class'=>'btn btn-success pull-right']) !!}
                                    </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </section>
                <div id="cleanersTemp" style="visibility: hidden;display: table;width:100%;" >

                    <div class="cleanersRow" >
                        <div class="form-group col-sm-4{!! formError($errors,'item_id',true) !!}">
                            <div class="controls">
                                {{ Form::label('item_id',__('Item')) }}
                                {!! Form::select('item_id[]',isset($result->id) ? [$result->item_id =>$result->item_id]:[''=>__('Select Item')],isset($result->id) ? $result->item_id:old('item_id'),['style'=>'width: 100%;' ,'class'=>'form-control col-md-12 item_id']) !!}
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

                        <div class="form-group col-sm-2{!! formError($errors,'price',true) !!}">
                            <div class="controls">
                                {{ Form::label('price',__('Price')) }}
                                {!! Form::number('total[]','',['id'=>'','class'=>'form-control  total','disabled'=>'disabled']) !!}
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

                <!--/ Javascript sourced data -->
            </div>
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

        ajaxSelect2('#supplier','supplier','',"{{route('system.ajax.get')}}");
        ajaxSelect2('#supplier_order_id','supplier_order_id','',"{{route('system.ajax.get')}}");


             @if (isset($result->id))
        @foreach ($items as $key=>$value)
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
            clonedRow.find('select').addClass('item_id'+length);
            clonedRow.find('.total').attr('id','total_'+length);
            clonedRow.find('.count').attr('id','count_'+length);
            clonedRow.find('.price').attr('id','price_'+length);
            $('#cleaners').append(clonedRow.html());
            ajaxSelect2('.item_id'+length,'item','',"{{route('system.ajax.get')}}");
            $('.item_id'+length).next().next().remove();
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
            var length = $('#cleaners .cleanersRow').length;

            for(var x = 0; x < length ; x++ ){
                count = $('#count_'+x).val();
                price = $('#price_'+x).val();
                total_price +=  +count * +price ;
            }
//            console.log(total_price);
            $('#total_price').val(total_price);
        }

        $(function(){
            count_total_price();
            $('.datepicker').datetimepicker({
                viewMode: 'months',
                format: 'YYYY-MM-DD'
            });
        });

    </script>
@endsection