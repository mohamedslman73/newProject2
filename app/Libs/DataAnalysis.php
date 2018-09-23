<?php

namespace App\Libs;

use App\Models\AreaType;
use App\Models\Area;
use App\Models\User;
use App\Models\UserAction;

class DataAnalysis{

    public static function users(array $Data){
        $listData = ['created_at1','created_at2','birthdate1','birthdate2','is_parent','status','merchant_category_id'];
        foreach ($listData as $key=>$value){
            if(!isset($Data[$value])){
                $Data[$value] = null;
            }
        }

        // select(['user_id',\DB::raw('COUNT(merchants.merchant_category_id) as `count`')])
        $eloquentData = UserAction::join('users','users.id','=','user_actions.user_id');

        whereBetween($eloquentData,'users.created_at',$Data['created_at1'],$Data['created_at2']);
        whereBetween($eloquentData,'users.birthdate',$Data['birthdate1'],$Data['birthdate2']);

        if($Data['is_parent'] == 1){
            $eloquentData->whereNull('users.parent_id');
        }elseif($Data['is_parent'] == 2){
            $eloquentData->whereNotNull('users.parent_id');
        }


        /*
         * Category Filter
         */

        $merchantCategoryArray = [];
        if($Data['merchant_category_id']){
            $eloquentDataCategory = $eloquentData;

            $eloquentDataCategory->select(['users.id','user_actions.type',\DB::raw('COUNT(*) as `count`')]);
            // JOIN
            $eloquentDataCategory->join('merchant_products','merchant_products.id','=','user_actions.model_id');
            $eloquentDataCategory->join('merchants','merchant_products.merchant_id','=','merchants.id');

            // WHERE
            $eloquentDataCategory->where('user_actions.model_type','App\\Models\\MerchantProducts');
            $eloquentDataCategory->where('merchants.merchant_category_id',$Data['merchant_category_id']);

            // GROUP BY
            $eloquentDataCategory->groupBy(['user_actions.user_id','user_actions.type']);


            $merchantCategory = $eloquentDataCategory->get();
            if($merchantCategory->isNotEmpty()){
                $merchantCategorySum   = $merchantCategory->sum('count');
                $merchantCategoryCount = $merchantCategory->count();
                $merchantCategoryAvg   = @$merchantCategorySum/$merchantCategoryCount;
                if(isset($Data['interest']) && $Data['interest'] != 'all'){

                    $merchantCategory->map(function($data){
                       if($data->type == 'click'){
                           $data->count*= 2;
                       }
                       return $data;
                    });

                    switch ($Data['interest']){
                        case 'high':
                            $merchantCategory = $merchantCategory->reject(function($data) use($merchantCategoryAvg){
                                if($data->count < $merchantCategoryAvg){
                                    return true;
                                }else{
                                    return false;
                                }
                            });
                            break;

                        case 'low':
                            $merchantCategory = $merchantCategory->reject(function($data) use($merchantCategoryAvg){
                                if($data->count >= $merchantCategoryAvg){
                                    return true;
                                }else{
                                    return false;
                                }
                            });
                            break;
                    }
                }

                $merchantCategoryArray = array_unique(array_column($merchantCategoryArray->toArray(),'id'));
            }else{
                return [];
            }
        }


        /*
         * Merchant Filter
         */
        $merchantArray = [];
        if($Data['merchant_id']){
            $eloquentDataMerchant = $eloquentData;
            $eloquentDataMerchant->select(['users.id','user_actions.type',\DB::raw('COUNT(*) as `count`')]);

            // JOIN
            $eloquentDataMerchant->join('merchant_products','merchant_products.id','=','user_actions.model_id');
            $eloquentDataMerchant->join('merchants','merchant_products.merchant_id','=','merchants.id');

            // WHERE
            $eloquentDataMerchant->where('user_actions.model_type','App\\Models\\MerchantProducts');
            $eloquentDataMerchant->where('merchants.id',$Data['merchant_id']);

            // GROUP BY
            $eloquentDataMerchant->groupBy(['user_actions.user_id','user_actions.type']);

            $merchant = $eloquentDataMerchant->get();

            if($merchant->isNotEmpty()){

                $merchantSum   = $merchant->sum('count');
                $merchantCount = $merchant->count();
                $merchantAvg   = @$merchantSum/$merchantCount;
                $Data['interest'] = 'high';
                if(isset($Data['interest']) && $Data['interest'] != 'all'){

                    $merchant->map(function($data){
                        if($data->type == 'click'){
                            $data->count*= 2;
                        }
                        return $data;
                    });

                    switch ($Data['interest']){
                        case 'high':
                            $merchant = $merchant->reject(function($data) use($merchantAvg){
                                if($data->count < $merchantAvg){
                                    return true;
                                }else{
                                    return false;
                                }
                            });

                            break;

                        case 'low':
                            $merchant = $merchant->reject(function($data) use($merchantAvg){
                                if($data->count >= $merchantAvg){
                                    return true;
                                }else{
                                    return false;
                                }
                            });
                            break;
                    }
                }

                $merchantArray = array_unique(array_column($merchant->toArray(),'id'));
            }else{
                return [];
            }
        }

        // @TODO AREA FILTER

        return array_intersect($merchantCategoryArray,$merchantArray);

    }


}