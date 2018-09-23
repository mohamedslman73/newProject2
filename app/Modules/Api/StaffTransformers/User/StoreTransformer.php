<?php

namespace App\Modules\Api\Transformers\User;

use App\Modules\Api\Transformers\User\ProductCategoryTransformer;
use App\Modules\Api\Transformers\Transformer;

class StoreTransformer extends Transformer
{
    public function transform($item, $opt)
    {
        return [
            'branchId' => $item['id'],
            'name' => self::trans($item, 'name', $opt),
            'address' => self::trans($item, 'address', $opt),
            //'LatLng'                    => $item['latitude'].','.$item['longitude'],
            'merchantName' => ((isset($item['merchant_name'])) ? $item['merchant_name'] : self::trans($item['merchant'], 'name', $opt)),
            'merchantId' => ((isset($item['merchant_id'])) ? $item['merchant_id'] : ((isset($item['merchant']['id'])) ? $item['merchant']['id'] : null)),
            'isActive' => (($item['status'] == 'active') ? true : false),
            'logo' => getenv('APP_URL') . '/' . ((isset($item['logo'])) ? $item['logo']
                    : ((isset($item['merchant'])) ? $item['merchant']['logo'] : null)),
            'distance' => (isset($item['distance'])) ? ($item['distance'] < 1000) ? round($item['distance'] * 1000, 2) . ' Meter' : round($item['distance'], 2) . ' Km' : null,
            'categories' => (isset($item['categories']) ? (new ProductCategoryTransformer())->transformCollection($item['categories'], [$opt])
                : (isset($item['categories_count']) ? $item['categories_count'] : null)),
        ];
    }
}