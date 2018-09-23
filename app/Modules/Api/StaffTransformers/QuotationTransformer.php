<?php

namespace App\Modules\Api\StaffTransformers;


class QuotationTransformer extends Transformer
{
    public function transform($item, $opt=null)
    {
       // dd($item);
        //'id','client_id','total_price','status','staff_id','items','cleaners','created_at'
        return $item;
//       return [
//           'id' =>    $item['id'],
//           'total_price' =>$item['total_price'],
//           'created_at' =>$item['created_at']
//       ];
    }

}