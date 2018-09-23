<?php

namespace App\Modules\Api\Transformers\User;

use App\Modules\Api\Transformers\Transformer;

class TransactionTransformer extends Transformer
{
    public function transform($item, $opt)
    {
        return [
            'transactionID' => $item['id'],
            'amount' => $item['amount'],
            'payVia' => self::payVia($item),
            'transactionType' => self::transactionType($item),
            'isPaid' => (($item['status'] == 'paid') ? true : false),
            'order' => self::transactionOrder($item, $opt),
        ];
    }

    public static function transactionOrder($item, $opt)
    {
        if (isset($item['model'])) {
            if (count($item['model']))
                return (new BasicOrderTransformer())->transform($item['model'], $opt);
            else
                return null;
        } else
            return null;
    }

    public static function transactionType($item)
    {
        return ((isset($item['pay_type'])) ? $item['pay_type'] : null);
    }
}