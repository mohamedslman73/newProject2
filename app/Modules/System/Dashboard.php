<?php

namespace App\Modules\System;


use App\Libs\Commission;
use App\Libs\Payments\Adapters\Bee;
use App\Libs\WalletData;
use App\Models\Client;
use App\Models\Item;
use App\Models\ItemCategories;
use App\Models\Merchant;
use App\Models\MerchantBranch;
use App\Models\PaymentInvoice;
use App\Models\PaymentServiceAPIParameters;
use App\Models\PaymentServiceAPIs;
use App\Models\PaymentServiceProviderCategories;
use App\Models\PaymentServiceProviders;
use App\Models\PaymentServices;
use App\Models\PaymentTransactions;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\SupplierCategories;
use App\Models\SupplierOrders;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\UserNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Hash;
use Illuminate\Support\Facades\Crypt;
use App\Libs\SMS;


use App\Libs\Payments\Payments;


class dashboard extends SystemController{


    public function __construct(Request $request){
        parent::__construct();
        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Home'),
                'url'=> url('system'),
            ]
        ];
    }


    public function index(Request $request){

        if($request->amr){
            getCategoryTreeSelect();
            dd();
        }


        $this->viewData['itemCount'] = Item::count();
        $this->viewData['itemCategoryCount'] = ItemCategories::count();
        $this->viewData['clientCount'] = Client::whereStatus('active')->count();
        $this->viewData['projectCount'] = Project::count();
        $this->viewData['projectThisMonth'] = Project::where( DB::raw('MONTH(created_at)'), '=', date('n') )->count();
        $this->viewData['supplierCategoryCount'] = SupplierCategories::count();
        $this->viewData['supplierCount'] = Supplier::whereStatus('active')->count();
        $this->viewData['supplierOrderCount'] = SupplierOrders::count();
        $this->viewData['supplierOrderThisMonth'] = SupplierOrders::where( DB::raw('MONTH(created_at)'), '=', date('n') )->count();
        $this->viewData['staffCount'] = Staff::count();

        return $this->view('dashboard.index',$this->viewData);
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('system.dashboard');
    }

    public function changePassword(Request $request){
        if($request->method() == 'POST'){

            $this->validate($request,[
                'old_password'          => 'required',
                'password'              => 'required|confirmed',
                'password_confirmation' => 'required'
            ]);

            if (!Hash::check($request->old_password, Auth::user()->getAuthPassword())){
                return back()
                    ->with('status','danger')
                    ->with('msg',__('Old Password is incorrect'));
            }

            Staff::find(Auth::id())->update(['password'=>bcrypt($request->password)]);

            return back()
                ->with('status','success')
                ->with('msg',__('Your Password Has been changed successfully'));
        }else{
            $this->viewData['pageTitle'] = __('Change Password');
            return $this->view('dashboard.change-password',$this->viewData);
        }
    }


    public function encrypt(Request $request){
        $type = $request->encrypt_type;
        $text = $request->encrypt_text;

        if(
            !in_array($type,['encrypt','decrypt']) ||
            empty($text)
        ){
            return ['status'=>false,'msg'=>__('Please Enter valid data')];
        }

        if($type == 'encrypt'){
            return ['status'=>true,'data'=> Crypt::encryptString($text)];
        }else{
            return ['status'=>true,'data'=> Crypt::decryptString($text)];
        }

    }

    public function development(Request $request){
//        echo bcrypt(123456);exit;


        if(!in_array(Auth::id(),[1,42]) ){
            abort(403);
        }

        switch ($request->type){


            case 'client_number':

                $a = PaymentTransactions::where('response_type','done')->where('service_type','payment')->get();

                $data = [];
                foreach ($a as $key => $value){
                    $number = array_first($value->request_map);
                    if(!in_array($number,$data) && mb_strlen($number) == 11){
                        $data[] = $number;
                        echo $number."<br/>";
                    }
                }

                echo 'DONE';

                break;

            case 'last-5-days':


                function totalMerchantByDate($merchantModal,$date){
                    $a = $merchantModal->payment_invoice()
                        ->where('status','paid')
                        ->whereRaw("DATE(`created_at`) = '".$date."'")
                        ->selectRaw('SUM(total_amount) as total')
                        ->first()
                        ->total;


                    return amount($a,true);

                }


                $merchants = Merchant::get();

                $table = '<table>';
                $table.='<thead>
                    <tr>
                        <th>Merchant ID</th>
                        <th>Merchant Name</th>
                        <th>Sales</th>
                        <th>12-05-2018</th>
                        <th>11-05-2018</th>
                        <th>10-05-2018</th>
                        <th>09-05-2018</th>
                        <th>08-05-2018</th>
                        <th>07-05-2018</th>
                    </tr>
                    </thead><tbody>';
                foreach($merchants as $key => $value){
                    $table.= '<tr>
                        <th>'.$value->id.'</th>
                        <th>'.$value->name_en.'</th>
                        <th>'.$value->staff->fullname.'</th>
                        <th>'.totalMerchantByDate($value,'2018-05-12').'</th>
                        <th>'.totalMerchantByDate($value,'2018-05-11').'</th>
                        <th>'.totalMerchantByDate($value,'2018-05-10').'</th>
                        <th>'.totalMerchantByDate($value,'2018-05-09').'</th>
                        <th>'.totalMerchantByDate($value,'2018-05-08').'</th>
                        <th>'.totalMerchantByDate($value,'2018-05-07').'</th>
                    </tr>';
                }

                $table.='</tbody></table>';


                echo $table;

                break;


            case 'clear-cache':
               // opcache_reset();
                \Artisan::call('cache:clear');
                \Artisan::call('route:cache');
                echo 'cached clear';
                break;

            case 'clear-opcache':
                opcache_reset();
                break;

            case 'bee-xml':

                $lang = $request->lang ?? 'en';

                header('Content-type: text/xml');
                echo Bee::serviceList(0,$lang,'XML');

                break;

            case 'bee-issue':
                dd(Bee::transactionStatus(28829));


                echo(implode(',',array_column($data,'id')));


                break;



            case 'bee-services':
//                header('Content-type: text/xml');
//                echo(Bee::serviceList());
//                exit;



//                header('Content-type: text/xml');
//
//                echo Bee::serviceList();exit;
//
//
                $bee = json_decode(json_encode(Bee::serviceList()))->data;
//
//                foreach ($bee->serviceList->service as $key => $value){
//                    if($key == 228){
//                        foreach ($value as $k2 => $value2){
//                            echo (string) $k2.': '. $value2."\n";
//                        }
//                    }
//                }
//
//
//                exit;

                // Categories
                $getNewCategories = [];
                $getOldCategories = [];
                foreach ($bee->providerGroupList->providerGroup as $key => $value){
                    $data = PaymentServiceProviderCategories::find($value->id);
                    if(!$data){
                        $getNewCategories[] = (array)$value;
                    }else{
                        $getOldCategories[] = $data->toArray();
                    }
                }
                $this->viewData['getNewCategories'] = $getNewCategories;
                $this->viewData['getOldCategories'] = $getOldCategories;
                // Categories

                // Providers
                $getNewProviders = [];
                $getOldProviders = [];
                foreach ($bee->providerList->provider as $key => $value){
                    $data = PaymentServiceProviders::find($value->id);
                    if(!$data){
                        $getNewProviders[] = (array)$value;
                    }else{
                        $getOldProviders[] = $data->toArray();
                    }
                }

                $this->viewData['getNewProviders'] = collect($getNewProviders)->groupBy('providerGroupId')->toArray();
                $this->viewData['getOldProviders'] = collect($getOldProviders)->groupBy('payment_service_provider_category_id')->toArray();
                $this->viewData['getNewProvidersKeys'] = array_keys($this->viewData['getNewProviders']);


                // Providers

                // Service
                $getNewServices = [];
                foreach ($bee->serviceList->service as $key => $value){
                    $data = PaymentServiceAPIs::where('external_system_id',$value->accountId)->first();
                    if(!$data){
                        $getNewServices[] = (array)$value;
                    }
                }
                $this->viewData['getNewServices'] = collect($getNewServices)->groupBy('providerId')->toArray();
                // Service

                
                // ------------------------------------------

                // Service
                $getNewServices = [];
                foreach ($bee->serviceList->service as $key => $value){
                    $data = PaymentServiceAPIs::where('external_system_id',$value->accountId)->first();
                    if(!$data){
                        $getNewServices[] = (array)$value;
                    }
                }
                $this->viewData['getNewServices'] = collect($getNewServices)->groupBy('providerId')->toArray();
                // Service

//                dd($this->viewData['getNewServices']);


                return $this->view('development.bee-services',$this->viewData);
            case 'sales-report':
                set_time_limit(0);
                $IDs = $request->ids;
                $data = PaymentInvoice::where('status','paid')
                    ->where('creatable_type','App\Models\Merchant')
                    ->whereIn('creatable_id',explode(',',$IDs))
                    ->get();



                $systemCommission   = 0;
                $merchantCommission = 0;

                foreach ($data as $key => $value){
                    $Commission = Commission::calculateCommission(
                        $value->total_amount,
                        $value->payment_transaction->payment_services->commission_list->id,
                        $value->creatable
                    );

                    $systemCommission  += $Commission['data']->system_commission;
                    $merchantCommission+= $Commission['data']->merchant_commission;

                }

                $totalPaid = PaymentInvoice::where('status','paid')
                    ->where('creatable_type','App\Models\Merchant')
                    ->whereIn('creatable_id',explode(',',$IDs))
                    ->selectRaw('SUM(total_amount) as `total`')
                    ->first();

                echo "Total Paid:".$totalPaid->total." <br /> System Commission: $systemCommission <br /> Merchant Commission: $merchantCommission";

                break;
            case 'merchant-report':

                function CalcCommissionDATA($merchantID){
                    $data = PaymentInvoice::where('status','paid')
                        ->where('creatable_type','App\Models\Merchant')
                        ->where('creatable_id',$merchantID)
                        ->get();

                    $systemCommission   = 0;
                    $merchantCommission = 0;

                    foreach ($data as $key => $value){
                        $Commission = Commission::calculateCommission(
                            $value->total_amount,
                            $value->payment_transaction->payment_services->commission_list->id,
                            $value->creatable
                        );

                        $systemCommission  += $Commission['data']->system_commission;
                        $merchantCommission+= $Commission['data']->merchant_commission;

                    }

                    return [
                        'systemCommission'=> $systemCommission,
                        'merchantCommission'=> $merchantCommission,
                        'totalCommission'=> $systemCommission+$merchantCommission
                    ];

                }

                $IDs = $request->ids;

                $IDsData = array_flip(explode(',',$IDs));

                $data = PaymentInvoice::where('status','paid')
                    ->where('creatable_type','App\Models\Merchant')
                    ->whereIn('creatable_id',explode(',',$IDs))
                    ->select(['*'])
                    ->addSelect(\DB::raw('SUM(total_amount) as `sum`'))
                    ->groupBy('creatable_id')
                    ->get();

                $table = '<table>';
                $table.= '<thead>
                            <tr>
                                <td>ID</td>
                                <td>Name</td>
                                <td>Total Paid</td>
                                <td>Merchant Commission</td>
                                <td>System Commission</td>
                                <td>Total Commission</td>
                            </tr>
                          </thead><tbody>';

                $totalPaid                  = 0;
                $totalmerchantCommission    = 0;
                $totalsystemCommission      = 0;
                $totaltotalCommission       = 0;

                foreach ($data as $key => $value){

                    unset($IDsData[$value->creatable->id]);



                    $commission = CalcCommissionDATA($value->creatable->id);
                    $table.= '<tr>
                                <td>'.$value->creatable->id.'</td>
                                <td>'.$value->creatable->name_ar.'</td>
                                <td>'.$value->sum.'</td>
                                <td>'.$commission['merchantCommission'].'</td>
                                <td>'.$commission['systemCommission'].'</td>
                                <td>'.$commission['totalCommission'].'</td>
                              </tr>';

                    $totalPaid                  += $value->sum;
                    $totalmerchantCommission    += $commission['merchantCommission'];
                    $totalsystemCommission      += $commission['systemCommission'];
                    $totaltotalCommission       += $commission['totalCommission'];

                }



                if(!empty($IDsData)){
                    $merchantNotPaid = Merchant::whereIn('id',array_keys($IDsData))->get();
                    foreach ($merchantNotPaid as $key => $value){
                        $table.= '<tr>
                                <td>'.$value->id.'</td>
                                <td>'.$value->name_ar.'</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                              </tr>';
                    }
                }


                $table.= '<tr>
                                <td colspan="2">Total</td>
                                <td>'.$totalPaid.'</td>
                                <td>'.$totalmerchantCommission.'</td>
                                <td>'.$totalsystemCommission.'</td>
                                <td>'.$totaltotalCommission.'</td>
                              </tr>';


                $table.= '</tbody></table>';

                echo $table;

                break;
        }

    }

}