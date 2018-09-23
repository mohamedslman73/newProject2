<?php

namespace App\Modules\Api\Transformers\User;

use App\Modules\Api\Transformers\Transformer;

class UploadTransformer extends Transformer
{
    public function transform($item, $opt)
    {
        return [
            'title' => $item['title'],
            'image' => self::Link($item, 'path'),
            'isDefault' => (($item['is_default'] == 'yes') ? true : false),
        ];
    }

}