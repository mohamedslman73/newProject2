<?php

namespace App\Modules\Api\Transformers\User;

use App\Modules\Api\Transformers\Transformer;

class ProductCategoryTransformer extends Transformer
{
    public function transform($item, $opt)
    {
        return [
            'productCategoryId' => $item['id'],
            'name' => self::trans($item, 'name', $opt),
            'description' => self::trans($item, 'description', $opt),
            'isActive' => self::status($item),
            'products' => isset($item['product']) ? (new ProductTransformer())->transformCollection($item['product'], [$opt]) : __('No products available'),
        ];
    }
}