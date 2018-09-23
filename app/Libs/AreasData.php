<?php

namespace App\Libs;

use App\Models\AreaType;
use App\Models\Area;

class AreasData{
    public static function getAllTypes(){
        return AreaType::orderBy('id','ASC')->get();
    }

    public static function getNextAreas($id,$lang){
        if(!$id){
            return ['type'=> false,'areas'=> false];
        }

        $areaData = Area::where('parent_id',$id)->select(['*',\DB::raw("name_$lang as name")])->get()->toArray();
        if(!empty($areaData)){
            return [
                'old_type'=> AreaType::select(['*',\DB::raw("name_$lang as name")])->find($id),
                'type'=> AreaType::select(['*',\DB::raw("name_$lang as name")])->find($areaData[0]['area_type_id']),
                'areas'=> $areaData,
            ];
        }else{
            return ['type'=> false,'areas'=> false];
        }
    }


    public static function getAreasDown($areaID,$firstRequest = true){
        static $arrayOfData;
        if($firstRequest == true){
            $arrayOfData = null;
        }

        if($arrayOfData === null){
            if(is_array($areaID)){
                $areaID = getLastNotEmptyItem($areaID);
                if(!$areaID){
                    return [];
                }
            }
            $arrayOfData = [$areaID];
        }

        $result = Area::where('parent_id',$areaID)->get(['id']);
        if(!$result->isEmpty()){
            foreach ($result as $value){
                $arrayOfData[] = $value->id;
                self::getAreasDown($value->id,false);
            }
        }

        return $arrayOfData;
    }

    public static function getAreasUp($areaID,$areaNames = false,$lang = 'en',$firstRequest = true){

        static $arrayOfData;
        if($firstRequest == true){
            $arrayOfData = [];
        }

        $area = Area::find($areaID);
        if($area ){
            if(!$areaNames){
                $arrayOfData[$area->area_type_id] = $area->id;
            }else{
                $arrayOfData[$area->area_type_id] = $area->{'name_'.$lang};
            }

            self::getAreasUp($area->parent_id,$areaNames,$lang,false);
        }

        return array_reverse($arrayOfData,true);

    }


    public static function getAreaTypesUp($areaTypeID,$lang = 'en',$firstRequest = true){
        static $arrayOfData;
        if($firstRequest == true){
            $arrayOfData = [];
        }

        if(empty($arrayOfData)){
            $data = AreaType::find($areaTypeID);
            $arrayOfData[$data->id] = $data->{'name_'.$lang};
            return self::getAreaTypesUp($areaTypeID,$lang,false);
        }else{
            $data = AreaType::where('id','<',$areaTypeID)->orderByDesc('id')->first();
            if($data){
                $arrayOfData[$data->id] = $data->{'name_'.$lang};
                return self::getAreaTypesUp($data->id,$lang,false);
            }
        }

        return array_reverse($arrayOfData,true);

    }





}