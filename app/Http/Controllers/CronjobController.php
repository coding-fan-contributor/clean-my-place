<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Setting;
use App\Models\Favourite;
use App\Models\Transaction;
use App\Models\Payout;
use App\Models\CleanerData;
use App\Models\Notification;
use App\Models\Enquiry;
use App\Models\CronLog;
use App\Models\Cleans;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;

use Illuminate\Support\Facades\Validator;

use DB;

use Unirest;

class CronjobController extends Controller
{
    //
    public $UtilityController, $StripeController, $MailController, $PushController;
    public function __construct(){
        //constructor
        // use utility controller
        $this->UtilityController = new UtilityController;
        // use Stripe controller
        $this->StripeController = new StripeController;
        // user mail controller
        $this->MailController = new MailController;
        // user push controller
        $this->PushController = new PushController;
    }

    // send reminders
    public function reminder(){
        // default cleaner 3 hours prior & users 1 hours prior
        $defaultHoursForCleaners = 3;
        $defaultHoursForUsers = 1;

        $getCleanerHours = Setting::where(['key'=> 'cron_cleaner_hours'])->first();
        if($getCleanerHours){
            $defaultHoursForCleaners = $getCleanerHours->value;
        }

        $getUserHours = Setting::where(['key'=> 'cron_user_hours'])->first();
        if($getUserHours){
            $defaultHoursForUsers = $getUserHours->value;
        }

        $current_date = date('Y-m-d');

        $orders = Order::with(['cleaner', 'user'])->where(function($query){
            $query->orWhere('status', 'paid');
            $query->orWhere('status', 'accepted');
        })->where('date', $current_date)->whereNotNull('cleaner_id')->get();

        $ordercleans = Cleans::with(['order', 'order.cleaner', 'order.user'])->whereHas('order', (function($q){
            $q->whereNotNull('cleaner_id');
        }))->where('status', 'paid')->where('date', $current_date)->get();

        foreach($orders as $order){
            // check if time not over yet
            if(time() < strtotime($order->date .' '.$order->time)){
                // cleaner check
                if((strtotime($order->date .' '.$order->time) - time()) <= ($defaultHoursForCleaners * 3600) && (strtotime($order->date .' '.$order->time) - time()) >= (($defaultHoursForCleaners - 1) *3600)){
                    if($order->cleaner->token){
                        // send push
                        $this->PushController->reminder($order->id, 'order', [$order->cleaner->token], 'Your clean for '.$order->user->name.' starts in three hours', 'reminder', 'cleaner');
                    }
                }

                // user check
                if((strtotime($order->date .' '.$order->time) - time()) <= (($defaultHoursForUsers + 1) *3600) && (strtotime($order->date .' '.$order->time) - time()) >= ($defaultHoursForUsers * 3600)){
                    if($order->user->token){
                        // send push
                        $this->PushController->reminder($order->id, 'order', [$order->user->token], 'Your cleaner '.$order->cleaner->name.' is due to arrive in one hour', 'reminder', 'user');
                    }
                }
            }
        }

        foreach($ordercleans as $orderclean){
            // check if time not over yet
            if(time() < strtotime($orderclean->date .' '.$orderclean->time)){
                // cleaner check
                if((strtotime($orderclean->date .' '.$orderclean->time) - time()) <= ($defaultHoursForCleaners * 3600) && (strtotime($orderclean->date .' '.$orderclean->time) - time()) >= (($defaultHoursForCleaners - 1) * 3600)){
                    if($orderclean->order->cleaner->token){
                        // send push
                        $this->PushController->reminder($orderclean->order->id,  'orderhistory',[$orderclean->order->cleaner->token], 'Your clean for '.$orderclean->order->user->name.' starts in three hours', 'reminder', 'cleaner');
                    }
                }

                // user check
                if((strtotime($orderclean->date .' '.$orderclean->time) - time()) <= (($defaultHoursForUsers + 1) * 3600) && (strtotime($orderclean->date .' '.$orderclean->time) - time()) >= ($defaultHoursForUsers * 3600)){
                    if($orderclean->order->user->token){
                        // send push
                        $this->PushController->reminder($orderclean->order->id, 'orderhistory', [$orderclean->order->user->token], 'Your cleaner '.$orderclean->order->cleaner->name.' is due to arrive in one hour', 'reminder', 'user');
                    }
                }
            }
        }
       
        CronLog::insert(['cron_name'=> "reminder" , 'create_date'=> date('Y-m-d H:i:s')]);

        return response()->json(['success' => true, 'message' => 'ok']);
    }



    // send notification to cleaners 
    public function complete_reminder(){
        $current_date = date('Y-m-d');
        // check today's orders and orderhistories
        $current_time = strtotime(date('H:i:s'));

        $orders = Order::with(['cleaner', 'user'])->where(function($query){
            $query->orWhere('status', 'paid');
            $query->orWhere('status', 'accepted');
        })->where('date', $current_date)->whereNotNull('cleaner_id')->get();

        $cleans = Cleans::with(['order', 'order.cleaner', 'order.user'])->whereHas('order', (function($q){
            $q->whereNotNull('cleaner_id');
        }))->where('status', 'paid')->where('date', $current_date)->get();

        foreach ($orders as $order) {
            $end = strtotime($order->time) + (60 * 60 * $order->hours);
            $notification_time_start = $end + (60 * 60);
            $notification_time_end = $end + (60 * 60 * (1+1));
            // one hour after the order end time but not more than 1 hours later the order end time -- means only once as cronorder is hourly
            if($current_time >= $notification_time_start && $current_time <= $notification_time_end){
                // send the cleaner a notification reminding about to mark the order as completed
                if($order->cleaner->token){
                    // send push
                    $this->PushController->reminder($order->id, 'order', [$order->cleaner->token], 'Please confirm your order has been completed', 'reminder', 'cleaner');
                }
            }
        }

        foreach ($cleans as $clean) {
            $end = strtotime($clean->time) + (60 * 60 * $clean->order->hours);
            $notification_time_start = $end + (60 * 60);
            $notification_time_end = $end + (60 * 60 * (1+1));
            // one hour after the order end time but not more than 1 hours later the order end time -- means only once as cronorder is hourly
            if($current_time >= $notification_time_start && $current_time <= $notification_time_end){
                // send the cleaner a notification reminding about to mark the order as completed
                if($clean->order->cleaner->token){
                    array_push($arr, $clean);
                    // send push
                    $this->PushController->reminder($clean->order->id, 'orderhistory', [$clean->order->cleaner->token], 'Please confirm your order has been completed', 'reminder', 'cleaner');
                }
            }
        }

        CronLog::insert(['cron_name'=> "complete_reminder",  'create_date'=> date('Y-m-d H:i:s')]);

        return response()->json(['success' => true, 'message' => 'ok']);

    }


    public function cron_test(){
        CronLog::insert(['cron_name'=> "cron_test",  'create_date'=> date('Y-m-d H:i:s')]);
        return response()->json(['success' => true, 'message' => 'ok']);
    }

}
