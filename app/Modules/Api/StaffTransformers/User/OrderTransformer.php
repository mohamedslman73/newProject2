<?php

namespace App\Modules\Api\Transformers\User;

use App\Modules\Api\Transformers\Transformer;

class OrderTransformer extends Transformer
{
    public function transform($item, $opt)
    {
        return [
            'orderId' => $item['id'],
            'total' => $item['total'],
            'isPaid' => self::isPaid($item),
            'branchName' => ((isset($item['branch_name'])) ? $item['branch_name'] : self::trans($item['merchant_branch'], 'name', $opt)),
            'branchId' => $item['merchant_branch_id'],
            'orderItems' => ((isset($item['orderitems'])) ? count($item['orderitems']) : null),
            'items' => ((isset($item['orderitems'])) ? (new OrderitemTransformer())->transformCollection($item['orderitems'], [$opt]) : null),
            'transactions' => ((isset($item['trans'])) ? (new TransactionTransformer())->transformCollection($item['trans'], [$opt]) : null),
        ];
    }
}