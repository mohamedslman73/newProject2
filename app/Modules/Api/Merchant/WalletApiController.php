<?php
namespace App\Modules\Api\Merchant;

use App\Models\PaymentTransactions;
use App\Modules\Api\Transformers\WalletTransactionsTransformer;
use App\Modules\Api\Transformers\ClientTransformer;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletApiController extends MerchantApiController
{
    protected $Transformer;
    public function __construct(ClientTransformer $walletTransformer)
    {
        parent::__construct();
        $this->Transformer = $walletTransformer;
    }

    public function walletTransactions(Request $request){

        $eloquentData = Auth::user()->paymentWallet->allTransaction();

        whereBetween($eloquentData,'DATE(created_at)',$request->created_at1,$request->created_at2);
        if($request->status){
            $eloquentData->where('status',$request->status);
        }

        $eloquentData->where(function($query){
            $query->whereNull('model_id')->orWhere('model_type','=','App\\Models\\WalletSettlement');
        });

        if($request->status){
            $eloquentData->where('status','=',$request->status);
        }

        if($request->type){
            if($request->type=='settlement')
                $eloquentData->where('model_type','=','App\\Models\\WalletSettlement');
            if($request->type=='transfer')
                $eloquentData->whereNull('model_id');
        }

        $rows = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(['balance'=>Auth::user()->paymentWallet->balance.' '.__('LE')],__('No Wallet Transactions to display'));

        $WalletTransactionsTransformer = new WalletTransactionsTransformer();
        return $this->respondSuccess(
            array_merge(
                $WalletTransactionsTransformer->transformCollection($rows->toArray(),[$this->systemLang]),
                ['balance'=>Auth::user()->paymentWallet()->first()->balance.' '.__('LE')]
            )
            ,__('Merchant Transactions details'));
    }

    public function OneWalletTransactions(Request $request){
        $inputs = $request->only('transaction_id');

        $validator = Validator::make($inputs,[
            'transaction_id'    => 'required|numeric|exists:transactions,id',
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $eloquentData = Auth::user()->paymentWallet->allTransaction()
            ->where('transactions.id','=',$inputs['transaction_id']);

        whereBetween($eloquentData,'DATE(created_at)',$request->created_at1,$request->created_at2);
        if($request->status){
            $eloquentData->where('status',$request->status);
        }

        $eloquentData->where(function($query){
            $query->whereNull('model_id')->orWhere('model_type','=','App\\Models\\WalletSettlement');
        });
        $rows = $eloquentData->first();
        if(!$rows)
            return $this->respondNotFound(['balance'=>Auth::user()->paymentWallet->balance.' '.__('LE')],__('No Wallet Transaction to display'));

        if($rows->model_type == 'App\Models\WalletSettlement'){
            $rows->model->payment_invoice;
            $invoiceIDs = recursiveFind($rows->model->payment_invoice->toARray(),'id');
            if(count($invoiceIDs)){
                $serviceList = PaymentTransactions::serviceList($this->systemLang,[])
                    ->whereIn('payment_invoice.id',$invoiceIDs)
                    ->get();
                $rows['model']['payment_invoice']['service_list'] = $serviceList;
                $rows['model']['payment_invoice']['invoiceIDs'] = $invoiceIDs;
            }

        }
        $WalletTransactionsTransformer = new WalletTransactionsTransformer();
        return $this->respondSuccess(
            array_merge(
                $WalletTransactionsTransformer->OneTransaction($rows->toArray(),$this->systemLang),
                ['balance'=>Auth::user()->paymentWallet()->first()->balance.' '.__('LE')]
            )
            ,__('Merchant Transaction details'));
    }
    /*
    public function balance(){
        $merchantStaff = Auth::user();
        if(!$wallet=$merchantStaff->merchant->wallet)
            return $this->respondNotFound(false,__('User Doesn\'t have Wallet'));

        return $this->respondSuccess($this->Transformer->transform(['balance'=>$wallet->balance],[$this->systemLang]),'Wallet Details');
    }


    function transactions(){
        $merchantStaff = Auth::user();
        if(!$wallet=$merchantStaff->merchant->wallet)
            return $this->respondNotFound(false,__('User Doesn\'t have Wallet'));

        return $this->respondSuccess((new TransactionTransformer())->transformCollection($wallet->trans()->with('from')->with('to')->get()->toArray(),[$this->systemLang]),'Transactions Details');
    }
    */

}