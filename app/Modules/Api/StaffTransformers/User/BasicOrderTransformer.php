<?php

namespace App\Modules\Api\Transformers\User;

use App\Modules\Api\Transformers\Transformer;

class BasicOrderTransformer extends Transformer
{
    public function transform($item, $opt)
    {
        return [
            'orderId' => $item['id'],
            'total' => $item['total'],
            'isPaid' => self::isPaid($item),
            'branchName' => ((isset($item['branch_name'])) ? $item['branch_name'] : (($opt == 'ar') ? $item['merchant_branch']['name_ar'] : $item['merchant_branch']['name_en'])),
            'branchId' => $item['merchant_branch_id'],
            'merchantName' => self::merchantName($item, $opt),
            'merchantDescription' => self::merchantDescription($item, $opt),
        ];
    }


    private static function merchantName($item, $opt)
    {
        if (isset($item['merchant_branch']['merchant'])) {
            if (isset($item['merchant_branch']['merchant']['name']))
                return $item['merchant_branch']['merchant']['name'];
            else
                return $item['merchant_branch']['merchant'][self::trans('name', $opt)];
        } else
            return null;
    }

    private static function merchantDescription($item, $opt)
    {
        if (isset($item['merchant_branch']['merchant'])) {
            if (isset($item['merchant_branch']['merchant']['description']))
                return $item['merchant_branch']['merchant']['description'];
            else
                return $item['merchant_branch']['merchant'][self::trans('description', $opt)];
        } else
            return null;
    }


}