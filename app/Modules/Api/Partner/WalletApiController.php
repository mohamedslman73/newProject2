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






}