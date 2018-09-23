<?php

namespace App\Modules\Api\StaffTransformers;

use App\Libs\WalletAdapters\Adapter;
use Carbon\Carbon;

class WalletTransactionsTransformer extends Transformer
{
    public function transform($item, $opt = 'ar')
    {
        //return $item;
        return [
            'id' => $item['id'],
            'status' => $item['status'],
            'amount' => $item['amount'] ,
            'created_at' => $item['created_at'], //Carbon::createFromFormat('Y-m-d H:i:s', $item['created_at'])->diffforhumans(),
            'form_type' => self::WalletType($item['from_wallet']),
            'form_id' => ((self::WalletType($item['from_wallet']) == 'System') ? null : $item['from_id']),
            'form_name' => self::WalletName($item['from_wallet'], $opt),
            'to_type' => self::WalletType($item['to_wallet']),
            'to_id' => $item['to_id'],
            'to_name' => self::WalletName($item['to_wallet'], $opt),
            'TransactionType' => self::TransactionType($item),
        ];
    }

    public function transrormAll($items,$opt){
        return array_map([$this, 'transform'], $items);
    }

    public function OneTransaction($item, $opt)
    {
        //return $item;
        $data = [
            'id' => $item['id'],
            'status' => $item['status'],
            'amount' => $item['amount'] ,
            'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $item['created_at'])->diffforhumans(),
            'form_type' => self::WalletType($item['from_wallet']),
            'form_id' => ((self::WalletType($item['from_wallet']) == 'System') ? null : $item['from_id']),
            'form_name' => self::WalletName($item['from_wallet'], $opt),
            'to_type' => self::WalletType($item['to_wallet']),
            'to_id' => $item['to_id'],
            'to_name' => self::WalletName($item['to_wallet'], $opt),
            'TransactionType' => self::TransactionType($item),
        ];
        if ($data['TransactionType'] == 'Settlement') {
            $InvoiceTransformer = new InvoiceTransformer();
            $data['settlement']['fromDate'] = $item['model']['from_date_time'];
            $data['settlement']['toDate'] = $item['model']['to_date_time'];
            $data['settlement']['invoices'] = $item['model']['num_success'];
            $data['settlement']['invoiceIDs'] = implode(',', $item['model']['payment_invoice']['invoiceIDs']);
            $data['settlement']['service_list'] = $item['model']['payment_invoice']['service_list'];

            //$data['model'] = $item['model'];
        }
        return $data;
    }

    private static function WalletName($walletowner, $lang)
    {
        if (isset($walletowner['walletowner'])) {
            if (array_key_exists('firstname', $walletowner['walletowner'])) {
                return $walletowner['walletowner']['firstname'] . ' ' . $walletowner['walletowner']['lastname'];
            } elseif (array_key_exists('name_en', $walletowner['walletowner'])) {
                return parent::trans($walletowner['walletowner'], 'name', $lang);
            } else {
                return 'Egpay';
            }
        } else {
            return "";
        }
    }

    private static function WalletType($wallet)
    {
        if (isset($wallet['walletowner_id'])) {
            $Type = array_search($wallet['walletowner_type'], Adapter::$ownerType);
            if ($Type == 'main_wallets')
                return 'System';
            if ($Type)
                return $Type;
        }
        return '';
    }

    private static function TransactionType($item)
    {
        if (!$item['model_id']) {
            return 'Transfer';
        } else if ($item['model_type'] == 'App\Models\WalletSettlement') {
            return 'Settlement';
        }
        return '';
    }
}