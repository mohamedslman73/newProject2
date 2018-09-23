<?php

namespace App\Modules\Api\StaffTransformers;

class TransferTransformer extends Transformer
{
    public function transform($item, $opt)
    {
        /*
         * Transfer status
         */
        if ($item['status'] === false) {
            switch ($item['error_code']) {
                //Amount must be number
                case 1:
                    return self::Failtransfer(__('Amount must be a valid number'), $item);
                    break;
                //Not enough balance
                case 2:
                    return self::Failtransfer(__('You do not have enough credit to make this transfer'), $item);
                    break;
                //Can't Transfer to yourself
                case 6:
                    return self::Failtransfer(__('Can not transfer credit to yourself'), $item);
                    break;
                //Transaction type not in [Wallet,Cash]
                case 3:
                    //Error transaction status
                case 4:
                    //User model can't make this transaction
                case 5:
                default:
                    return self::Failtransfer(__('Can not make such transaction, transaction not processed'), $item);
                    break;
            }
        } else {
            return [
                'transactionID' => $item['id'],
                'amount' => $item['amount'] ,
                'isPaid' => (($item['status'] == 'paid') ? true : false),
                'toName' => ((array_key_exists('to_wallet', $item)) ? self::WalletOwnerName($item['to_wallet']['walletowner'], $opt) : ''),
                'toId' => $item['to_id'],
            ];
        }
    }

    private static function WalletOwnerName($item, $opt)
    {
        if (isset($item['merchant_category_id']))
            return self::trans($item, 'name', $opt);
        else
            return $item['mobile'];
    }

    private static function Failtransfer($msg, $item)
    {
        $item['msg'] = $msg;
        return $item;
    }
}