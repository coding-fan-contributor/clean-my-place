<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Cleaner;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;



class AppPaymentController extends Controller
{
    //
    public $UtilityController;
    public function __construct(){
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $this->UtilityController = new UtilityController;
    }

    public function processpayment($order_id){
		$intent = \Stripe\SetupIntent::create();
        $order = Order::findOrFail($order_id);
        $currency = $this->UtilityController::Currencies()[env('CURRENCY')];

        $logo = Setting::where(['key'=> 'image'])->first();
        if($logo){
            $logo = $logo->value;
        }

    	return view('stripe', ['intent'=> $intent, 'order_id'=> $order_id, 'order'=> $order, 'STRIPE_PAYMENT_URL'=> env('APP_URL').'/processpayment', 'STRIPE_SUCCESS_PAYMENT_URL'=> env('APP_URL').'/paymentsuccess', 'STRIPE_FAILED_PAYMENT_URL'=> env('APP_URL').'/paymentfailed', 'currency'=> $currency, 'logo' => $logo]);
    }

    public function charge(Request $request){
    	$credentials = $request->all();
        $rules = [
            'payment_method' => 'required',
            'order_id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()], 403);
        }

        $order = Order::findOrFail($request->post('order_id'));


        // if already customer has stripe customer id then we should update the payment method 
        // else we create new customer

        if($order->user->stripe_customer_id){
            // existing stripe customer


            // detach previous payment methods from the customer
             $old_payment_methods = \Stripe\PaymentMethod::all([
                  'customer' => $order->user->stripe_customer_id,
                  'type' => 'card',
              ]);

             if(count($old_payment_methods->data) > 0){
                foreach ($old_payment_methods->data as $pm) {
                  $pm_retrive = \Stripe\PaymentMethod::retrieve(
                    $pm->id
                  );
                  $pm_retrive->detach();
                }
             }

            // update payment method of existing customer
            $payment_method = \Stripe\PaymentMethod::retrieve(
              $request->post('payment_method')
            );
            $payment_method->attach([
              'customer' => $order->user->stripe_customer_id,
            ]);

            // set this as default payment method by updating the customer
            \Stripe\Customer::update(
              $order->user->stripe_customer_id,
              ['invoice_settings' => [
                  'default_payment_method' => $request->post('payment_method'),
              ]]
            );

            $customer_id = $order->user->stripe_customer_id;
            $user = user::findOrFail($order->user->id);


        }else{
            // now create the new customer

            $customer = \Stripe\Customer::create([
              'payment_method' => $request->post('payment_method'),
              "description" => "Customer for user_id: ".$order->user->id,
              "email" => $order->user->email,
              "metadata" => [
                    "id" => $order->user->id,
                    "name" => $order->user->name,
                    //"phone" => $user->phone,
                ],

              'invoice_settings' => [
                  'default_payment_method' => $request->post('payment_method'),
              ],
            ]);

            $user = user::findOrFail($order->user->id);
            $user->stripe_customer_id = $customer->id;
            $user->save();

            $customer_id = $customer->id;
        }

            // now charge the customer
            $amount = round($order->price, 2) * 100;
            $currency = env('CURRENCY');

            try {
              $paymentintent = \Stripe\PaymentIntent::create([
                  'amount' => $amount,
                  'currency' => strtolower($currency),
                  'customer' => $customer_id,
                  'payment_method' => $request->post('payment_method'),


                  // 'off_session' => true,
                  'confirm' => true,

                  'save_payment_method' => true,

                  'setup_future_usage' => 'off_session',
              ]);


                // check for paymentintent status 
                if($paymentintent->status != 'succeeded'){
                  // authentication required
                    return response()->json(['data'=> $paymentintent], 402);
                }


                $original_amount = $amount / 100;

                $insertData = ['user_id'=> $user->id, 'order_id'=> $order->id, 'stripe_customer_id'=> $customer_id, 'transaction_id'=> $paymentintent->id, 'original_amount'=> $order->total, 'amount'=> $original_amount, 'data'=> json_encode($paymentintent), 'status'=> $paymentintent->status, 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s'), 'service_date' => $order->date];
                if($order->cleaner_id){
                    $insertData['cleaner_id'] = $order->cleaner_id;
                }
                // create transaction
                $transaction = Transaction::insert($insertData);

              return response()->json($paymentintent);


            } catch (\Stripe\Exception\CardException $e) {
              // Error code will be authentication_required if authentication is needed
              // echo 'Error code is:' . $e->getError()->code;
              $payment_intent_id = $e->getError()->payment_intent->id;
              $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);



              //echo '<pre>';
              //echo(json_encode($payment_intent)); die;

                //TODO: send email of authentication required
              return response()->json(['data'=> $payment_intent], 400);

                


            }


    }


    public function chargeconfirmed(Request $request){
    	$credentials = $request->all();
        $rules = [
            'data' => 'required',
            'order_id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()], 403);
        }

        $data = $request->post('data');

        //return response()->json($data);

        $original_amount = $data['amount'] / 100;

        $order = Order::findOrFail($request->post('order_id'));

        $insertData = ['user_id'=> $order->user->id, 'order_id'=> $order->id, 'stripe_customer_id'=> $order->user->stripe_customer_id, 'transaction_id'=> $data['id'], 'original_amount'=> $order->total, 'amount'=> $original_amount, 'data'=> json_encode($data), 'status'=> $data['status'], 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s'), 'service_date' => $order->date];
        if($order->cleaner_id){
            $insertData['cleaner_id'] = $order->cleaner_id;
        }

        // create transaction
	    $transaction = Transaction::insert($insertData);

	    return response()->json($order);

    }



    // ==================== update card details 
    public function cardupdate($user_id){
      $intent = \Stripe\SetupIntent::create();
      $user = User::findOrFail($user_id);

      $logo = Setting::where(['key'=> 'image'])->first();
      if($logo){
        $logo = $logo->value;
      }

      return view('cardupdate', ['intent'=> $intent, 'STRIPE_PAYMENT_URL'=> env('APP_URL').'/processpayment', 'STRIPE_SUCCESS_PAYMENT_URL'=> env('APP_URL').'/paymentsuccess', 'STRIPE_FAILED_PAYMENT_URL'=> env('APP_URL').'/paymentfailed', 'user' => $user, 'logo' => $logo]);
    }

    public function updatecard(Request $request){
        $credentials = $request->all();
        $rules = [
            'user_id' => 'required',
            'payment_method' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()], 403);
        }

        $user_id = $request->post('user_id');
        $user = User::findOrFail($user_id);

        // detach previous payment methods from the customer
         $old_payment_methods = \Stripe\PaymentMethod::all([
              'customer' => $user->stripe_customer_id,
              'type' => 'card',
          ]);

         if(count($old_payment_methods->data) > 0){
            foreach ($old_payment_methods->data as $pm) {
              $pm_retrive = \Stripe\PaymentMethod::retrieve(
                $pm->id
              );
              $pm_retrive->detach();
            }
         }

        // update payment method of existing customer
        $payment_method = \Stripe\PaymentMethod::retrieve(
          $request->post('payment_method')
        );
        $payment_method->attach([
          'customer' => $user->stripe_customer_id,
        ]);

        // set this as default payment method by updating the customer
        \Stripe\Customer::update(
          $user->stripe_customer_id,
          ['invoice_settings' => [
              'default_payment_method' => $request->post('payment_method'),
          ]]
        );

        return response()->json(['success' => true, 'message' => 'Updated new payment method']);
    }


}
