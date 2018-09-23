<?php

namespace App\Modules\Api\StaffTransformers\User;

use App\Modules\Api\StaffTransformers\Transformer;

class MerchantTransformer extends Transformer
{
    public function transform($item, $opt)
    { 
        return [
            'merchantId' => $item['id'],
            'name' => self::trans($item, 'name', $opt),
            'logo' => self::Link($item, 'logo'),
            'description' => self::trans($item, 'description', $opt),
            'areaId' => self::areaId($item, $opt),
            'areaName' => self::areaName($item, $opt),
            'branchesCount' => ((isset($item['merchant_branch_count'])) ? $item['merchant_branch_count'] : null),
            'branches' => ((isset($item['merchant_branch']) && count($item['merchant_branch'])) ? (new StoreTransformer())->transformCollection($item['merchant_branch'], [$opt]) : null),
            'categories' => ((isset($item['merchant_product_categories'])) ? (new ProductCategoryTransformer())->transformCollection($item['merchant_product_categories'], [$opt]) : null),
            'isActive' => self::status($item),
        ];
    }

    public function merchantApiTransform($item, $opt)
    { //print_r($item);die;
        return [
            'merchantId' => $item['id'],
            'name' => self::trans($item, 'name', $opt),
            'logo' => self::Link($item, 'logo'),
            'description' => self::trans($item, 'description', $opt),
            'address' => $item['address'],
            'areaName' => self::areaName($item, $opt),
            'category' => $item['merchant_category']['name_' . $opt],
            'isActive' => self::status($item),
            'balance' => $item['payment_wallet']['balance'],
            'payment_wallet_id' => $item['payment_wallet']['id'],
            'staff_name' => $item['staff']['firstname'] . ' ' . $item['staff']['lastname'],
            'staff_mobile' => $item['staff']['mobile'],
        ];
    }

    private static function areaName($item, $opt)
    {
        if (isset($item['area'])) {
            if (isset($item['area']['name']))
                return $item['area']['name'];
            else
                return self::trans($item['area'], 'name', $opt);
        } else
            return null;
    }

    private static function areaId($item, $opt)
    {
        if (isset($item['area'])) {
            if (isset($item['area']['id']))
                return $item['area']['id'];
            else
                return null;
        } else
            return null;
    }

    function merchantTotalConsumed($item){

        return [
            'id'=>$item->id,
            'name'=>$item->name,
            'total_consumed'=>$item->total_paid
        ];
    }


}