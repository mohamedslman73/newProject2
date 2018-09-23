<?php
namespace App\Modules\Api\User;

use App\Models\MerchantProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\WalletTransaction;
use App\Modules\Api\Transformers\User\OrderitemTransformer;
use App\Modules\Api\Transformers\User\OrderTransformer;
use App\Modules\Api\Transformers\User\TransactionTransformer;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionsApiController extends UserApiController
{
    protected $Transformer;
    public function __construct(TransactionTransformer $transactionTransformer)
    {
        parent::__construct();
        $this->Transformer = $transactionTransformer;
    }

    public function getAllData(Request $request){
        $userObj = Auth::user();

        $eloquentData = WalletTransaction::viewData($this->systemLang,[],$userObj->wallet->id);

        if($request->created_at1 && $request->created_at2) {
            whereBetween($query, 'transactions.created_at', $request->created_at1, $request->created_at2);
        }

        if($request->orderId){
            $eloquentData->where('transactions.model_id','=',$request->orderId);
        }

        //dd($eloquentData->get()->toArray());
        $rows = $eloquentData->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('There are no orders to display'));

        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),$this->systemLang),__('Orders to display'));
    }


    public function AddUserToOrder($qrcode){
        //TODO add User to and order via QRcode
        dd('Not DOne yet');
        $Walletid = Auth()->user()->wallet->id;
        $Order = Order::where('id','=',$order)
            ->whereIn('qr_code',$qrcode)
            ->first();
        $MerchantWalletId = $Order->merchant()->wallet->id;
        if($Order) {
            $userAmount = $Order->totall / $Order->withCount('trans');
            $Order->trans()->create([
                'amount' => $userAmount,
                'from_id' => $Walletid,//wallet id
                'to_id' => $MerchantWalletId,//Merchant wallet id
                'type' => 'wallet',
                'status' => 'unpaid',
            ]);
        } else {
            return $this->respondNotFound(false,__('Order Not found'));
        }

    }


}