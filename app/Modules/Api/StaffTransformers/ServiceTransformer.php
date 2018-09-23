<?php

namespace App\Modules\Api\StaffTransformers;

use Illuminate\Support\Facades\Auth;

class ServiceTransformer extends Transformer
{
    public function transform($items,$opt)
    {
       return array_map([$this,'service_category'], $items);
    }

    function service_category($item){

        $service_category = [
            'id'=>$item['id'],
            'name_ar'=>$item['name_ar'],
            'name_en'=>$item['name_en'],
            'payment_service_providers'=>array_map([$this,'service_provider'],$item['payment_service_providers'])


        ];
        return $service_category;
    }

    function service_provider($item){

        $service_provider = [
            'id'=>$item['id'],
            'payment_service_provider_category_id'=>$item['payment_service_provider_category_id'],
            'name_ar'=>$item['name_ar'],
            'name_en'=>$item['name_en'],
            'payment_services'=>array_map([$this,'payment_services'],$item['payment_services'])


        ];
        return $service_provider;
    }

    function payment_services($item){

        $payment_services = [
            'id'=>$item['id'],
            'payment_service_provider_id'=>$item['payment_service_provider_id'],
            'name_ar'=>$item['name_ar'],
            'name_en'=>$item['name_en']



        ];
        return $payment_services;
    }

    function payment_services_api($item){

        $payment_services = [
            'id'=>$item['id'],
            'name'=>$item['provider_name'].'-'.$item['name'],




        ];
        return $payment_services;
    }


}