<?php
namespace App\Observers;

use App\Models\MerchantProduct;
use App\Models\OrderItem;
class OrderItemsObserver {
/*
    public function creating(OrderItem $orderitem){
        $row = OrderItem::where('merchant_product_id',$orderitem->merchant_product_id)->where('order_id',$orderitem->order_id)->first();
        if($row) {
            $row->update(['qty'=>$orderitem->qty]);
            return false;
        }
    }


    public function created(OrderItem $orderitem){
        return $orderitem->order()->increment('total',MerchantProduct::where('id',$orderitem->merchant_product_id)->first()->price * $orderitem->qty);
    }

    public function updated(OrderItem $orderitem){
        $items = $orderitem->order->orderitems()->get(['price','qty'])->toArray();
        $totall = array_map(function($item){
            return $item['price'] * $item['qty'];
        },$items);
        return $orderitem->order->update([
            'total' => array_sum($totall)
        ]);
    }

    public function deleted(OrderItem $orderitem){
        return $orderitem->order()->decrement('total',MerchantProduct::where('id',$orderitem->merchant_product_id)->first()->price * $orderitem->qty);

    }
*/
}
