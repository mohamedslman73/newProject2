<?php

namespace App\Modules\System;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;
use App\Http\Requests\PermissionGroupFormRequest;
use Illuminate\Http\Request;
use Auth;


class PermissionGroupController extends SystemController
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

    public function index(Request $request){

        if($request->isDataTable){
            $systemLang = \DataLanguage::get();
            $eloquentData = PermissionGroup::select([
                'permission_groups.id',
                'permission_groups.name',
                "permission_groups.updated_at",
                DB::raw("(SELECT COUNT(*) FROM `staff` WHERE permission_group_id = `permission_groups`.`id`) as `count`")
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }



            if($request->downloadExcel == "true") {
                if (staffCan('download.permission-group.excel')) {
                    $excelData = $eloquentData;
                    $excelData = $excelData->get();
                    exportXLS(__('Permission Group'),
                        [
                            __('ID'),
                            __('name'),
                            __('Number Staff'),
                            __('Updated At'),

                        ],
                        $excelData,
                        [
                            'id'                                    => 'id',
                            'name'                                  =>'name',
                            'count'                                 =>'count',
                            'updated_at'                            =>'updated_at',
                        ]
                    );
                }
            }


            return Datatables::eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('count','{{$count}}')
                ->addColumn('updated_at','{{$updated_at}}')
                ->addColumn('action',function($data){
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"".route('system.permission-group.edit',$data->id)."\">".__('Edit')."</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        }else{

            // View Data
            $this->viewData['tableColumns'] = [__('ID'),__('Name'),__('Num. Staff'),__('Last Update'),__('Action')];
            $this->viewData['breadcrumb'][] = [
                'text'=> __('Staff Permission')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Staff Permission');
            }else{
                $this->viewData['pageTitle'] = __('Staff Permission');
            }

            return $this->view('permission-group.index',$this->viewData);
        }
    }

    public function create(Request $request)
    {
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Staff Permission'),
            'url'=> route('system.permission-group.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Staff Permission'),
        ];
//    $permissions = $this->permissions();
//    foreach ($permissions as $permission){
//        echo '<strong>'.$permission['name'].'</strong>'.'<br>';
//        foreach($permission['permissions'] as $key=>$val){
//         echo ucfirst(str_replace('-',' ',$key)).'<br> ';
//        }
//    }
        $this->viewData['pageTitle'] = __('Create Staff Permission');

        $this->viewData['permissions'] = $this->permissions();
        return $this->view('permission-group.create',$this->viewData);
    }


    public function store(PermissionGroupFormRequest $request)
    {
        $permissions = array();
        $perms = recursiveFind($this->permissions(),'permissions');
        foreach($perms as $val){
            foreach($val as $key=>$oneperm){
                $permissions[$key] = $oneperm;
            }
        }

        $coll = new Collection();

        $requestData = $request->all();

        if(!$requestData['whitelist_ip']){
            $requestData['whitelist_ip'] = null;
        }


        if($row = PermissionGroup::create($requestData)){
            array_map(function($oneperm) use ($permissions,$row,&$coll){
                foreach ($permissions[$oneperm] as $oneroute){
                    $coll->push(new Permission(['route_name'=>$oneroute,'permission_group_id'=>$row->id]));
                }
            },$request->all()['permissions']);
            $row->permission()->insert($coll->toArray());

            return redirect()
                ->route('system.permission-group.create')
                ->with('status', 'success')
                ->with('msg', __('Permission Group added'));
        } else{
            return redirect()
                ->route('system.permission-group.create')
                ->with('status','danger')
                ->with('msg',__('Sorry Couldn\'t add Permission Group'));
        }

    }


    public function show(PermissionGroup $permission_group)
    {
        return back();
    }


    public function edit(PermissionGroup $permission_group)
    {
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Staff Permission'),
            'url'=> route('system.permission-group.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit Staff Permission'),
        ];

        $this->viewData['pageTitle'] = __('Edit Staff Permission');

        $this->viewData['permission_group'] = $permission_group;
        $this->viewData['permissions'] = $this->permissions();
        $this->viewData['currentpermissions'] = $permission_group->permission()->get()->pluck('route_name')->toArray();

        return $this->view('permission-group.create',$this->viewData);
    }


    public function update(PermissionGroupFormRequest $request, PermissionGroup $permission_group)
    {
        $permissions = array();
        $perms = recursiveFind($this->permissions(),'permissions');
        foreach($perms as $val){
            foreach($val as $key=>$oneperm){
                $permissions[$key] = $oneperm;
            }
        }

        $requestData = $request->all();

        if(!$requestData['whitelist_ip']){
            $requestData['whitelist_ip'] = null;
        }

        if($request->only(['permissions'])['permissions'] !== null){
            $coll = new Collection();
            array_map(function($oneperm) use ($permissions,&$coll,$permission_group){
                foreach ($permissions[$oneperm] as $oneroute){
                    $coll->push(new Permission(['route_name'=> $oneroute,'permission_group_id'=> $permission_group->id]));
                }
            },$request->all()['permissions']);
        }


        if($permission_group->update($requestData)) {
            $permission_group->permission()->delete();
            if(isset($coll) && $coll->count()) {
                $permission_group->permission()->insert($coll->toArray());
            }

            return redirect()
                ->route('system.permission-group.edit',$permission_group->id)
                ->with('status','success')
                ->with('msg',__('Successfully edit Permissions Group'));
        }else{
            return redirect()
                ->route('system.permission-group.edit')
                ->with('status','success')
                ->with('msg',__('Sorry Couldn\'t Edit Permissions Group'));
        }
    }


    public function destroy(PermissionGroup $permission_group)
    {
        return back();
    }


    /*
    public function permissions($permission=false){
        $permissions = [
            'view-merchants'=>          ['merchant.index'],
            'create-merchants'=>        ['merchant.create'],
            'create-merchant'=>         ['merchant.create','merchant.store'],
            'create-permission-group'=> ['permission-group.create','permission-group.store'],
            'update-merchant'=>['merchant.edit','merchant.update'],
            'update-permission-group'=>['permission-group.edit','permission-group.update']
        ];
        return $permission ? $permissions[$permission] : $permissions;
    }
    */


}
