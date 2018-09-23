<?php
namespace App\Modules\Api\Merchant;

use App\Models\News;
use App\Models\NewsCategory;
use App\Modules\Api\Transformers\NewsTransformer;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsApiController extends MerchantApiController {
    protected $Transformer;
    public function __construct(NewsTransformer $newsTransformer)
    {
        parent::__construct();
        $this->Transformer = $newsTransformer;
    }

    public function getalldata(){
        $LatestNews = News::viewData($this->systemLang,[])
            ->Active()
            ->where('news_categories.type','=','merchant')
            ->orderBy('news.created_at','desc');
        $rows = $LatestNews->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('There Are no news to display'));
        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),[$this->systemLang]),__('News Data'));
    }

    public function view($id){
        $row = News::viewData($this->systemLang,[])
            ->Active()
            ->where('news_categories.type','=','merchant')
            ->where('news.id','=',$id)
            ->first();
        if(!$row) {
            return $this->respondNotFound(false,__('Article not found'));
        }
        return $this->respondSuccess($this->Transformer->transform($row->toArray(),$this->systemLang),__('Article details'));
    }

    public function view_category($id){
        $CategoryNews = News::viewData($this->systemLang,[])
            ->Active()
            ->where('news_categories.type','=','merchant')
            ->where('news_categories.id','=',$id)
            ->orderBy('news.created_at','desc');
        $rows = $CategoryNews->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('News Category doesn\'t have any news'));
        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),$this->systemLang),__('News from category'));
    }

}