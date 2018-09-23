<?php

namespace App\Modules\System;

use App\Models\Call;
use App\Models\Complain;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Auth;

class CallController extends SystemController
{

    public function __construct(){
        parent::__construct();
        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Home'),
                'url'=> url('system'),
            ],
            [
                'text'=> __('Calls'),
                'url'=> url('system/call')
            ]
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        if($request->isDataTable){

            $eloquentData = Call::select([
                'calls.id',
                'calls.call_time',
                'calls.phone_number',
                "calls.client_name",
                "calls.call_propose",
                "calls.call_details",
                "calls.status",
                "calls.reminder",
                "calls.staff_id",
                "calls.created_at",
                "calls.updated_at",
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
            ])
                ->join('staff', 'staff.id', '=', 'calls.staff_id');

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            whereBetween($eloquentData,'DATE(calls.created_at)',$request->created_at1,$request->created_at2);

            if($request->id){
                $eloquentData->where('calls.id', '=',$request->id);
            }
            if($request->status){
                $eloquentData->where('calls.status', '=',$request->type);
            }
            if($request->phone_number){
                $eloquentData->where('calls.phone_number','=',$request->phone_number);
            }

            if($request->client_name){
                $eloquentData->where('calls.client_name','=',$request->client_name);
            }

            if($request->staff_id){
                $eloquentData->where('calls.staff_id','=',$request->staff_id);
            }
            if($request->call_details){
                $eloquentData->where('calls.call_details','LIKE','%'.$request->call_details.'%');
            }
            if($request->call_propose){
                $eloquentData->where('calls.call_propose','LIKE','%'.$request->call_propose.'%');
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('status','{{$status}}')
                ->addColumn('phone_number', function($data){
                    return $data->phone_number;
                })
                ->addColumn('call_time', function($data){
                    return $data->call_time;
                })
                ->addColumn('client_name','{{$client_name}}')
                ->addColumn('call_propose','{{$call_propose}}')
//                ->addColumn('call_details',function ($data){
//                    return str_limit($data->call_details,25);
//                })
                ->addColumn('reminder',function ($data){
                    if ($data->reminder){
                        return $data->reminder;
                    }
                    return '--';
                })
                ->addColumn('staff_name', '<a href="{{route(\'system.staff.show\',$staff_id)}}" target="_blank">{{$staff_name}}</a>')
                ->addColumn('action',function($data){
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"javascript:;\" onclick='urlIframe(\"".route('system.call.show',$data->id)."\")'>".__('View')."</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.call.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('".route('system.call.destroy',$data->id)."')\" href=\"javascript:void(0)\">".__('Delete')."</a></li>
                              </ul>
                            </div>";
                })

                ->make(true);
        }else{

            // View Data
            $this->viewData['tableColumns'] = ['ID',__('Call status'),__('Phone#'),__('Calltime'),__('Caller Name'),__('Call Propose'),__('Reminder'),__('Created_By'),__('Action')];


            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Call');
            }else{
                $this->viewData['pageTitle'] = __('System Calls');
            }



            return $this->view('call.index',$this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Call'),
        ];

        $this->viewData['pageTitle'] = __('Create Call');

        $return  = [];
        $projects  = Project::get(['id','name']);
        foreach ($projects as $key=>$value){
            $return[$value->id] = $value->name;
        }
        $this->viewData['projects'] = $return;
        $this->viewData['pageTitle'] = __('Create Complain');

        return $this->view('call.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

//dd($request->client_id);
        $validation = [
            'status'                 =>    'required|in:high,intermediate,low',
            'call_propose'           =>      'required',
            'call_details'           =>      'required',
            'type'           =>      'required|in:in,out',
            'call_time'           =>      'required',

        ];
        if ($request->call_type == 'client'){
            // dd($request->all());
            $validation['client_id'] = 'required|exists:clients,id';
        }
        if ($request->call_type == 'other'){
            $validation['phone_number'] = 'required|min:8|max:16';
            $validation['client_name'] = 'required';
        }


        if ($request->call_propose == 'complain'){
            $validation['order_date'] = 'required';

           // $validation['complain_client_id'] = 'required|exists:clients,id';
            $validation['complain_of_staff_id'] = 'required|exists:staff,id';
            $validation['project_id'] = 'required|exists:projects,id';
        }
        $this->validate($request,$validation);

        $callData = $request->only(['client_id','phone_number','type','client_name','call_time','reminder','status','call_propose','call_details']);

        $complainData = $request->only(['order_date','complain_of_staff_id','project_id','call_details']);

        $callData['staff_id'] = Auth::id();


        $call =   Call::create($callData);
        if($call){
            $complainData['staff_id'] = Auth::id();
            $complainData['call_id'] = $call->id;
            Complain::create($complainData);
            return redirect()
                ->route('system.call.create')
                ->with('status', 'success')
                ->with('msg', __('Call has been added successfully'));
        } else {
            return redirect()
                ->route('system.call.create')
                ->with('status','danger')
                ->with('msg',__('Sorry Couldn\'t add call'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Call $call){
        $this->viewData['breadcrumb'][]= [
            'text'=>  $call->client_name,
        ];

        $this->viewData['pageTitle'] = __('Call  Information');
        $this->viewData['result'] = $call;
        $this->viewData['result']['complain'] = Complain::where('call_id',$call->id)->first();

        return $this->view('call.show',$this->viewData);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Call $call){

        $this->viewData['breadcrumb'][] = [
            'text'=> __('system Call'),
            'url'=> url('system/call')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit system call '),
        ];
        $this->viewData['pageTitle'] = __('Edit call');

        $return  = [];
        $projects  = Project::get(['id','name']);
        foreach ($projects as $key=>$value){
            $return[$value->id] = $value->name;
        }
        $this->viewData['projects'] = $return;
        $this->viewData['pageTitle'] = __('Create Complain');

        $this->viewData['result'] = $call;
        return $this->view('call.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Call $call)
    {
        $RequestedData = $request->only(['status','phone_number','client_name','call_time','call_propose','call_details','reminder']);
        $this->validate($request,[
            'status'                 =>    'required|in:high,intermediate,low',
            'phone_number'           =>      'required|min:8|max:16',
            'client_name'            =>      'required',
            'call_time'              =>      'required',
            'call_propose'           =>      'required',
            'call_details'           =>      'required',
            // 'reminder'               =>      'required',
        ]);

        if($call->update($RequestedData)) {
            return redirect()->route('system.call.edit',$call->id)
                ->with('status','success')
                ->with('msg',__('Successfully edited call'));
        }else{
            return redirect()->route('system.call.edit')
                ->with('status','danger')
                ->with('msg',__('Sorry Couldn\'t Edit call'));
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Call $call ,Request $request){

        if($call->delete()){
            if($request->ajax()){
                return ['status'=> true,'msg'=> __('call  has been deleted successfully')];
            }else{
                redirect()
                    ->route('system.call.index')
                    ->with('status','success')
                    ->with('msg',__('call has been deleted successfully'));
            }
        } else {
            if($request->ajax()){
                return ['status'=> false,'msg'=> __('Couldn\'t delete the call')];
            }else{
                redirect()
                    ->route('system.call.index')
                    ->with('status','danger')
                    ->with('msg',__('Couldn\'t delete the call'));
            }
        }

    }


}
