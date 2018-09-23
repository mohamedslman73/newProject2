<?php

namespace App\Modules\Api\Transformers\User;

use App\Modules\Api\Transformers\Transformer;

class ProductTransformer extends Transformer
{
    public function transform($item, $opt)
    {
        return [
            'productId' => $item['id'],
            'name' => self::trans($item, 'name', $opt),
            'description' => self::trans($item, 'description', $opt),
            'price' => $item['price'],
            'images' => ((isset($item['uploadmodel']) && count($item['uploadmodel'])) ? (new UploadTransformer())->transformCollection($item['uploadmodel'], [$opt]) : null),
        ];
    }

}