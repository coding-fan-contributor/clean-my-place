<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Cleaner;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Payout;
use App\Models\Transaction;
use App\Models\Enquiry;
use App\Models\Notification;
use App\Models\Banner;
use App\Models\ExtraService;
use App\Models\TaxSetting;
use App\Models\Mailcontent;

use App\Models\Cleans;

use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public $ApiController, $PushController;
     public function __construct(){
        $this->PushController = new PushController;
        $this->ApiController = new ApiController;
    }

    // ================= Global
    public function changeStatus(Request $request){
        $params = $request->all();
        if($params['id'] && $params['type']){
            $id = $params['id'];
            switch($params['type']){
                case 'user':
                    $data = User::findOrFail( $id );
                    break;
                case 'cleaner':
                    $data = Cleaner::findOrFail( $id );
                    break;
                case 'banner':
                    $data = Banner::findOrFail( $id );
                    break;
                default:
                    return response()->json(['success'=> false, 'error'=> 'invalid type'], 403);
            }

            if($data->status == 1){
                $data->status = 0;
            }else{
                $data->status = 1;
            }
            $data->save();
            return response()->json($data);
        }else{
            return response()->json(['success'=> false, 'error'=> 'id and type required'], 403);
        }

    }

    public function delete(Request $request){
        $params = $request->all();
        if($params['id'] && $params['type']){
            $id = $params['id'];
            switch($params['type']){
                case 'user':
                    $data = User::findOrFail( $id );
                    // delete existing image if any exists
                    if($data->image){
                        // unlink file
                        @unlink($this->uploadPath.'/profile/'.$data->image);
                    }
                    break;
                case 'cleaner':
                    $data = Cleaner::findOrFail( $id );
                    // delete existing image if any exists
                    if($data->image){
                        // unlink file
                        @unlink($this->uploadPath.'/profile/'.$data->image);
                    }
                    break;
                case 'review':
                    $data = Rating::findOrFail( $id );
                    break;
                case 'enquiry':
                    $data = Enquiry::findOrFail( $id );
                    break;
                case 'banner':
                    $data = Banner::findOrFail( $id );
                    // delete existing image if any exists
                    if($data->image){
                        // unlink file
                        @unlink($this->uploadPath.'/banner/'.$data->image);
                    }
                    break;

                case 'extraservice':
                    $data = ExtraService::findOrFail( $id );
                    break;
                case 'taxsetting':
                    $data = TaxSetting::findOrFail( $id );
                    break;
                case 'mailcontent':
                    $data = Mailcontent::findOrFail( $id );
                    break;
                default:
                    return response()->json(['success'=> false, 'error'=> 'invalid type'], 403);

            }

            $data->delete();

            // update cleaner's average rating if delete rating
            if($params['type'] == 'review'){
                // find cleaner of the rating
                $ratings = Rating::where(['cleaner_id'=> $data->cleaner_id])->get();
                if($ratings->count() > 0){
                    $total = 0;
                    foreach($ratings as $rating){
                        $total += $rating->rating;
                    }
                    $average = ($total / $ratings->count());
                    $cleaner = Cleaner::findOrFail($rating->cleaner_id);
                    $cleaner->rating = $average;
                    $cleaner->save();
                }else{
                    $cleaner = Cleaner::findOrFail($data->cleaner_id);
                    $cleaner->rating = NULL;
                    $cleaner->save();
                }
            }

            return response()->json($data);
        }else{
            return response()->json(['success'=> false, 'error'=> 'id and type required'], 403);
        }

    }

    public function changeCleansStatus(Request $request){
        $params = $request->all();
        if($params['id']){
            $id = $params['id'];
            $data = Cleans::findOrFail( $id );

            if($data->status == 'paid'){
                $data->status = 'completed';
                //******** check if the Cleans has transaction then mark the transaction's order status as completed for payout
                Transaction::where(['order_cleans_id'=> $id])->update(['order_status' => 'complete']);
            }else if($data->status == 'completed'){
                $data->status = 'pending';
            }else if($data->status == 'pending'){
                $data->status = 'paid';
            }
            $data->save();
            return response()->json($data);
        }else{
            return response()->json(['success'=> false, 'error'=> 'id required'], 403);
        }
    }

    public function changePayoutStatus(Request $request){
        $params = $request->all();
        if($params['cleaner_id']){
            $cleaner_id = $params['cleaner_id'];
            $data = Payout::where(['month'=> $params['month'], 'year'=> $params['year'], 'cleaner_id'=> $cleaner_id])->first();
            if($data){
                if($data->status == 'paid'){
                    $insertorupdate = Payout::where(['month'=> $params['month'], 'year'=> $params['year'], 'cleaner_id'=> $cleaner_id])->update(['status'=> 'pending']);
                }else if($data->status == 'pending'){
                    $insertorupdate = Payout::where(['month'=> $params['month'], 'year'=> $params['year'], 'cleaner_id'=> $cleaner_id])->update(['status'=> 'paid']);
                }
            }else{
                // insert with paid
                $insertorupdate = Payout::insert(['month'=> $params['month'], 'year'=> $params['year'], 'cleaner_id'=> $cleaner_id, 'status'=> 'paid', 'amount'=> $params['amount'], 'created_at'=> date('Y-m-d H:i:s')]);
            }
            // get current data
            $data = Payout::where(['month'=> $params['month'], 'year'=> $params['year'], 'cleaner_id'=> $cleaner_id])->first();
            return response()->json($data);
        }else{
            return response()->json(['success'=> false, 'error'=> 'cleaner id required'], 403);
        }
    }

    public function changeAvailability(Request $request){
        $params = $request->all();
        if($params['id']){
            $id = $params['id'];
            $data = Cleaner::findOrFail( $id );

            if($data->available == 'yes'){
                $data->available = 'no';
            }else{
                $data->available = 'yes';
            }
            $data->save();
            return response()->json($data);
        }else{
            return response()->json(['success'=> false, 'error'=> 'id required'], 403);
        }
    }

    public function deletecleanerschedule(Request $request){
        $id = $request->post('id');
        $cleaner_id = $request->post('cleaner_id');
        $res = Schedule::where(['id'=> $id, 'cleaner_id'=> $cleaner_id])->delete();
        if($res){
            return response()->json($res);
        }else{
            return response()->json(['success'=> false, 'error'=> 'Error deleting record'], 403);
        }

    }

    public function pay_payout(Request $request){
        $params = $request->all();
        $months = ['01'=> 'January', '02'=> 'February', '03'=> 'March', '04'=> 'April', '05'=> 'May', '06'=> 'June', '07'=> 'July', '08'=> 'August', '09'=> 'September', '10'=> 'October', '11'=> 'November', '12'=> 'December'];
        // default
        $year = $params['year'];
        $month = $params['month'];

        $monthExample = $year.'-'.$month.'-01';
        $yearExample = $year.'-'.$month.'-01';
        
        if($params['cleaner_id']){
            $cleaner_id = $params['cleaner_id'];

            // if start or end empty then it means full month
            if($params['start'] == '' || $params['end'] == '' || !$params['start'] || !$params['end']){
                // mark transactions as paid for full month till now
                Transaction::whereRaw('MONTH(service_date) = MONTH(\''.$monthExample.'\') AND YEAR(service_date) = YEAR(\''.$yearExample.'\')')->where('payout_status', 'pending')->where('order_status', 'complete')->where('cleaner_id', $cleaner_id)->update(['payout_status'=> 'paid']);
            }else{
                // mark transactions as paid date range wise
                Transaction::whereRaw('MONTH(service_date) = MONTH(\''.$monthExample.'\') AND YEAR(service_date) = YEAR(\''.$yearExample.'\')')->where('payout_status', 'pending')->where('order_status', 'complete')->where('cleaner_id', $cleaner_id)->whereBetween('service_date', [$params['start'], $params['end']])->update(['payout_status'=> 'paid']);
            }

            $data = Payout::where(['month'=> $params['month'], 'year'=> $params['year'], 'cleaner_id'=> $cleaner_id])->first();
            if($data){
                // update existing adding amount
                $payout = Payout::findOrFail($data->id);
                $payout->amount = ($payout->amount + $params['amount']);
                $payout->save();
            }else{
                // insert with paid
                Payout::insert(['month'=> $params['month'], 'year'=> $params['year'], 'cleaner_id'=> $cleaner_id, 'status'=> 'paid', 'amount'=> $params['amount'], 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')]);
            }
            // get current data
            $data = Payout::where(['month'=> $params['month'], 'year'=> $params['year'], 'cleaner_id'=> $cleaner_id])->first();

            return response()->json($data);
        }else{
            return response()->json(['success'=> false, 'error'=> 'cleaner id required'], 403);
        }
    }

    // change order history date
    public function changehistorydate(Request $request){
        if($request->post('id') && $request->post('date')){
            Cleans::where(['id'=> $request->post('id')])->update(['date'=> date('Y-m-d', strtotime($request->post('date')))]);
            return response()->json(['success'=> true, 'message'=> 'Date changed to '.date('Y-m-d', strtotime($request->post('date')))]);
        }else{
            return response()->json(['success'=> false, 'error'=> 'id & date required'], 403);
        }
        
    }


    // resend notifications to nearest cleaners
    public function resend_notifications(Request $request){
        if($request->post('order_id')){
            // get order data
            $orderData = Order::findOrFail($request->post('order_id'));

            // validation
            if($orderData->status == 'pending' || $orderData->status == 'cancelled-user' || $orderData->status == 'completed'){
                return response()->json(['success'=> false, 'error'=> 'This order is currently '.$orderData->status.'.'], 403);
            }

            // update order's current positions to assignable to other person || status paid, cleaner null 
            $orderData->status = 'paid';
            $orderData->cleaner_id = null;
            $orderData->save();

            // delete all previous notifications if any exists
            Notification::where(['order_id' => $orderData->id])->delete();

            // send notifications to the nearest cleaners
            $cleaners = $this->ApiController->get_nearest_cleaners($orderData->id);
            // log order notifications
            foreach ($cleaners as $cleaner) {
                Notification::insert(['user_id'=> $orderData->user_id, 'cleaner_id'=> $cleaner->id, 'order_id'=> $orderData->id, 'date' => $orderData->date, 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')]);
            }
            // send push
            $this->PushController->send_notification_cleaner($orderData->id, $cleaners, 'nearby');

            return response()->json(['success'=> true, 'message'=> 'Notified successfully.']);
        }else{
            return response()->json(['success'=> false, 'error'=> 'order_id required.'], 403);
        }
    }

    // update priority
    public function updatePriority(Request $request){
        $params = $request->all();
        if($params['id'] && $params['type'] && $params['priority']){
            $id = $params['id'];
            switch($params['type']){
                case 'banner':
                    $data = Banner::findOrFail( $id );
                    break;
                case 'taxsetting':
                    $data = TaxSetting::findOrFail( $id );
                    break;
                case 'extraservice':
                    $data = ExtraService::findOrFail( $id );
                    break;
                default:
                    return response()->json(['success'=> false, 'error'=> 'invalid type'], 403);
            }

            $data->priority = $params['priority'];
            $data->save();
            return response()->json($data);
        }else{
            return response()->json(['success'=> false, 'error'=> 'id, type and priority required'], 403);
        }

    }
}
