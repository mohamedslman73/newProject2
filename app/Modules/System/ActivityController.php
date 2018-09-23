<?php

namespace App\Modules\System;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Yajra\Datatables\Facades\Datatables;
use Auth;
use Jenssegers\Agent\Agent;

class ActivityController extends SystemController
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){
            $systemLang = \DataLanguage::get();
            $eloquentData = Activity::with(['subject','causer'])
                ->select([
                    'id',
                    'log_name',
                    'description',
                    'subject_id',
                    'subject_type',
                    'causer_id',
                    'causer_type',
                    'created_at',
                    'updated_at'
                ]);

            whereBetween($eloquentData,'DATE(created_at)',$request->created_at1,$request->created_at2);

            if($request->id){
                $eloquentData->where('id', '=',$request->id);
            }

            if($request->description){
                $eloquentData->where('description', '=',$request->description);
            }

            if($request->subject_type){
                $eloquentData->where('subject_type', '=',$request->subject_type);
            }

            if($request->subject_id){
                $eloquentData->where('subject_id', '=',$request->subject_id);
            }

            if($request->causer_type){
                $eloquentData->where('causer_type', '=',$request->causer_type);
            }

            if($request->causer_id){
                $eloquentData->where('causer_id', '=',$request->causer_id);
            }




            if ($request->downloadExcel == "true") {
                if (staffCan('download.activity-log.excel')) {
                    $excelData = $eloquentData;
                    $excelData = $excelData->get();
                    exportXLS(__('Activity Log'),
                        [
                            'ID',
                            'Status',
                            'Model',
                            'User',
                            'Created At'

                        ],
                        $excelData,
                        [
                            'id' => 'id',
                            'description' => 'description',
                            'subject' => function($data){
                                return $data->subject_type.' ('.$data->subject_id.')';
                            },
                            'causer'=>function($data){
                                return $data->causer_type.' ('.$data->causer_id.')';
                            },
                            'created_at' =>function($data){
                                if ($data->created_at !=null) {
                                    return $data->created_at->diffForHumans();
                                }
                                return '--';
                            },
                        ]
                    );
                }
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('description','{{$description}}')
                ->addColumn('subject',function($data){
                    return $data->subject_type.' ('.$data->subject_id.')';
                })
                ->addColumn('causer',function($data){
                    return $data->causer_type.' ('.$data->causer_id.')';
                })
                ->addColumn('created_at','{{$created_at}}')

                ->addColumn('action',function($data){
                    return "<button class=\"btn btn-primary\" type=\"button\" onclick='urlIframe(\"".route('system.activity-log.show',$data->id)."\")'><i class=\"ft-eye\"></i></button>";
                })
                ->make(true);
        }else{

            // View Data
            $this->viewData['tableColumns'] = ['ID','Status','Model','User','Created At','Action'];
            $this->viewData['breadcrumb'][] = [
                'text'=> __('Activity Log')
            ];
            $this->viewData['pageTitle'] = __('Activity Log');


            return $this->view('activity-log.index',$this->viewData);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($ID){
        $result = Activity::findOrFail($ID);

        $agent = new Agent();
        $agent->setUserAgent($result->user_agent);
        $result->agent = $agent;

        $location = @json_decode(file_get_contents('http://ip-api.com/json/'.$result->ip));
        if($location->status!='fail')
            $result->location = $location;


        $this->viewData['result'] = $result;
        return $this->view('activity-log.show',$this->viewData);
    }

}
