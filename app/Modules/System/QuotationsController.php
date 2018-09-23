<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;


use App\Models\Item;
use App\Models\Quotations;
use App\Models\Department;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class QuotationsController extends SystemController
{
    public function __construct(){
        parent::__construct();
        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Home'),
                'url'=> url('system'),
            ]
        ];
    }
    public function index(Request $request)
    {

        if ($request->isDataTable) {
            $eloquentData = Quotations::select('id','client_id','total_price','staff_id','created_at')
            ->with(['client','staff']);


            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);
            whereBetween($eloquentData, 'total_price', $request->price1, $request->price2);

            if ($request->id) {
                $eloquentData->where('id', '=', $request->id);
            }
            if ($request->client_id) {
                $eloquentData->where('client_id', '=', $request->client_id);
            }

            if ($request->status) {
                $eloquentData->where('status', '=', $request->status);
            }
            if ($request->staff_id) {
                $eloquentData->where('staff_id', '=', $request->staff_id);
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
//                ->addColumn('client_id', function($data){
//                    return $data->client->name;
//                })
                ->addColumn('total_price', '{{$total_price}}')
//                ->addColumn('status', '{{$status}}')

                ->addColumn('staff_id', function($data){
                    return '<a href="'.route("system.staff.show",$data->staff_id).'" target="_blank">'.$data->staff->Fullname.'</a>';
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d h:ia');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.quotations.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.quotations.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.quotations.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data

            $this->viewData['tableColumns'] = [
                __('ID'),
//                __('Client'),
                __('Total Price'),
//                __('Status'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Quotations')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Quotations');
            } else {
                $this->viewData['pageTitle'] = __('Quotations');
            }



            return $this->view('quotations.index', $this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text' => __('Quotations'),
            'url' => route('system.quotations.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Quotations'),
        ];

        $this->viewData['department'] = Department::get(['id','name']);

        $this->viewData['pageTitle'] = __('Create Quotations');
        return $this->view('quotations.create', $this->viewData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      //  dd($request->all());
        $theRequest = $request->only(['client_id','description','price_per_cleaner','status','department_id','girles','boys','item_id','count','price']);


        $validation = [

            'description'                    =>'required',
            'price_per_cleaner'              =>'required|numeric',
           // 'status'                         =>'required',
            'department_id'                  =>'array',
            'department_id.*'                =>'required',
            'girles'                         =>'array',
          //  'girles.*'                       => 'nullable|numeric',
            'boys'                           => 'array',
         //   'boys.*'                         => 'numeric|required_if:girls,==,',
        ];

        if($request->client_type == 'exsistClient')
            $validation['client_id'] = 'required|exists:clients,id';
        elseif($request->client_type == 'newClient'){
            $validation['name'] = 'required';
            $validation['phone'] = 'required|numeric|min:8';
            $validation['address'] = 'required';
        }

        foreach ($request->girles as $key=>$value){
            if ($request->girles[$key] == 0 && $request->boys[$key] ==0){
                $validation['boys.*'] = 'required|numeric';
                $validation['girles.*'] = 'nullable|numeric';
            }
        }

        if($request->item){
            $validation['price.*'] = 'required|numeric';
            $validation['count.*'] = 'required|numeric';
        }

        $this->validate($request,$validation);


        if($request->file){
        $theRequest['file'] = $request->file->store('quotations/'.date('y').'/'.date('m'));
    }

        $theRequest['staff_id']  = Auth::id();
             $cleaners = [
            'department_id'=>$theRequest['department_id'],
            'girles'=>$theRequest['girles'],
            'boys'=>$theRequest['boys']
        ];

        $items = [
            'item_id'=>$theRequest['item_id'],
            'count'=>$theRequest['count'],
            'price'=>$theRequest['price']
        ];


        $sumItems = [];
        foreach ($request->count as $key=>$value){
            $sumItems[] = $request->price[$key] * $value;
        }
        $theRequest['total_price'] = ($theRequest['price_per_cleaner']  *( array_sum($theRequest['boys']) + array_sum($theRequest['girles'])))+ array_sum($sumItems);
        $theRequest['cleaners'] = $cleaners;
        $theRequest['items'] = $items;
        $quotations = Quotations::create($theRequest);
        if ($quotations)
            return redirect()
                ->route('system.quotations.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.quotations.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add quotations'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Quotations $quotation)
    {
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Quotations'),
                'url' => route('system.quotations.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];

        $items = unserialize($quotation->items);
        $this->viewData['item_id'] = $items['item_id'];
        $this->viewData['count'] = $items['count'];
        $this->viewData['price'] = $items['price'];
        $cleaners = unserialize($quotation->cleaners);
        $this->viewData['pageTitle'] = __('Quotations');
        $this->viewData['result'] = $quotation;
        $this->viewData['result']->department_id = $cleaners['department_id'];
        $this->viewData['result']->girles = $cleaners['girles'];
        $this->viewData['result']->boys = $cleaners['boys'];
        $this->viewData['result']->client_name = $quotation->client->name;

        $names = [];
        $itemNames = [];
        if (!empty($this->viewData['result']->department_id)) {
            foreach ($this->viewData['result']->department_id as $key => $row) {

                $item = Department::find($row);
                $names[$key] = $item->name;
            }
        }

        if (!empty($this->viewData['item_id'])) {
            foreach ($this->viewData['item_id'] as $key => $row) {

                $item = Item::find($row);
                $itemNames[$key] = $item->name;
            }
        }
        $this->viewData['names'] =$names ;
        $this->viewData['itemNames'] =$itemNames ;
        return $this->view('quotations.show', $this->viewData);
    }

    public function edit(Quotations $quotation)
    {
//dd($quotation->toArray());
        $this->viewData['breadcrumb'][] = [
            'text' => __('Quotations'),
            'url' => route('system.quotations.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Quotations'),
        ];

        $cleaners = unserialize($quotation->cleaners);
        $this->viewData['pageTitle'] = __('Edit Quotations');
        $this->viewData['department'] = Department::get();
        $this->viewData['result'] = $quotation;
        $this->viewData['result']->department_id = $cleaners['department_id'];
        $this->viewData['result']->girles = $cleaners['girles'];
        $this->viewData['result']->boys = $cleaners['boys'];
        $this->viewData['result']->client_name = $quotation->client->name;
        $items = unserialize($quotation->items);
        $this->viewData['result']->item_id = $items['item_id'];

        $names = [];
        foreach ($this->viewData['result']->item_id as $key=>$row){

           $item = Item::find($row);
           $names[$key] = $item->name;

        }
        $this->viewData['names'] = $names;
        $this->viewData['result']->count = $items['count'];
        $this->viewData['result']->price = $items['price'];
        return $this->view('quotations.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Quotations $quotation)
    {
        //dd($request->all());

        $theRequest = $request->only(['client_id','description','price_per_cleaner','status','department_id','girles','boys','item_id','count','price']);
        $validation = [
            'client_id'                      =>'required|exists:clients,id',
            'description'                    =>'required',
            'price_per_cleaner'              =>'required|numeric',
            //'status'                         =>'required',
            'department_id'                  =>'array',
            'department_id.*'                =>'required',
            'girles'                         =>'array',
            //  'girles.*'                       => 'nullable|numeric',
            'boys'                           => 'array',
            //   'boys.*'                         => 'numeric|required_if:girls,==,',
        ];

        if(emptyArray($request->boys) == 'an empty array'){
            $validation['girles.*'] = 'required|numeric';
            $validation['boys.*'] = 'nullable|numeric';
        } elseif( emptyArray($request->girles) == 'an empty array'){
            $validation['boys.*'] = 'required|numeric';
            $validation['girles.*'] = 'nullable|numeric';
        }

        if($request->client_type == 'exsistClient')
            $validation['client_id'] = 'required|exists:clients,id';
        elseif($request->client_type == 'newClient'){
            $validation['name'] = 'required';
            $validation['phone'] = 'required|numeric|min:8';
            $validation['address'] = 'required';
        }

        if($request->item){
            $validation['price.*'] = 'required|numeric';
            $validation['count.*'] = 'required|numeric';
        }

        $this->validate($request,$validation);


        $theRequest = $request->all();
        if($request->file){
            $theRequest['file'] = $request->file->store('quotations/'.date('y').'/'.date('m'));
        }



        $theRequest['staff_id']  = Auth::id();
        $cleaners = [
            'department_id'=>$theRequest['department_id'],
            'girles'=>$theRequest['girles'],
            'boys'=>$theRequest['boys']
        ];
        $items = [
            'item_id'=>$theRequest['item_id'],
            'count'=>$theRequest['count'],
            'price'=>$theRequest['price']
        ];
        $sumItems = [];
        foreach ($request->count as $key=>$value){
            $sumItems[] = $request->price[$key] * $value;
        }
     //  dd(array_sum($sumItems));
        $theRequest['total_price'] = ($theRequest['price_per_cleaner']  *( array_sum($theRequest['boys']) + array_sum($theRequest['girles']) )+ array_sum($sumItems));
        $theRequest['cleaners'] = $cleaners;
        $theRequest['items'] = $items;

        if ($quotation->update($theRequest)) {
            return redirect()
                ->route('system.quotations.edit', $quotation->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit quotations'));
        }
        else {
            return redirect()
                ->route('system.quotations.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit quotations'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Quotations $quotation)
    {
        $quotation->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Quotations  has been deleted successfully')];
        } else {
            redirect()
                ->route('system.bus.index')
                ->with('status', 'success')
                ->with('msg', __('This Quotations  has been deleted'));
        }
    }
}
