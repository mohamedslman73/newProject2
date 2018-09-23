<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;
use App\Models\Department;
use App\Models\ProjectCleaners;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class ProjectCleanersController extends SystemController
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

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Department $department)
    {

    }

    public function edit(Department $department)
    {

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

    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ProjectCleaners $projectCleaner)
    {
        $projectCleaner->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Project Cleaners has been deleted successfully')];
        } else {
            redirect()
                ->route('system.project-cleaners.index')
                ->with('status', 'success')
                ->with('msg', __('This project cleaner has been deleted'));
        }
    }
}
