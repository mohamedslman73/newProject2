<?php






function getCategoryTreeSelect($parent_category_id = 0, $level = ' ', $category_id = 0) {

    $result =  \App\Models\ItemCategories::select(['id','name', 'parent_id'])->where(['status'=>'active','parent_id'=> $parent_category_id])
        ->get();
    $menu = '';
    if (!empty($result)) {
        foreach ($result as $row) {
            if (  $row -> id == $category_id)
                $select = 'selected="selected"';
            else
                $select = '';
            $menu .= '<option ' . $select . ' value="' . $row -> id . '" > ' .$level . ' - ' . $row -> name . '</option>';

            $check = \App\Models\ItemCategories::select(['id','name', 'parent_id'])
                ->where(['status'=>'active','parent_id'=> $row->parent_id])->get();

            if (!empty($check)) {

                $menu .= getCategoryTreeSelect($row -> id, $level . ' - '.$row->name  ,$category_id);
            }
        }
    }

    echo $menu;

}


function SelectCategory($parent_id = 0 , $parent_name = '')
{     $out = '';
    $category =  \App\Models\ItemCategories::select(['id','name', 'parent_id'])->where(['id'=> $parent_id])->first();
    if (!empty($category) &&  $category->parent_id != 0 ) {
        $sub = SelectCategory($category->parent_id , $category->name);
        $out .= '<option>'.$category->name.'</option>';
    }if(!empty($category) &&  $category->parent_id == 0  ) {
    $out .= '<option>'.$category->name.'</option>';
}


    echo $out;
}




function AllCategoriesWithItems($parent_id = 0)
{     $out = '';
    $categories =  \App\Models\ItemCategories::select(['id','name', 'parent_id'])->where(['parent_id'=> $parent_id])->get();

    if (!empty($categories)) {
        $out = '<table class="table table-hover"  ><tbody>';
        foreach ($categories as $key => $category) {
            $out .= '<tr>';
            $out .= '<td>'.$category->name.'</td>';
            $items =  $category->items;
            if(!empty($items)){
                $out .= '<td>';
                $categories[$key]['items'] = $items;
                foreach ($items as $row){
                    $out .= '<table class="table table-hover"><tbody><tr>';
                    $out .= '<th>المنتج</th>
                            <th>سعر التكلفه</th>
                            <th>الكميه</th>
                            <th>اجمالى التكلفه</th>
                            <th>السعر</th>
                            <th>اجمالى البيع</th>
                            <th>الحاله </th></tr>';
                    if($row->status == 'in-active')
                        $class = 'style="background-color: red"';
                    else
                        $class = '';
                    $out .= '<tr '.$class.' ><td>'.$row->name.'</td>';
                    $out .= '<td>'.$row->cost.'</td>';
                    $out .= '<td>'.$row->count.'</td>';
                    $out .= '<td>'.$row->cost * $row->count  .'</td>';
                    $out .= '<td>'.$row->price.'</td>';
                    $out .= '<td>'.$row->price * $row->count  .'</td>';
                    $out .= '<td>'.$row->status.'</td>';
                }
                $out .='</td>';
            }
            $categories[$key]['sub'] = AllCategoriesWithItems($category->id);
            if(!empty($categories[$key]['sub'])){

                $out .= '<td><tr>'.$categories[$key]['sub'].'</tr></td>';
            }
            $out .='</tr></tbody></table>';
            $out .= '</tr>';
        }
    }

    if($out != '')
        $out .='</tbody></table>';

    return $out;
}














function  exportXLS($title ,$heads, $exData,$callback){

    $return = '<table><thead><tr><th colspan="'.count($heads).'">'.$title.'</th></tr><tr>';

    foreach ($heads as $key => $value){
        $return.= '<th>'.$value.'</th>';
    }
    $return.= '</thead><tbody>';
    foreach ($exData as $key => $value){
        $return.= '<tr>';
        foreach ($callback as $k => $v){
            if(is_string($v))
                $return.= '<td>'.$value[$v].'</td>' ;
            else
                $return.= '<td>'.$v($value).'</td>';
        }
        $return.= '</tr>';
    }
    $return.= '</tbody></table>';


    \Excel::create($title, function($excel) use ($return) {
        $excel->sheet('Excel sheet', function($sheet) use ($return) {
            $sheet->loadView('system.export-to-excel')->with('return',$return);
        });

    })->export('xls');

}

