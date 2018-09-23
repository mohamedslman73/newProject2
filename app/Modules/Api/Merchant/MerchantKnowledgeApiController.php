<?php
namespace App\Modules\Api\Merchant;

use App\Models\MerchantKnowledge;
use App\Models\MerchantProduct;
use App\Modules\Api\Transformers\KnowledgeTransformer;
use Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MerchantKnowledgeApiController extends MerchantApiController
{
    protected $Transformer;
    public function __construct(KnowledgeTransformer $knowledgeTransformer)
    {
        parent::__construct();
        $this->Transformer = $knowledgeTransformer;
    }


    public function getalldata(Request $request){
        $merchantStaff = Auth::user();

        $eloquentData = MerchantKnowledge::select([
            'id',
            "name_{$this->systemLang} as name",
            "content_{$this->systemLang} as content",
            "merchant_staff_id"
        ]);

        if ($request->withTrashed) {
            $eloquentData->onlyTrashed();
        }

        /*
         * Start handling filter
         */

        whereBetween($eloquentData, 'created_at', $request->created_at1, $request->created_at2);

        if ($request->knowledgeId) {
            $eloquentData->where('id', '=', $request->knowledgeId);
        }

        if ($request->name) {
            orWhereByLang($eloquentData, 'name', $request->name);
        }

        if ($request->Content) {
            orWhereByLang($eloquentData, 'content', $request->Content);
        }


        $rows = $eloquentData->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('No knowledge base info to display'));


        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),[$this->systemLang]),__('Knowledge details'));

    }

    public function create(Request $request){
        $merchantStaff = Auth::user();

        $RequestData = $this->headerdata(['name_en','content_en','name_ar','content_ar']);

        $validator = Validator::make($RequestData, [
            'name_ar'          =>  'required',
            'name_en'          =>  'required',
            'content_ar'       =>  'required',
            'content_en'       =>  'required',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }


        $theRequest = $RequestData;
        $theRequest['merchant_staff_id'] = $merchantStaff->id;

        try {
            $row = MerchantKnowledge::create($theRequest);
            $row->addToIndex();
            return $this->respondCreated($this->Transformer->transform($row->toArray(),$this->systemLang),__('Knowledge data successfuly added'));
        } catch (QueryException $e){
            if($e->getCode() == '23000')
                return $this->setStatusCode(200)->setCode(106)->respondWithoutError(false,__('Duplicated Knowledge'));
            return $this->setStatusCode(403)->respondWithoutError(false,__('Couldn\'t add Knowledge data'));
        }

    }

    public function view($id){
        $merchantStaff = Auth::user();
        $row = MerchantKnowledge::where('id',$id)->first();
        if(!$row)
            return $this->respondNotFound(false,__('This Knowledge data doesn\'t exist'));

        return $this->respondSuccess($this->Transformer->transform($row->toArray(),$this->systemLang),__('Knowledge data details'));
    }

    public function edit_row($id,Request $request){
        $merchantStaff = Auth::user();
        $row = MerchantKnowledge::where('id',$id)->first();
        if(!$row)
            return $this->setStatusCode(403)->respondWithError(false,__('Knowledge data doesn\'t exist'));

        $RequestData = $this->headerdata(['name_en','content_en','name_ar','content_ar']);

        $validator = Validator::make($RequestData, [
            'name_ar'          =>  'required',
            'name_en'          =>  'required',
            'content_ar'       =>  'required',
            'content_en'       =>  'required',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $theRequest = $RequestData;
        $theRequest['merchant_staff_id'] = $merchantStaff->id;

        try {
            $row->update($theRequest);
            $row->reindex();
            return $this->respondSuccess($this->Transformer->transform($row->toArray(),$this->systemLang),__('Knowledge data details'));
        } catch (QueryException $e){
            if($e->getCode() == '23000')
                return $this->setStatusCode(200)->setCode(106)->respondWithError(false,__('Duplicated Knowledge data'));
            return $this->setStatusCode(403)->respondWithError(false,__('Knowledge data couldn\'t be updated'));
        }

    }

    public function delete($id){
        $merchantStaff = Auth::user();
        $row = MerchantKnowledge::where('id',$id)->first();
        if(!$row)
            return $this->setStatusCode(403)->respondWithError(false,__('Knowledge data doesn\'t exist'));

        if(!$row->delete())
            return $this->setStatusCode(403)->respondWithError(false,__('Knowledge data couldn\'t be deleted'));

        return $this->respondSuccess(false,__('Knowledge data successfully deleted'));
    }


    public function search(){
        $search = $this->headerdata('search');

        $validator = Validator::make($search, [
            'search'          =>  'required',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }
        $result = MerchantKnowledge::complexSearch(array(
            'type'=> 'merchant_knowledge',
            'body' => array(
                'query' => array(
                    'match' => array(
                        '_all'=> [
                            'query' => $search['search'] ,
                            'fuzziness' => "2",
                            "operator" => "OR"
                        ]
                    )
                ),
            )
        ));

        if(!$result)
            return $this->respondNotFound(false,__('No knowledge base info to display'));
        return $this->respondSuccess($this->Transformer->transformCollection($result->toArray(),$this->systemLang),__('Knowledge data details'));
    }

}