<?php

namespace App\Modules\Api\StaffTransformers;


class ClientReportTransformer extends Transformer
{
    public function transform($item, $opt=null)
    {
      //  dd($item);
//      return $item;
        $return = [
          'id' => $item['id'],
          'client name' => $item['name'],
            'init_credit'=>$item['init_credit'],

        ];

        if (!empty($item['client_order'])){
            $return['total_client_order'] = $item['client_order'][0]['sum_total_order'];
        }else{
            $return['total_client_order'] = 0;
        }
        if (!empty($item['client_order_back'])){
            $return['total_client_order_back'] = $item['client_order_back'][0]['sum_total_client_order_back'];
        }else{
            $return['total_client_order_back'] = 0;
        }
        if (!empty($item['client_revenue'])){
            $return['total_client_revenue'] = $item['client_revenue'][0]['sum_total_client_revenue'];
        }else{
            $return['total_client_revenue'] = 0;
        }



        $return['difference'] = ($return['total_client_order'] + $item['init_credit']) - ( $return['total_client_revenue'] +$return['total_client_order_back'] );

        if ($return['difference'] > 0){
            $return ['difference']= $return ['difference'].' On him';
            //else on him
        }else{
            $return ['difference']= $return ['difference'].' From him';
        }
        return $return;
    }





}