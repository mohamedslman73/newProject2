<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;
use App\Models\Department;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class DepartmentController extends SystemController
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
            $eloquentData = Department::with('staff');


            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);

            if ($request->id) {
                $eloquentData->where('id', '=', $request->id);
            }

            if ($request->name) {
                $eloquentData->where('name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->staff_id) {
                $eloquentData->where('staff_id', '=', $request->staff_id);
            }



            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('name', '{{$name}}')
                ->addColumn('staff_id', function($data){
                    return '<a href="'.route("system.staff.show",$data->staff_id).'" target="_blank">'.$data->staff->Fullname.'</a>';
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                  <li class=\"dropdown-item\"><a href=\"" . route('system.department.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.department.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Name'), __('Created By'), __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('department')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Item Categories');
            } else {
                $this->viewData['pageTitle'] = __('department');
            }

            return $this->view('department.index', $this->viewData);
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
            'text' => __('department'),
            'url' => route('system.department.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create department'),
        ];

        $this->viewData['pageTitle'] = __('Create department');
        return $this->view('department.create', $this->viewData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
           'name' =>'required',

        ]);
        $theRequest = [];
        $theRequest = $request->only([
            'name',

        ]);


        $theRequest['staff_id'] = Auth::id();
        $department = Department::create($theRequest);
        if ($department)
            return redirect()
                ->route('system.department.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.department.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add department'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Department $department)
    {
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Department'),
                'url' => route('system.department.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Department';
        $this->viewData['result'] = $department;
        $this->viewData['lang'] = \DataLanguage::get();

        return $this->view('department.show', $this->viewData);
    }

    public function edit(Department $department)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Department'),
            'url' => route('system.department.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Department'),
        ];


        $this->viewData['pageTitle'] = __('Edit Department');
        $this->viewData['result'] = $department;

        return $this->view('department.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Department $department)
    {
        $this->validate($request,[
            'name' =>'required',

        ]);
        $theRequest = $request->only([
            'name',

        ]);
        if ($department->update($theRequest)) {
          //  dd($category);
            return redirect()
                ->route('system.department.edit', $department->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit department'));
        }
        else {
            return redirect()
                ->route('system.department.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit department'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Department $department)
    {
        $department->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('department has been deleted successfully')];
        } else {
            redirect()
                ->route('system.department.index')
                ->with('status', 'success')
                ->with('msg', __('This $department has been deleted'));
        }
    }
}
