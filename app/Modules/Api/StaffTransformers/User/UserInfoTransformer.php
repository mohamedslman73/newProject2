<?php

namespace App\Modules\Api\Transformers\User;

use App\Modules\Api\Transformers\Transformer;

class UserInfoTransformer extends Transformer
{
    public function transform($item, $opt)
    {
        return [
            'userId' => $item['id'],
            'fullName' => self::fullName($item),
            'firstName' => ((isset($item['firstname']) ? $item['firstname'] : null)),
            'middleName' => ((isset($item['middlename']) ? $item['middlename'] : null)),
            'lastName' => ((isset($item['lastname']) ? $item['lastname'] : null)),
            'gender' => ((isset($item['gender'])) ? $item['gender'] : ''),
            'birthdate' => ((isset($item['birthdate'])) ? $item['birthdate'] : ''),
            'image' => self::Link($item, 'image'),
            'nationalId' => ((isset($item['national_id']) ? $item['national_id'] : null)),
            'nationalIdImage' => self::Link($item, 'national_id_image'),
            'email' => ((isset($item['email']) ? $item['email'] : null)),
            'mobile' => ((isset($item['mobile']) ? $item['mobile'] : null)),
            'address' => ((isset($item['address']) ? $item['address'] : null)),
            'balance' => ((isset($item['e_commerce_wallet']) ? self::Round($item['e_commerce_wallet']['balance']) : "")),
            //'balance'                   => ((isset($item['wallet'])?$item['wallet']['balance'].' '.__('LE'):null)),

            'isActive' => self::status($item),
        ];
    }


}