<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\ChequeExpiryNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Nilambar\NepaliDate\NepaliDate;

class UpdateNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'it will push the notification if cheque expiry date is near';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            Log::info('i am running from notification controller');

            $transactions = Transaction::select('id', 'cheque_exchange_date', 'cheque_no')->where('ischeque', '=', 1)->where('notification_status', '=', 0)->get();
            $now = Carbon::now()->format('Y/m/d');

            foreach ($transactions as $transaction) {
                $date = Carbon::parse($this->chequeExchangeDate($transaction->cheque_exchange_date));
                $diff = $date->diffInDays($now);
                Log::info('This is a different date: '.$diff);
                if ($diff < 5) {
                    Log::info('I am from condition');
                    $users = Admin::all();
                    //notify users with cheque number and remaining day
                    Notification::send($users, new ChequeExpiryNotification($diff, $transaction->cheque_no));
                    //update transaction notification status
                    Transaction::where('id', '=', $transaction->id)
                        ->update([
                            'notification_status' => 1,
                        ]);
                }
            }
            Log::info('code executed');
        } catch (\Exception $ex) {
            Log::error('this is catch error for notification: '.$ex->getMessage());
        }
    }

    public function chequeExchangeDate($dateNepali)
    {
        $obj = new NepaliDate();
        if (isset($dateNepali)) {
            $nepDate = explode('/', $dateNepali);
            $date = $obj->convertBsToAd($nepDate[0], $nepDate[1], $nepDate[2]);
            Log::info("English date: {$date['year']}/{$date['month']}/{$date['day']}");

            return "{$date['year']}/{$date['month']}/{$date['day']}";
        } else {
            return  null;
        }
    }
}
