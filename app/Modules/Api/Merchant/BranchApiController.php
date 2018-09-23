<?php
namespace App\Modules\Api\Merchant;

use App\Libs\AreasData;
use App\Models\MerchantBranch;
use App\Modules\Api\Transformers\BranchTransformer;
use Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchApiController extends MerchantApiController {

    protected $Transformer;
    public function __construct(BranchTransformer $branchTransformer)
    {
        parent::__construct();
        $this->Transformer = $branchTransformer;
    }


    public function getalldata(Request $request){
        $merchantStaff = Auth::user();

            $eloquentData = MerchantBranch::viewData($this->systemLang,['merchant_branches.merchant_id']);

            $eloquentData->where('merchant_branches.merchant_id', $merchantStaff->merchant->id);


            whereBetween($eloquentData, 'merchant_branches.created_at', $request->created_at1, $request->created_at2);
            if ($request->branchId) {
                $eloquentData->where('merchant_branches.id', '=', $request->branchId);
            }


            if ($request->merchantId) {
                $eloquentData->where('merchant_branches.merchant_id', $request->merchantId);
            }

            if ($request->name) {
                orWhereByLang($eloquentData,'merchant_branches.name',$request->name);
            }


            if ($request->address) {
                orWhereByLang($eloquentData,'merchant_branches.address',$request->address);
            }

            if (is_array($request->area_id) && !empty($request->area_id) && !(count($request->area_id) == 1 && $request->area_id[0] == '0')) {
                $eloquentData->where('merchant_branches.area_id', 'IN', \App\Libs\AreasData::getAreasDown($request->area_id));
            }


            if ($request->active) {
                $status = ($request->active) ? 'active' : 'in-active';
                $eloquentData->where('merchants.status', $status);
            }


        $eloquentData->with('area');

        $rows = $eloquentData->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('There Are no Branches to display'));

        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),[$this->systemLang]),__('Branches to display'));
    }

    public function create(Request $request){
        $merchantStaff = Auth::user();

        $RequestData = $this->headerdata(['name_en','address_en','description_en','name_ar','address_ar','description_ar','latitude','longitude','area_id','status']);
        $validator = Validator::make($RequestData, [
            'name_en'           => 'required',
            'address_en'        => 'required',
            'description_en'    => 'required',
            'name_ar'           => 'required',
            'address_ar'        => 'required',
            'description_ar'    => 'required',
            'latitude'          => 'required',
            'longitude'         => 'required',
            'area_id'           => 'required|area_id',
            'status'            => 'required|in:active,in-active'
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $RequestData['merchant_staff_id'] = $merchantStaff->id;
        $RequestData['area_id'] = getLastNotEmptyItem($request->area_id);

        try{
            $branch = $merchantStaff->merchant->merchant_branch()->create($RequestData);
            return $this->respondCreated($this->Transformer->transform($branch,[$this->systemLang]),__('Branch has been successfully added'));
        } catch (QueryException $e){
            if($e->getCode() == '23000')
                return $this->setCode(106)->respondWithError(false,__('Duplicated Branch data'));
            return $this->respondWithError(false,__('Sorry Couldn\'t add Branch'));
        }

    }

    public function view($id){
        $merchantStaff = Auth::user();
        $row = MerchantBranch::where('merchant_id',$merchantStaff->merchant->id)->where('id',$id)
            ->with('area')->with('merchant')
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('Branch not found'));
        else
            return $this->respondSuccess($this->Transformer->transform($row->toArray(),[$this->systemLang]),__('Branch Details'));
    }

    public function edit($id,Request $request){
        $merchantStaff = Auth::user();
        $row = MerchantBranch::where('merchant_id',$merchantStaff->merchant->id)->where('id','=',$id)->first();
        if(!$row)
            return $this->setStatusCode(403)->respondWithError(false,__('Branch doesn\'t exist, Or you don\'t have permissions to edit it'));


        $RequestData = $this->headerdata(['name_en','address_en','description_en','name_ar','address_ar','description_ar','latitude','longitude','area_id','status']);
        $validator = Validator::make($RequestData, [
            'name_en'           => 'required',
            'address_en'        => 'required',
            'description_en'    => 'required',
            'name_ar'           => 'required',
            'address_ar'        => 'required',
            'description_ar'    => 'required',
            'latitude'          => 'required',
            'longitude'         => 'required',
            'area_id'           => 'required|area_id',
            'status'            => 'required|in:active,in-active'
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $RequestData['merchant_staff_id'] = $merchantStaff->id;
        $RequestData['area_id'] = getLastNotEmptyItem($request->area_id);

        try {
            $row->update($RequestData);
            $newrow = MerchantBranch::where('id',$row->id)-with('area')->first();
            return $this->respondSuccess($this->Transformer->transform($newrow->toArray(),[$this->systemLang]),__('Branch successfully updated'));
        } catch (QueryException $e){
            return $this->setCode(107)->respondWithError(false,__('Branch couldn\'t be updated'));
        }

    }

    public function delete($id){
        $merchantStaff = Auth::user();
        $row = MerchantBranch::where('merchant_id',$merchantStaff->merchant->id)->where('id',$id)->first();
        if(!$row)
            return $this->setCode(101)->respondWithError(false,__('Branch doesn\'t exist, Or you don\'t have permissions to delete it'));

        if(!$row->delete())
            return $this->setCode(107)->respondWithError(false,__('Branch couldn\'t be deleted'));

        return $this->respondSuccess(false,__('Branch successfully deleted'));

    }

}