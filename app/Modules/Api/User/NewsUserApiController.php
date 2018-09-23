<?php
namespace App\Modules\Api\User;

use App\Models\News;
use App\Modules\Api\Transformers\NewsTransformer;
use Auth;

class NewsUserApiController extends UserApiController {
    protected $Transformer;
    public function __construct(NewsTransformer $newsTransformer)
    {
        parent::__construct();
        $this->Transformer = $newsTransformer;
    }

    public function getalldata(){
        $LatestNews = News::viewData($this->systemLang,[])
            ->where('news.status','=','active')
            ->where('news_categories.type','=','user')
            ->orderBy('news.created_at','desc');
        $rows = $LatestNews->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('There Are no news to display'));
        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),$this->systemLang),__('News Data'));
    }

    public function view($id){
        $row = News::viewData($this->systemLang,[])
            ->where('news.status','=','active')
            ->where('news_categories.type','=','user')
            ->where('news.id','=',$id)
            ->first();
        if(!$row) {
            return $this->respondNotFound(false,__('Article not found'));
        }
        return $this->respondSuccess($this->Transformer->transform($row->toArray(),$this->systemLang),__('Article details'));
    }

    public function view_category($id){
        $CategoryNews = News::viewData($this->systemLang,[])
            ->where('news.status','=','active')
            ->where('news_categories.type','=','user')
            ->where('news_categories.id','=',$id)
            ->orderBy('news.created_at','desc');
        $rows = $CategoryNews->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('News Category doesn\'t have any news'));
        return $this->respondSuccess($this->Transformer->transformCollection($CategoryNews,$this->systemLang),__('News from category'));
    }

}