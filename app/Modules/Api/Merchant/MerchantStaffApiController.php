<?php
namespace App\Modules\Api\Merchant;

use App\Models\MerchantProduct;
use App\Modules\Api\Transformers\MerchantStaffTransformer;
use App\Models\MerchantStaff;
use Auth;
use function foo\func;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MerchantStaffApiController extends MerchantApiController
{
    protected $Transformer;
    public function __construct(MerchantStaffTransformer $merchantStaffTransformer)
    {
        parent::__construct();
        $this->Transformer = $merchantStaffTransformer;
    }


    public function getalldata(Request $request){
        $merchantStaff = Auth::user();
        $merchant = $merchantStaff->merchant;

        $eloquentData = MerchantStaff::viewData($this->systemLang,['merchant_staff.status']);

        whereBetween($eloquentData,'merchant_staff.created_at',$request->created_at1,$request->created_at2);

        $eloquentData->where('merchant_staff_groups.merchant_id','=',$merchant->id);

        if($request->firstName){
            $eloquentData->where('merchant_staff.firstname','=',$request->firstName);
        }

        if($request->lastName){
            $eloquentData->where('merchant_staff.lastname','=',$request->lastName);
        }

        if($request->isActive){
            $status = ($request->isActive) ? 'active' : 'in-active';
            $eloquentData->where('merchant_staff.status','=',$status);
        }

        if($request->nationalId){
            $eloquentData->where('merchant_staff.national_id','=',$request->nationalId);
        }

        if($request->emailAddress){
            $eloquentData->where('merchant_staff.email','=',$request->emailAddress);
        }

        if($request->staffgroupId){
            $eloquentData->where('merchant_staff.merchant_staff_group_id','=',$request->staffgroupId);
        }


        $eloquentData->with('merchant_staff_group');

        $rows = $eloquentData->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('No Merchant staff to display'));

        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),[$this->systemLang]),__('Merchant staff details'));

    }

    public function create(Request $request){
        $merchantStaff = Auth::user();

        $merchantGroups = $merchantStaff->merchant->merchant_staff_group->pluck('id')->toArray();

        $RequestData = $this->headerdata(['firstname','lastname','email','national_id','merchant_staff_group_id','password','password_confirmation','status','branches']);

        $validator = Validator::make($RequestData, [
            'firstname'                 =>  'required',
            'lastname'                  =>  'required',
            'email'                     =>  'required',
            'national_id'               =>  'required|digits:14',
            'merchant_staff_group_id'   =>  'required|numeric|in:'.implode(',',$merchantGroups),
            'status'                    =>  'required|in:active,in-active',
            'password'                  =>  'required|confirmed',
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        if(isset($RequestData['branches'])){
            $nbranches = [];
            $merchantbranches = $merchantStaff->merchant->merchant_branch()->pluck('id')->toArray();
            foreach($RequestData['branches'] as $branchid){
                if(in_array($branchid,$merchantbranches))
                    $nbranches[] = $branchid;
            }
            $RequestData['branches'] = implode(',',$nbranches);
        } else {
            $RequestData['branches'] = '';
        }

        $therequest = $RequestData;
        $therequest['password'] = Hash::make($therequest['password']);

        try {
            $row = MerchantStaff::create($therequest);
                $nrow = MerchantStaff::where('id',$row->id)->with('merchant_staff_group')->first();
                return $this->respondCreated($this->Transformer->transform($nrow->toArray(),$this->systemLang),__('Merchant staff successfully added'));
        } catch (QueryException $e){
            if($e->getCode() == '23000')
                return $this->setStatusCode(200)->setCode(106)->respondWithoutError(false,__('Duplicated merchant staff'));
            return $this->setStatusCode(200)->respondWithoutError(false,__('Couldn\'t add Merchant Staff'));
        }
    }

    public function view($id){
        $merchantStaff = Auth::user();
        $merchantGroups = $merchantStaff->merchant->merchant_staff_group->pluck('id')->toArray();
        $row = MerchantStaff::wherein('merchant_staff_group_id',$merchantGroups)->where('id',$id)
            ->with('merchant_staff_group')
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('This Merchant staff doesn\'t exist'));

        return $this->respondSuccess($this->Transformer->transform($row->toArray(),$this->systemLang),__('Merchant staff details'));
    }

    public function edit($id,Request $request){
        $merchantStaff = Auth::user();
        $merchantGroups = $merchantStaff->merchant->merchant_staff_group->pluck('id')->toArray();
        $row = MerchantStaff::wherein('merchant_staff_group_id',$merchantGroups)->where('id',$id)
            ->with('merchant_staff_group')
            ->first();
        if(!$row)
            return $this->setStatusCode(200)->respondWithError(false,__('Product doesn\'t exist'));

        $RequestData = $this->headerdata(['firstname','lastname','email','national_id','merchant_staff_group_id','password','password_confirmation','status','branches']);

        $validator = Validator::make($RequestData, [
            'firstname'                 =>  'required',
            'lastname'                  =>  'required',
            'email'                     =>  'required',
            'national_id'               =>  'required|digits:14',
            'merchant_staff_group_id'   =>  'required|numeric|in:'.implode(',',$merchantGroups),
            'status'                    =>  'required|in:active,in-active',
            'password'                  =>  'confirmed',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        if(isset($RequestData['branches'])){
            $nbranches = [];
            $merchantbranches = $merchantStaff->merchant->merchant_branch()->pluck('id')->toArray();
            foreach($RequestData['branches'] as $branchid){
                if(in_array($branchid,$merchantbranches))
                    $nbranches[] = $branchid;
            }
            $RequestData['branches'] = implode(',',$nbranches);
        } else {
            $RequestData['branches'] = '';
        }

        if(!isset($RequestData['password']))
            $RequestData['password'] = null;
        else{
            $RequestData['password'] = Hash::make($RequestData['password']);
        }
        $therequest = array_filter($RequestData);

        try {
            $row->update($therequest);

            $nrow = MerchantStaff::where('id',$row->id)->with('merchant_staff_group')->first();
            return $this->respondSuccess($this->Transformer->transform($nrow->toArray(),$this->systemLang),__('Merchant staff details'));
        } catch (QueryException $e){
            if($e->getCode() == '23000')
                return $this->setStatusCode(200)->setCode(106)->respondWithError(false,__('Duplicated Merchant staff info'));
            return $this->setStatusCode(200)->respondWithError(false,__('Merchant staff couldn\'t be updated'));
        }

    }

    public function delete($id){
        $merchantStaff = Auth::user();
        $merchantGroups = $merchantStaff->merchant->merchant_staff_group->pluck('id')->toArray();
        $row = MerchantStaff::wherein('merchant_staff_group_id',$merchantGroups)->where('id',$id)
            ->first();
        if(!$row)
            return $this->setStatusCode(403)->respondWithError(false,__('Merchant staff doesn\'t exist'));

        if(!$row->delete())
            return $this->setStatusCode(403)->respondWithError(false,__('Merchant staff couldn\'t be deleted'));

        return $this->respondSuccess(false,__('Merchant staff successfully deleted'));
    }

}