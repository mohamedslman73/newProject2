<div class="col-xs-12" >

    <a  href="javascript:;" class="btn btn-primary text-center" onclick="$('input[name=\'cleaner_id[]\']').prop('checked',true)" >{{__('select All')}} <i class="fa fa-star" ></i> </a>
    <a  href="javascript:;" class="btn btn-outline-warning text-center" onclick="$('input[name=\'cleaner_id[]\']').prop('checked',false)" >{{__('Deselect All')}} <i class="fa fa-star-o" ></i> </a>
</div>

<div class="form-group col-sm-12">

    @if(isset($result->id))
        @foreach($cleaners as $key => $row)
            {{dd($result[$key])}}
            <div class="form-group col-sm-3 {!! formError($errors,'cleaner_id',true) !!}">
                <div class="controls">
                    {{ Form::label('cleaner_id',$row->Fullname) }}
                    {!! Form::checkbox('cleaner_id[]',isset($result->id) ? $result[$key] :old('cleaner_id')[$key],(isset($result->id) && $result[$key]->type == 'presence' )? true:false,['style'=>'width: 30%;' ,'id'=>'cleaner_id[]','class'=>'form-control col-md-4']) !!}

                </div>
                {!! formError($errors,'cleaner_id') !!}
            </div>
        @endforeach
    @elseif(!empty(old('cleaner_id'))   )
        @foreach(old('cleaner_id') as $key => $row)
            <div class="form-group col-sm-3 {!! formError($errors,'cleaner_id',true) !!}">
                <div class="controls">
                    {{ Form::label('cleaner_id',$row->Fullname) }}
                    {!! Form::checkbox('cleaner_id[]',isset($result->id) ? $result[$key] :old('cleaner_id')[$key],(isset($result->id) && $result[$key]->type == 'presence' )? true:false,['style'=>'width: 30%;' ,'id'=>'cleaner_id[]','class'=>'form-control col-md-4']) !!}

                </div>
                {!! formError($errors,'cleaner_id') !!}
            </div>
        @endforeach
    @else
@if(!empty($cleaners))
        @foreach($cleaners as $key => $row)

            <div class="form-group col-sm-3 {!! formError($errors,'cleaner_id',true) !!}">
                <div class="controls">
                    {{ Form::label('cleaner_id',$row->Fullname) }}
                    {!! Form::checkbox('cleaner_id[]',$row->id,false,['style'=>'width: 30%;' ,'id'=>'cleaner_id[]','class'=>'form-control col-md-4']) !!}

                </div>
                {!! formError($errors,'cleaner_id') !!}
            </div>
        @endforeach
    @endif


    @endif
</div>
