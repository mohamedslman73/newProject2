<?php

namespace App\Modules\Api\StaffTransformers;

class StaffInfoTransformer extends Transformer
{
    public function transform($item, $opt)
    {
        return [
            'userId' => $item['id'],
            'firstname' => ((isset($item['firstname']) ? $item['firstname'] : null)),
            'lastname' => ((isset($item['lastname']) ? $item['lastname'] : null)),
            'wallet_id' => $item['payment_wallet']['id'],
            'email' => ((isset($item['email']) ? $item['email'] : null)),
            'balance' => self::Balance($item),
            'merchant_name' => self::merchant_Name($item, $opt),
            'isActive' => self::status($item),
        ];
    }


    private static function Balance($item)
    {
        if (array_key_exists('payment_wallet', $item)) {
            return $item['payment_wallet']['balance'];

        }
        return "0";
    }

    private static function merchant_Name($item, $lang)
    {
        if (array_key_exists('staff_group', $item)) {
            if (array_key_exists('merchant', $item['staff_group'])) {
                return $item['staff_group']['merchant']['name_' . ((is_array($lang)) ? $lang[0] : $lang)];
            }
        }
        return '';
    }
}