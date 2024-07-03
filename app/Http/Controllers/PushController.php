<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cleaner;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

use Unirest;

class PushController extends Controller
{
    //
    public function send_notification_cleaner($order_id, $cleaners, $type = null)
    {
        Log::error('test');
        Log::error($order_id);
        //Log::error($cleaners);
        //Log::error($type);

        $role = 'cleaner';
        $tokens = [];
        $message = null;
        $order = Order::findOrFail($order_id);
        $order->user;
        $order->cleaner;

        switch ($type) {
            case 'nearby':
                // collect tokens of cleaners
                foreach ($cleaners as $cleaner) {
                    if ($cleaner->token) {
                        array_push($tokens, $cleaner->token);
                    }
                }
                $message = 'A new cleaning order is available in your area. Check the app to accept and start earning for your work.';
                break;
            case 'selected':
                // collect token of cleaner
                array_push($tokens, $cleaners->token);

                $message = 'Good news! You have been accepted for the '.$order->frequency.' clean on '.$order->date.'. You can see this clean in your My Cleans section in the app.';
                break;

            case 'cancelled':
                // collect tokens of user

                array_push($tokens, $cleaners->token);

                $message = "The ".$order->frequency." clean you requested has now been assigned to another cleaner. More cleans will be available in your area soon.";
                break;

            case 'accepted':
                // collect tokens of user
                if ($order->cleaner->token) {
                    array_push($tokens, $order->cleaner->token);
                }
                $message = 'Good news! You have been accepted for the '.$order->frequency.' clean on '.$order->date.'. You can see this clean in your My Cleans section in the app.';

                break;

            default:
                # code...
                break;
        }
        Log::error("Tokens");
        Log::error($tokens);
        if (count($tokens) > 0 && $message) {
            $this->onesignal_send($order_id, $tokens, $message, $type, $role);
        }
    }

    public function send_notification_user($order_id, $type = null)
    {
        $role = 'user';
        $tokens = [];
        $message = null;
        $order = Order::findOrFail($order_id);
        $order->user;
        $order->cleaner;
        switch ($type) {
            case 'accepted':
                // collect tokens of user
                if ($order->user->token) {
                    array_push($tokens, $order->user->token);
                }
                $message = 'Your booking has been accepted by ' . ucwords($order->cleaner->name);
                break;
            case 'completed':
                // collect tokens of user
                if ($order->user->token) {
                    array_push($tokens, $order->user->token);
                }
                if ($order->frequency == 'oneoff') {
                    $message = ucwords($order->cleaner->name) . ' completed your one-off cleaning service. Please rate to share your experience';
                } else {
                    $message = ucwords($order->cleaner->name) . ' has completed your first service & payment has been taken for your next service date';
                }
                break;

            case 'history_completed':
                // collect tokens of user
                if ($order->user->token) {
                    array_push($tokens, $order->user->token);
                }
                $message = ucwords($order->cleaner->name) . ' has completed one of your ' . $order->frequency . ' cleaning services';
                break;

            case 'cancelled':
                // collect tokens of user
                if ($order->user->token) {
                    array_push($tokens, $order->user->token);
                }
                $message = 'Your cleaning service has been cancelled successfully';
                break;

            case 'hold':
                // collect tokens of user
                if ($order->user->token) {
                    array_push($tokens, $order->user->token);
                }
                $message = 'One of your recurring booking is paused. Please pay manually to resume';
                break;

            default:
                # code...
                break;

        }

        if (count($tokens) > 0 && $message) {
            $this->onesignal_send($order_id, $tokens, $message, $type, $role);
        }
    }

