<?php

namespace App\Console;

use App\Libs\Commission;
use App\Mail\SendDirectEmail;
use App\Models\PaymentInvoice;
use App\Models\RechargeList;
use App\Models\Staff;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon;
use Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\PrpjectRefresh::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule){

        // Reverse Money To Fail Payment
        /*$schedule->call(function () {

            $dateNow = Carbon::now()->format('Y-m-d H:i:s');
            $paymentInvoice = PaymentInvoice::where('status','=','pending')
                ->whereRaw("DATE_ADD(`created_at`,INTERVAL ".setting('reverse_fail_payments_invoice')." HOUR) <= '".$dateNow."'")
                ->with(['payment_transaction','wallet_transaction'])
                ->get();

            if($paymentInvoice){
                foreach ($paymentInvoice as $key => $value){
                    $adapter = \App\Libs\Payments\Payments::selectAdapterByService($value->payment_services_id);
                    $transactionStatus = $adapter::transactionStatus($value->payment_transaction->id);

                    \DB::transaction(function() use($transactionStatus,$value) {
                        if($transactionStatus['status']){
                            \App\Libs\WalletData::changeTransactionStatus($value->wallet_transaction->id,'paid');
                            $value->update(['status'=>'paid']);
                        }else{
                            \App\Libs\WalletData::changeTransactionStatus($value->wallet_transaction->id,'reverse');
                            $value->update(['status'=>'reverse']);
                        }
                    });

                }

            }

        })->hourly();*/
        // Reverse Money To Fail Payment



        // Settlement Payment Data
        $schedule->call(function (){
            $carbon = Carbon::yesterday();
            $yesterday = $carbon->format('Y-m-d');

            foreach (explode("\n",setting('staff_ids_receive_daily_report')) as $key => $value){
                $user = Staff::find($value);
                if($user){
                    Mail::to($user)
                        ->send(new SendDirectEmail(paymentDailyReportByEmail($yesterday)));
                }
            }
        })->dailyAt('00:02');

/*
        // Settlement Payment Data
        $schedule->call(function (){
            $carbon = Carbon::yesterday();
            $yesterday = $carbon->format('Y-m-d H:i:s');

            $carbon->addHours(23);
            $carbon->addMinute(59);
            $carbon->addSeconds(59);

            Commission::paymentSettlement($yesterday,$carbon->format('Y-m-d H:i:s'),'App\Models\Merchant');
            Commission::savePaymentSettlement();

        })->dailyAt('01:00');


        $schedule->call(function (){
            RechargeList::where('status','approved')
                ->where('system_run','no')
                ->limit(1)
                ->get();
        });
*/

        // Backup All files and database
        /*$schedule->command('backup:clean')->weekly()->fridays()->at('02:00');
        $schedule->command('backup:monitor')->dailyAt('03:00');
        $schedule->command('backup:run')->dailyAt('04:00');*/
        // Backup All files and database
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