function pda($ob)
{
    print_r($ob->toArray());
    die;
}

function pd($ob)
{
    print_r($ob);
    die;
}

function getLang(){
    return App::getLocale();
}

function getRealIP(){
    return env('HTTP_CF_CONNECTING_IP') ?? env('REMOTE_ADDR');
}

function databaseAmount($amount){
    $pos = strpos($amount,'.');
    if($pos === false){
        return $amount;
    }

    return substr($amount,0,$pos).substr($amount,$pos,3);
}

function distance($lat1, $lon1, $lat2, $lon2, $unit,$round) {

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return round(($miles * 1.609344),$round);
    } else if ($unit == "N") {
        return round(($miles * 0.8684),$round);
    } else { // defult kilometer
        return round(($miles * 1.609344),$round);
    }
}


function setError($data,$model_type,$model_id,$msg = null,$type = 'error'){
    $create = \App\Models\ErrorLog::create([
        'model_type'=> $model_type,
        'model_id'=> $model_id,
        'type'=> $type,
        'data'=> $data,
        'msg'=> $msg
    ]);

    if($create){
        return true;
    }else{
        return false;
    }

}


function amount($amount,$format = false){
    if($format){
        return number_format($amount,2).' '.__('LE');
    }
    return $amount.' '.__('LE');
}

function humanStr($value){
    return __(ucwords(str_replace('_', ' ', $value)));
}

// Arrays Helpers

function arrayGetOnly(array $array,$only){
    if(empty($array)){
        return [];
    }else{
        $newData = [];
        if(is_array($only)){
            foreach ($only as $key => $value) {
                if(isset($array[$value])){
                    $newData[$value] = $array[$value];
                }
            }
        }elseif(is_string($only)){
            if(isset($array[$only])){
                $newData[$only] = $array[$only];
            }
        }else{
            return [];
        }

        return $newData;
    }
}

// Arrays Helpers




function listLangCodes(){
    return [
        'ar'=> 'العربية',
        'en'=> 'English'
    ];
}

function iif($conditions,$true = null,$false = null){
    if($conditions){
        if(is_object($true) && ($true instanceof Closure)){
            return $true();
        }else{
            return $true;
        }
    }else{
        if(is_object($false) && ($false instanceof Closure)){
            return $false();
        }else{
            return $false;
        }
    }
}


function whereBetween( &$eloquent,$columnName,$form,$to){
    if(!empty($form) && empty($to)){
        $eloquent->whereRaw("$columnName >= ?",[$form]);
    }elseif(empty($form) && !empty($to)){
        $eloquent->whereRaw("$columnName <= ?",[$to]);
    }elseif(!empty($form) && !empty($to)){
        $eloquent->where(function($query) use($columnName,$form,$to) {
            $query->whereRaw("$columnName BETWEEN ? AND ?",[$form,$to]);
        });
    }
}

function orWhereByLang(&$eloquent,$columnName,$value,$operator = 'like'){
    $eloquent->where(function($query) use($columnName,$value,$operator){
        $count = 0;
        foreach (listLangCodes() as $key => $langName) {

            if($count == 0){
                if($operator == 'like'){
                    $query->where("$columnName".'_'."$key",'LIKE','%'.$value.'%');
                }else{
                    $query->where("$columnName".'_'."$key",$operator,$value);
                }
            }else{
                if($operator == 'like'){
                    $query->orWhere("$columnName".'_'."$key",'LIKE','%'.$value.'%');
                }else{
                    $query->orWhere("$columnName".'_'."$key",$operator,$value);
                }
            }
            $count++;
        }
    });
}

