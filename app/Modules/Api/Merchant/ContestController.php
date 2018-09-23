<?php
namespace App\Modules\Api\Merchant;

use App\Models\Contest;
use App\Modules\Api\Transformers\ContestTransformer;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContestController extends MerchantApiController {

    public function getAllData(Request $request){
        $lang = $this->systemLang;

        $eloquentData = Contest::viewData($lang);

        $rows = $eloquentData->where('status','active')
            ->where('type','e-payment')
            ->where('beneficiary','merchants')
            ->whereRaw("('".Carbon::now()->format('Y-m-d')."' BETWEEN `start_date` AND `end_date`)")
            ->first();


        if(!$rows){
            return $this->respondNotFound(false,__('There Are no Data to display'));
        }


        $winners = $rows->winners()
            ->join('merchants','merchants.id','=','contest_winners.beneficiary_id')
            ->join('areas','areas.id','=','merchants.area_id')
            ->select([
                'merchants.name_'.$lang.' as merchant_name',
                'areas.name_'.$lang.' as area_name'
            ])
            ->get();

        $rows->winners_list = $winners;

        return $this->respondSuccess($rows);
    }

    public function contestConsumed(){
        $merchant = Auth::id();

        $contest = Contest::where('status', 'active')
            ->where('type', 'e-payment')
            ->where('beneficiary', 'merchants')
            ->whereRaw("('" . Carbon::now()->format('Y-m-d') . "' BETWEEN `start_date` AND `end_date`)")
            ->first();



        if(!$contest){
            return $this->respond(['status' => false, 'code'=>101]);
        }


        $date = Carbon::now()->format('Y-m-d');

        $total = DB::select(" 
        SELECT 
          SUM(payment_invoice.total_amount) as `total`
        FROM
          `payment_invoice`
        INNER JOIN payment_transactions ON payment_transactions.id = payment_invoice.payment_transaction_id
        WHERE
            payment_invoice.status = 'paid'
            AND payment_invoice.creatable_type = 'App\\\\Models\\\\Merchant'
            AND payment_invoice.creatable_id = :merchant 
            AND -- NEED update
            DATE(payment_invoice.created_at) = :created_at
            AND payment_transactions.payment_services_id IN(".implode(',',$contest->service_ids).")
        ",['created_at'=>$date,'merchant'=>$merchant]);

        $target = $contest->target;
        if ($total[0]->total != null) {
            $new_total  = $total[0]->total;
            $total = $target-$new_total;

            if($new_total < $target){
                return $this->respond(['status' => true, 'code'=>101,'msg' => __('There is no Consumed Today'), 'data' => ['contest_amount' => "متبقي لك ".amount($total,true)." لدخول السحب اليومي"]]);
            }else{
                return $this->respond(['status' => true, 'code'=>100,'msg' => __('This is the Total Consumed Today'), 'data' => ['contest_amount' => __('You are now in contest')]]);
            }
        }else{
            $new_total  = 0;
            $total = $target-$new_total;

            return $this->respond(['status' => true, 'code'=>101,'msg' => __('There is no Consumed Today'), 'data' => ['contest_amount' => "متبقي لك ".amount($total,true)." لدخول السحب اليومي"]]);
        }
    }

}