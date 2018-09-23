<?php

namespace App\Modules\Api\StaffTransformers;



class DepositTransformer extends Transformer
{
    public function transform($item, $opt)
    {

        return [
            'id' => $item['id'],
          'transaction_id'=>$item['transaction_id'],
          'bank'=>$item['bank']['name_ar'],
          'creatable'=>$item['creatable']['firstname'] . $item['creatable']['lastname'],
          'amount'=>amount($item['amount']),
          'status'=>$item['status'],
          'date'=>$item['created_at'],

        ];
    }
}