<?php
namespace App\Modules\Api\Merchant;

use App\Libs\AreasData;
use App\Models\MerchantBranch;
use App\Modules\Api\Transformers\BranchTransformer;
use Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApkController extends MerchantApiController {
    public function download(){
        return response()->download(storage_path('app/public/latest-app.apk'));
    }
}