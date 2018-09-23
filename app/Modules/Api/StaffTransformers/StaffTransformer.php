<?php

namespace App\Modules\Api\StaffTransformers;

class StaffTransformer extends Transformer
{
    public function transform($item, $opt)
    {
        return [
            'staffId' => $item['id'],
            'staffName' => self::fullName($item),
            'isActive' => self::status($item),
            'type' => 'systemStaff'
        ];
    }


    public function staffWallet($item)
    {
        // print_r($item);die;
        return [
            'id' => $item['id'],
            'ballance' => $item['payment_wallet']['seller_balance']
        ];
    }

    public function subStaffWithMerchants($item)
    {

        return [
            'id' => $item['id'],
            'firstname' => $item['firstname'],
            'lastname' => $item['lastname'],
            'mobile' => $item['mobile'],
            'wallet_id' => $item['payment_wallet']['id'],
            'balance' => $item['payment_wallet']['balance'],
            'count_merchant' => count($item['merchant']),


        ];

        //   return array_map([$this, 'subStaffWithMerchantsCallBack'], $items);

    }

    function subStaffWithMerchantsCallBack($item)
    {

        return [
            'id' => $item['id'],
            'firstname' => $item['firstname'],
            'lastname' => $item['lastname'],
            'mobile' => $item['mobile'],
            'wallet_id' => $item['payment_wallet']['id'],
            'balance' => $item['payment_wallet']['balance'],
            'count_merchant' => count($item['merchant']),


        ];

    }

    function staffWithMerchants($item,$opt)
    {

        return [
            'id' => $item['id'],
            'firstname' => $item['firstname'],
            'lastname' => $item['lastname'],
            'mobile' => $item['mobile'],
            'wallet_id' => $item['payment_wallet']['id'],
            'balance' => $item['payment_wallet']['balance'],
            'count_merchant' => count($item['merchant']),
            'merchants' => $this->transformCollection($item['merchant'],[$opt],'staffWithMerchantsCallBack'),


        ];

    }


    function staffWithMerchantsCallBack($item)
    {

        return [
            'id' => $item['id'],
            'name' => $item['name_ar'],
            'wallet_id' => $item['payment_wallet']['id'],
            'balance' => $item['payment_wallet']['balance'],
            'mobile' => $item['staff']['mobile'],


        ];

    }

    function staffTotalConsumed($item){

        return [
            'id'=>$item->id,
            'name'=>$item->firstname.' '.$item->lastname,
            'total_consumed'=>$item->total_consumed
        ];
    }

   

}