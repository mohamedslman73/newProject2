<?php

namespace App\Modules\Api\Transformers\User;

use App\Modules\Api\Transformers\User\ProductCategoryTransformer;
use App\Modules\Api\Transformers\Transformer;

class MerchantCategoriesTransformer extends Transformer
{
    public function transform($item, $opt)
    {
        return [
            'categoryID' => $item['id'],
            'name' => self::trans($item, 'name', $opt),
            'description' => self::trans($item, 'description', $opt),
            'icon' => self::Link($item, 'icon'),
            'isActive' => self::status($item),
            'merchants' => (isset($item['merchants_count']) ? $item['merchants_count']
                : ((isset($item['merchants'])) ? (new MerchantTransformer())->transformCollection($item['merchants'], [$opt]) : null)
            ),
        ];
    }
}