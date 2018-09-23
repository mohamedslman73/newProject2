<?php
namespace App\Modules\Api\Merchant;

use App\Models\MerchantProductCategory;
use App\Models\MerchantStaffGroup;
use App\Models\MerchantStaffPermission;
use App\Modules\Api\Transformers\MerchantStaffGroupTransformer;
use Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MerchantStaffGroupApiController extends MerchantApiController {

    protected $Transformer;

    public function __construct(MerchantStaffGroupTransformer $merchantStaffGroupTransformer)
    {
        parent::__construct();
        $this->Transformer =$merchantStaffGroupTransformer;
    }

    function getalldata(Request $request){
        $merchantStaff = Auth::user();
        $eloquentData = MerchantStaffGroup::viewData($this->systemLang);

        $eloquentData->where('merchant_staff_groups.merchant_id','=',$merchantStaff->merchant->id);

        if($request->groupId){
            $eloquentData->where('merchant_staff_groups.id','=',$request->groupId);
        }

        if($request->groupTitle){
            orWhereByLang($eloquentData,'merchant_staff_groups.title',$request->groupTitle);
        }

        $eloquentData->with(['permissions','merchant']);

        $rows = $eloquentData->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('There Are no Merchant staff group to display'));

        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),$this->systemLang),__('Merchant staff group to display'));
    }

    function create(Request $request){
        $merchantStaff = Auth::user();

        $RequestData = $this->headerdata(['title','permissions']);

        $validator = Validator::make($RequestData, [
            'title' => 'required',
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        if(is_array($RequestData['permissions'])){
            $Newpermissions = [];
            foreach($RequestData['permissions'] as $onepermission){
                $Roles = $this->permissions($onepermission);
                if(is_array($Roles))
                    $Newpermissions = array_merge($Newpermissions,$Roles);
            }
        }

        $theRequest = $RequestData;

        try {
            $row = $merchantStaff->merchant->merchant_staff_group()->create($theRequest);
            foreach($Newpermissions as $oneperm){
                MerchantStaffPermission::create(['route_name'=>$oneperm,'merchant_staff_group_id'=>$row->id]);
            }
            $nrow = MerchantStaffGroup::where('id',$row->id)->with('merchant')->with('permissions')->first();

            return $this->respondCreated($this->Transformer->transform($nrow->toArray(), [$this->systemLang]), __('Merchant staff group has been successfully added'));
        } catch (QueryException $e){
            if($e->getCode() == '23000')
                return $this->setCode(106)->respondWithError(false,__('Duplicated Category'));
            return $this->respondWithError(false,__('Sorry Couldn\'t add Category'));
        }

    }

    function view($id){
        $merchantStaff = Auth::user();
        $row = MerchantStaffGroup::where('merchant_id',$merchantStaff->merchant->id)->where('id',$id)->with('merchant')->with('permissions')->first();
        if(!$row)
            return $this->respondNotFound(false,__('Category not found'));
        else
            return $this->respondSuccess($this->Transformer->transform($row->toArray(),[$this->systemLang]),'Merchant staff group Details');
    }

    function edit($id,Request $request){
        $merchantStaff = Auth::user();
        $row = MerchantStaffGroup::where('merchant_id',$merchantStaff->merchant->id)->where('id',$id)->with('merchant')
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('Merchant staff not found'));

        $RequestData = $this->headerdata(['title','permissions']);

        $validator = Validator::make($RequestData, [
            'title' => 'required',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        if(is_array($RequestData['permissions'])){
            $Newpermissions = [];
            foreach($RequestData['permissions'] as $onepermission){
                $Roles = $this->permissions($onepermission);
                if(is_array($Roles))
                    $Newpermissions = array_merge($Newpermissions,$Roles);
            }
        }

        try {
            $row->update($RequestData);
            if(is_array($Newpermissions)){
                foreach($row->permissions as $PermObj){
                    $PermObj->delete();
                }
                foreach($Newpermissions as $oneperm){
                    MerchantStaffPermission::create(['route_name'=>$oneperm,'merchant_staff_group_id'=>$row->id]);
                }
            }
            return $this->respondSuccess($this->Transformer->transform(MerchantStaffGroup::where('id',$row->id)->with('merchant')->first()->toArray(),[$this->systemLang]),__('Merchant staff group successfully updated'));
        } catch (QueryException $e){
            if($e->getCode())
                return $this->setCode(106)->respondWithError(false,__('Duplicated Merchant stuff group'));
            return $this->setStatusCode(200)->respondWithError(false,__('Merchant staff group couldn\'t be updated'));
        }
    }

    function delete($id){
        $merchantStaff = Auth::user();
        $row = MerchantStaffGroup::where('merchant_id',$merchantStaff->merchant->id)->where('id',$id)->first();
        if(!$row)
            return $this->respondNotFound(false,__('Merchant staff doesn\'t exist, Or you don\'t have permissions to delete it'));

        if(!$row->delete())
            return $this->setStatusCode(403)->respondWithError(false,__('Merchant staff group Couldn\'t be deleted'));

        $allPerms = MerchantStaffPermission::where('merchant_staff_group_id','=',$id)->get()->pluck('id');
        MerchantStaffPermission::wherein('id',$allPerms)->delete();
        return $this->respondSuccess(false,__('Merchant staff group has been deleted successfully'));
    }


    function getallpermissions(){
        $permissions = $this->permissions();
        $data = [];
        foreach($permissions as $key=>$val){
            array_push($data,['permission'=>$key,'name'=>__(ucfirst(str_replace('-',' ',$key)))]);
        }
        return $this->respondSuccess($data,__('Merchant staff permissions'));
    }


}