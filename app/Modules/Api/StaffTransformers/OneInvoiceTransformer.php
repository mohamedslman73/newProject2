<?php

namespace App\Modules\Api\StaffTransformers;

use Illuminate\Support\Facades\Auth;

class OneInvoiceTransformer extends Transformer
{
    public function transform($item, $opt)
    {
        list($date, $time) = explode(' ', $item->dateTime);
        $info = array_column(array_map(function ($val, $key) use ($opt) {
            if (!is_array($val))
                return ['key' => $key, 'value' => (string)$val];
            else
                return ['key' => $key, 'value' => $val];
        }, $item->info, array_keys($item->info)), 'value', 'key');


        if (!isset($info['ar'])) {
            $info['ar'] = [];
        }
        if (!isset($info['en'])) {
            $info['en'] = [];
        }

        return [
            'transactionID' => $item->transactionId,
            'date' => $date,
            'time' => $time,
            //'ccTransactionId' => $time->ccTransactionId,
            'amount' => (string)$item->system_amount['amount'],// . ' ' . __('LE'),
            'totalAmount' => (string)$item->system_amount['total_amount'],// . ' ' . __('LE'),
            'serviceData' => [
                'merchantId' => Auth::user()->merchant()->id . '-' . Auth::id(),
                'serviceId' => $item->service_info['service_id'],
                'serviceProviderAr' => $item->service_info['provider_name_ar'],
                'serviceNameAr' => $item->service_info['service_name_ar'],
                'serviceProviderEn' => $item->service_info['provider_name_en'],
                'serviceNameEn' => $item->service_info['service_name_en'],
                'paidBy' => $item->payment_by['name'],
                'paidByLogo' => $item->payment_by['logo'],
                'serviceDescriptionAr' => $item->service_info['service_description_ar'],
                'serviceDescriptionEn' => $item->service_info['service_description_en'],
            ],
            'info' => $info,
            'param' => $item->param
        ];
    }

    public function ApiStaffTransform($item, $opt)
    {
//dd($item);
   //     list($date, $time) = explode(' ', $item['date_time']);
//        $info = array_column(array_map(function ($val, $key) use ($opt) {
//            if (!is_array($val))
//                return ['key' => $key, 'value' => (string)$val];
//            else
//                return ['key' => $key, 'value' => $val];
//        }, $item->info, array_keys($item->info)), 'value', 'key');


        if (!isset($info['ar'])) {
            $info['ar'] = [];
        }
        if (!isset($info['en'])) {
            $info['en'] = [];
        }

        return [
            'transactionID' => $item['transactionID'],
            'created At'=>$item['date_time'],
            'status' =>$item['invoice_status'],
            'amount' => (string)$item['amount'],// . ' ' . __('LE'),
            'totalAmount' => (string)$item['total_amount'],// . ' ' . __('LE'),
            'serviceData' => [
           //     'merchantId' => Auth::user()->merchant()->id . '-' . Auth::id(),
                'serviceId' => $item['service_id'],
                'serviceProviderAr' => $item['provider_name_ar'],
                'serviceNameAr' => $item['service_name_ar'],
                'serviceProviderEn' => $item['provider_name_en'],
                'serviceNameEn' => $item['service_name_en'],
               // 'paidBy' => $item->payment_by['name'],
                //'paidByLogo' => $item['logo'],
                'serviceDescriptionAr' => $item['service_description_ar'],
                'serviceDescriptionEn' => $item['service_description_en'],
            ],
           // 'info' => $info,
            //'param' => $item->param
        ];
    }


}