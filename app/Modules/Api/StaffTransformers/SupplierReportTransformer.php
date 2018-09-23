<?php

namespace App\Modules\Api\StaffTransformers;


class SupplierReportTransformer extends Transformer
{
    public function transform($item, $opt=null)
    {
      //  dd($item);
     //return $item;
        $return = [
          'id' => $item['id'],
          'client name' => $item['name'],
            'init_credit'=>$item['init_credit'],

        ];

        if (!empty($item['supplier_order'])){
            $return['total_supplier_order'] = $item['supplier_order'][0]['sum_total_order'];
        }else{
            $return['total_supplier_order'] = 0;
        }
        if (!empty($item['supplier_order_back'])){
            $return['total_supplier_order_back'] = $item['supplier_order_back'][0]['sum_total_supplier_order_back'];
        }else{
            $return['total_supplier_order_back'] = 0;
        }
        if (!empty($item['supplier_expence'])){
            $return['total_supplier_expence'] = $item['supplier_expence'][0]['sum_total_supplier_expence'];
        }else{
            $return['total_supplier_expence'] = 0;
        }



        $return['difference'] = ($return['total_supplier_order'] + $item['init_credit']) - ( $return['total_supplier_expence'] +$return['total_supplier_order_back'] );
      //  $difference = ($sum_order + $data->init_credit ) - ($sum_expence + $sum_order_back);

        if ($return['difference'] > 0){
            $return ['difference']= $return ['difference'].' For him';
            //else on him
        }else{
            $return ['difference']= $return ['difference'].' On him';
        }
        return $return;
    }





}