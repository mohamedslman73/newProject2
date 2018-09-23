<?php

namespace App\Modules\System;

use App\Models\Certificate;
use App\Models\ClientTypes;
use App\Models\Clothe;
use App\Models\ItemCategories;
use App\Models\SupplierCategories;
use App\Models\VacationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class CertificateController extends SystemController
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
            $eloquentData = Certificate::select([
                'id',
                'name',
                'staff_id',
                'created_at'
            ]);
            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
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
                                <li class=\"dropdown-item\"><a href=\"" . route('system.certificates.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.certificates.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Name'), __('Created By'), __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Certificates')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Certificates');
            } else {
                $this->viewData['pageTitle'] = __('Certificates');
            }

            return $this->view('certificates.index', $this->viewData);
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
            'text' => __('Certificate'),
            'url' => route('system.certificates.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Certificate'),
        ];

        $this->viewData['pageTitle'] = __('Create Certificate');
        return $this->view('certificates.create', $this->viewData);

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
        $clientTypes = Certificate::create($theRequest);
        if ($clientTypes)
            return redirect()
                ->route('system.certificates.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.certificates.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add certificate'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Certificate $certificates )
    {
        //dd($supplier_category);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Certificate'),
                'url' => route('system.certificates.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Clothe';
        $this->viewData['result'] = $certificates;
        return $this->view('certificates.show', $this->viewData);
    }

    public function edit(Certificate $certificate)
    {

        $this->viewData['breadcrumb'][] = [
            'text' => __('Certificates'),
            'url' => route('system.certificates.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Certificate'),
        ];


        $this->viewData['pageTitle'] = __('Edit Certificate');
        $this->viewData['result'] = $certificate;

        return $this->view('certificates.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Certificate $certificate)
    {
        ;
        $this->validate($request,[
            'name' =>'required',
        ]);
        $theRequest = $request->only([
            'name',
        ]);
        if ($certificate->update($theRequest)) {

            return redirect()
                ->route('system.certificates.edit', $certificate->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Certificate'));
        }
        else {
            return redirect()
                ->route('system.certificates.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Certificate'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Certificate $certificate)
    {
        $certificate->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('This Certificate has been deleted successfully')];
        } else {
            redirect()
                ->route('system.certificates.index')
                ->with('status', 'success')
                ->with('msg', __('This Certificate has been deleted'));
        }
    }
}