    public function onesignal_send($order_id, $tokens, $message, $type, $role)
    {
        $data = ['type' => $type, 'order_id' => $order_id, 'role' => $role];

        // get icon name
        $icon = Setting::where(['key' => 'icon'])->first();
        if ($icon) {
            $icon = $icon->value;
        }

        $content = array(
            "en" => $message
        );
        //$tokens = [];
        //array_push($tokens,$token);
        $fields = array(
            'app_id' => env('ONESIGNAL_APP_ID'),
            'include_player_ids' => $tokens,
            'data' => $data,
            'contents' => $content,
            'large_icon' => env('APP_URL') . '/uploads/images/' . $icon,
        );
        $fields = json_encode($fields);
        Log::error('onesignal fields');
        Log::error($fields);
        //return $fields;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        Log::error('onesignal response');
        Log::error($response);
        curl_close($ch);

        // debug
        // var_dump($fields);
        // var_dump(json_decode($response)); die;

        return json_decode($response);
    }


    // caht notify =========
    public function chatNotify(Request $request)
    {
        if ($request->post('user_id') && $request->post('cleaner_id') && $request->post('sender')) {

            $tokens = [];
            if ($request->post('sender') == 'user') {
                // send to cleaner
                $target = Cleaner::findOrFail($request->post('cleaner_id'));
                if ($target->token) {
                    array_push($tokens, $target->token);
                    $role = 'cleaner';
                }
            } else {
                // send to user
                $target = User::findOrFail($request->post('user_id'));
                if ($target->token) {
                    array_push($tokens, $target->token);
                    $role = 'user';
                }
            }
            $message = 'You have received a new Chat Message';
            if (count($tokens) > 0 && $message) {
                $this->onesignal_send_chat($request->post('user_id'), $request->post('cleaner_id'), $tokens, $message, 'chat', $role);
            }
            return response()->json(['success' => true, 'message' => 'Notified successfully']);
        } else {
            return response()->json(['success' => false, 'error' => 'user_id, cleaner_id & sender required'], 403);
        }
    }


    public function onesignal_send_chat($user_id, $cleaner_id, $tokens, $message, $type, $role)
    {
        $data = ['type' => $type, 'user_id' => $user_id, 'cleaner_id' => $cleaner_id, 'role' => $role];

        // get icon name
        $icon = Setting::where(['key' => 'icon'])->first();
        if ($icon) {
            $icon = $icon->value;
        }

        $content = array(
            "en" => $message
        );
        $fields = array(
            'app_id' => env('ONESIGNAL_APP_ID'),
            'include_player_ids' => $tokens,
            'data' => $data,
            'contents' => $content,
            'large_icon' => env('APP_URL') . '/uploads/images/' . $icon,
        );
        $fields = json_encode($fields);

        //return $fields;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        // debug
        // var_dump($fields);
        // var_dump(json_decode($response)); die;

        return json_decode($response);
    }


    // custom push notification
    public function custom_push($tokens, $message)
    {
        // get icon name
        $icon = Setting::where(['key' => 'icon'])->first();
        if ($icon) {
            $icon = $icon->value;
        }

        $content = array(
            "en" => $message
        );
        $fields = array(
            'app_id' => env('ONESIGNAL_APP_ID'),
            'include_player_ids' => $tokens,
            'contents' => $content,
            'large_icon' => env('APP_URL') . '/uploads/images/' . $icon,
        );
        $fields = json_encode($fields);

        //return $fields;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        // debug
        //var_dump($fields);
        //var_dump(json_decode($response)); die;

        return json_decode($response);
    }


    // reminder by cron order
    public function reminder($order_id, $mode, $tokens, $message, $type, $role)
    {
        $data = ['type' => $type, 'order_id' => $order_id, 'role' => $role, 'mode' => $mode];

        // get icon name
        $icon = Setting::where(['key' => 'icon'])->first();
        if ($icon) {
            $icon = $icon->value;
        }

        $content = array(
            "en" => $message
        );
        $fields = array(
            'app_id' => env('ONESIGNAL_APP_ID'),
            'include_player_ids' => $tokens,
            'data' => $data,
            'contents' => $content,
            'large_icon' => env('APP_URL') . '/uploads/images/' . $icon,
        );
        $fields = json_encode($fields);

        //return $fields;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        // debug
        // var_dump($fields);
        // var_dump(json_decode($response)); die;

        return json_decode($response);
    }


}