function imageResize($imagePath,$width,$height){
    $vImagePath = $imagePath;
    $imagePath = storage_path('app/public/'.$imagePath);

    if(File::exists($imagePath) && explode('/',File::mimeType($imagePath))[0] == 'image' ){
        $resizedFileName = File::dirname($imagePath).'/'.File::name($imagePath).'_'.$width.'X'.$height.'.'.File::extension($imagePath);

        if(!Storage::exists($resizedFileName)){
            Image::make($imagePath)
                ->resize($width,$height)
                ->save($resizedFileName);
        }

        return File::dirname($vImagePath).'/'.File::name($imagePath).'_'.$width.'X'.$height.'.'.File::extension($imagePath);

//        return $resizedFileName;
    }


    return false;
}


function image($imagePath,$width,$height){
    return imageResize($imagePath,$width,$height);
}




/*
 * @ $areaID : array or int
 */

function getLastNotEmptyItem($array){
    if(empty($array) || !is_array($array)){
        return false;
    }

    $last = end($array);
    if(empty($last)){
        $last = prev($array);
    }
    return $last;
}

function contactType($row){
    return __(ucfirst(str_replace('_',' ',$row->type)));
}


function contactValue($row){
    if($row->type == 'email'){
        return '<a href="mailto:'.$row->value.'">'.$row->value.'</a>';
    }else{
        return '<a href="tel:'.$row->value.'">'.$row->value.'</a>';
    }
}

function UniqueId(){
    return md5(str_random(20).uniqid().str_random(50).(time()*rand()));
}

function Base64PngQR($var,$size=false){
    $height = ((isset($size['0']))? $size['0']:'256');
    $width = ((isset($size['1']))? $size['1']:'256');
    $renderer = new \BaconQrCode\Renderer\Image\Png();
    $renderer->setHeight($height);
    $renderer->setWidth($width);
    $writer = new \BaconQrCode\Writer($renderer);
    return $writer->outputContent($var);
}


function setting($name,$returnAll = false){
    static $data;
    if($data == null){
        $getData = App\Models\Setting::get(['name','value'])->toArray();
        $data = array_column($getData,'value','name');
    }
    if($returnAll){
        return $data;
    }elseif(isset($data[$name])){
        $unserialize = @unserialize($data[$name]);
        if(is_array($unserialize)){
            return $unserialize;
        }
        return $data[$name];
    }

    return null;
}

function recursiveFind(array $array, $needle)
{
    $response = [];
    $iterator  = new RecursiveArrayIterator($array);
    $recursive = new RecursiveIteratorIterator(
        $iterator,
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($recursive as $key => $value) {
        if ($key === $needle) {
            $response[] = $value;
        }
    }
    return ((count($response)=='1')?$response:$response);
}

function response_to_object($array) {
    $obj = new stdClass;
    foreach($array as $k => $v) {
        if(strlen($k)) {
            if((is_array($v)) && count($v)) {
                $obj->{$k} = response_to_object($v); //RECURSION
            } elseif(($k == 'info') && (is_array($v))) {
                $obj->{$k} = implode("\n",$v);
            } else {
                $obj->{$k} = $v;
            }
        }
    }
    return $obj;
}

function calcDim($width,$height,$maxwidth,$maxheight) {
    if($width != $height){
        if($width > $height){
            $t_width = $maxwidth;
            $t_height = (($t_width * $height)/$width);
            //fix height
            if($t_height > $maxheight)
            {
                $t_height = $maxheight;
                $t_width = (($width * $t_height)/$height);
            }
        } else {
            $t_height = $maxheight;
            $t_width = (($width * $t_height)/$height);
            //fix width
            if($t_width > $maxwidth){
                $t_width = $maxwidth;
                $t_height = (($t_width * $height)/$width);
            }
        }
    } else
        $t_width = $t_height = min($maxheight,$maxwidth);
    return array('height'=>(int)$t_height,'width'=>(int)$t_width);
}

function PaymentParamName($param,$lang){
    $paramData = \App\Models\PaymentServiceAPIParameters::where('external_system_id','=',explode('_',$param)['1'])->first();
    return $paramData->{'name_'.$lang};
}