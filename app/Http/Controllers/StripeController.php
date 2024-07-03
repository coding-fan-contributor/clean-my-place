<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Cleans;
use Exception;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    //
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }

    public function charge_new_customer($user_id, $order_id, $amount, $token)
    {
        $amount = round($amount, 2) * 100;
        $user = User::findorFail($user_id);
        $order = Order::findorFail($order_id);

        // ceate a new stripe customer & charge
        $customer = \Stripe\Customer::create([
            "description" => "Customer for user_id: " . $user->id,
            "email" => $user->email,
            "source" => $token, // obtained with Stripe.js
            "metadata" => [
                "id" => $user->id,
                "name" => $user->name,
                //"phone" => $user->phone,
            ],
        ]);
        $customer_id = $customer->id;
        // update stripe_customer_id
        $user->stripe_customer_id = $customer_id;


        try {
            $charge = \Stripe\Charge::create(['amount' => $amount, 'currency' => strtolower(env('CURRENCY')), 'customer' => $customer_id]);
            $original_amount = $charge->amount / 100;
            // create transaction
            $transaction = Transaction::insert(['user_id' => $user_id, 'order_id' => $order_id, 'stripe_customer_id' => $customer_id, 'transaction_id' => $charge->id, 'original_amount' => $order->total, 'amount' => $original_amount, 'data' => json_encode($charge), 'status' => $charge->status, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'service_date' => $order->date]);
            $user->save();
            return response()->json($charge);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'Error in charging new customer'], 400);
        }
    }

    public function charge_existing_customer($user_id, $order_id, $amount)
    {
        $amount = round($amount, 2) * 100;
        $order = Order::findorFail($order_id);
        $user = User::findorFail($user_id);
        // get customer id from existing record
        $customer_id = $user->stripe_customer_id;
        if ($user && $customer_id) {
            // try to charge
            try {

                // retrive payment method of the customer
                $payment_methods = \Stripe\PaymentMethod::all([
                    'customer' => $customer_id,
                    'type' => 'card',
                ]);

                // try to charge with payment intent
                $charge = \Stripe\PaymentIntent::create([
                    'amount' => $amount,
                    'currency' => strtolower(env('CURRENCY')),
                    'customer' => $customer_id,
                    'off_session' => true,
                    'confirm' => true,

                    'payment_method' => $payment_methods->data[0]->id,
                ]);

                $original_amount = $charge->amount / 100;
                // create transaction
                $transaction = Transaction::insert(['user_id' => $user_id, 'order_id' => $order_id, 'stripe_customer_id' => $customer_id, 'transaction_id' => $charge->id, 'original_amount' => $order->total, 'amount' => $original_amount, 'data' => json_encode($charge), 'status' => $charge->status, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'service_date' => $order->date]);
                return response()->json($charge);
            } catch (Exception $e) {
                return response()->json(['success' => false, 'error' => 'Error in charging existing customer'], 400);
            }
        } else {
            // either user or customer id not found
            return response()->json(['success' => false, 'error' => 'user or stripe_customer_id not found'], 404);
        }
    }


    // refund the user
    public function refund($intent, $type)
    {
        if ($type == 'full') {
            // full refund
            try {
                $refund = \Stripe\Refund::create([
                    'payment_intent' => $intent,
                ]);
                // update the transaction status = refunded **** added order status to completed for payout only for completed orders
                Transaction::where(['transaction_id' => $intent])->update(['status' => 'refunded', 'order_status' => 'complete']);
                return true;
            } catch (Exception $e) {
                return false;
            }
        } else {
            // partial refund
            try {
                $retrive = \Stripe\PaymentIntent::retrieve($intent);
                $amount = $retrive->amount;

                $paid_cancellation_charge = Setting::where(['key' => 'paid_cancellation_charge'])->first();
                if ($paid_cancellation_charge) {
                    $paid_cancellation_charge = $paid_cancellation_charge->value;
                    $refund_amount = intval($retrive->amount - (($retrive->amount * $paid_cancellation_charge) / 100));
                } else {
                    // deduct 25% static
                    $refund_amount = intval($retrive->amount - (($retrive->amount * 25) / 100));
                }

                if ($refund_amount > 0) {
                    $refund = \Stripe\Refund::create([
                        'amount' => $refund_amount,
                        'payment_intent' => $intent,
                    ]);
                }

                // update the transaction status = refunded **** added order status to completed for payout only for completed orders
                Transaction::where(['transaction_id' => $intent])->update(['status' => 'partial-refunded', 'order_status' => 'complete']);

                return true;
            } catch (Exception $e) {
                return false;
            }

        }

    }


    public function charge_existing_customer_silently($user_id, $order_id, $amount, $order_cleans_id = null)
    {
        $amount = round($amount, 2) * 100;
        $user = User::findorFail($user_id);
        $order = Order::findorFail($order_id);
        // get customer id from existing record
        $customer_id = $user->stripe_customer_id;
        if ($user && $customer_id) {
            // try to charge
            try {

                // retrive payment method of the customer
                $payment_methods = \Stripe\PaymentMethod::all([
                    'customer' => $customer_id,
                    'type' => 'card',
                ]);

                // try to charge with payment intent
                $charge = \Stripe\PaymentIntent::create([
                    'amount' => $amount,
                    'currency' => strtolower(env('CURRENCY')),
                    'customer' => $customer_id,
                    'off_session' => true,
                    'confirm' => true,

                    'payment_method' => $payment_methods->data[0]->id,
                ]);

                $original_amount = $charge->amount / 100;
                // create transaction
                $insertData = ['user_id' => $user_id, 'cleaner_id' => $order->cleaner_id, 'order_id' => $order_id, 'stripe_customer_id' => $customer_id, 'transaction_id' => $charge->id, 'original_amount' => $order->total, 'amount' => $original_amount, 'data' => json_encode($charge), 'status' => $charge->status, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'service_date' => $order->date];

                // if order history id sent insert it
                if ($order_cleans_id) {
                    // get order history
                    $order_history = Cleans::findorFail($order_cleans_id);

                    $insertData['order_cleans_id'] = $order_cleans_id;
                    $insertData['service_date'] = $order_history->date;
                }
                $transaction = Transaction::insert($insertData);
                return true;
                //return response()->json($charge);
            } catch (Exception $e) {
                // return response()->json(['success'=> false, 'error'=> 'Error in charging existing customer'], 400);
                return false;
            }
        } else {
            // either user or customer id not found
            // return response()->json(['success'=> false, 'error'=> 'user or stripe_customer_id not found'], 404);
            return false;
        }
    }

    // charge custom for like skip fees
    public function charge_custom($user_id, $order_id, $amount, $order_cleans_id = null)
    {
        $amount = round($amount, 2) * 100;
        $user = User::findorFail($user_id);
        $order = Order::findorFail($order_id);
        // get customer id from existing record
        $customer_id = $user->stripe_customer_id;
        if ($user && $customer_id) {
            // try to charge
            try {
                // retrive payment method of the customer
                $payment_methods = \Stripe\PaymentMethod::all([
                    'customer' => $customer_id,
                    'type' => 'card',
                ]);

                // try to charge with payment intent
                $charge = \Stripe\PaymentIntent::create([
                    'amount' => $amount,
                    'currency' => strtolower(env('CURRENCY')),
                    'customer' => $customer_id,
                    'off_session' => true,
                    'confirm' => true,

                    'payment_method' => $payment_methods->data[0]->id,
                ]);

                $original_amount = $charge->amount / 100;
                // create transaction
                $insertData = ['user_id' => $user_id, 'order_id' => $order_id, 'stripe_customer_id' => $customer_id, 'transaction_id' => $charge->id, 'original_amount' => $order->total, 'amount' => $original_amount, 'data' => json_encode($charge), 'status' => $charge->status, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'service_date' => $order->date];

                // if order history id sent insert it
                if ($order_cleans_id) {
                    // get order history
                    $order_history = Cleans::findorFail($order_cleans_id);

                    $insertData['order_cleans_id'] = $order_cleans_id;
                    $insertData['service_date'] = $order_history->date;
                }
                $transaction = Transaction::insert($insertData);
                return true;
                //return response()->json($charge);
            } catch (Exception $e) {
                // return response()->json(['success'=> false, 'error'=> 'Error in charging existing customer'], 400);
                return false;
            }
        } else {
            // either user or customer id not found
            // return response()->json(['success'=> false, 'error'=> 'user or stripe_customer_id not found'], 404);
            return false;
        }


    }

    public function createStripeConnectAccount($account_data)
    {
        $stripe_response = [];
        try {
            //code...

            $DEFAULT_COUNTRY = "GB";
            $DEFAULT_CURRENCY = "GBP";
            $bank_details = json_decode($account_data["bank_details"], true);
            $bank_name = (isset($bank_details['name'])) ? $bank_details['name'] : "";
            $bank_beneficiary = (isset($bank_details['beneficiary'])) ? $bank_details['beneficiary'] : "";
            $bank_number = (isset($bank_details['number'])) ? $bank_details['number'] : "";
            // $bank_number = "00012345";
            $bank_code = (isset($bank_details['code'])) ? $bank_details['code'] : "";

            $id_proof_path = $account_data["id_proof_path"];
            $address_proof_path = $account_data["address_proof_path"];

            $stripe_secret_key = env("STRIPE_SECRET_KEY");
            $stripe = new \Stripe\StripeClient($stripe_secret_key);

            // $stripe_res = $stripe->accounts->retrieve('acct_1OtnaeE1cuRATn0i', []);
            // $strip_requirements = $stripe_res->requirements;
            // $strip_requires = $this->getCreateAccountRequirements($strip_requirements);
            // echo "<pre>"; print_r($strip_requires);exit;

            // $fp = fopen($id_proof_path, 'r');
            // $doc_red = $stripe->files->create([
            // 'purpose' => 'identity_document',
            // 'file' => $fp
            // ]);
            // $doc_id = $doc_red->id;


            $res = $stripe->accounts->create([
                'type' => 'custom',
                'business_type' => 'individual',
                'country' => $DEFAULT_COUNTRY,
                'email' => $account_data["email"],
                'individual' => [
                    'address' => ["line1" => $account_data["address"], "postal_code" => $account_data["postcode"], "country" => $DEFAULT_COUNTRY],
                    'dob' => ["day" => date("d", strtotime($account_data["dob"])), "month" => date("m", strtotime($account_data["dob"])), "year" => date("Y", strtotime($account_data["dob"]))],
                    'email' => $account_data["email"],
                    'first_name' => $account_data["firstname"],
                    'last_name' => $account_data["lastname"],
                    'metadata' => ["CLEANER_ID" => $account_data["cleaner_id"]],
                    'phone' => $account_data["phone"],
                    // 'verification' => [
                    //     "document"=>[
                    //         // "back"=>$doc_id,
                    //         "front"=>$doc_id
                    //         ]
                    // ],
                ],
                'metadata' => ["CLEANER_ID" => $account_data["cleaner_id"]],
                'default_currency' => $DEFAULT_CURRENCY,
                'external_account' => [
                    "account_number" => $bank_number,
                    "country" => $DEFAULT_COUNTRY,
                    "currency" => $DEFAULT_CURRENCY,
                    "object" => "bank_account",
                    "account_holder_name" => $bank_beneficiary,
                    "bank_name" => $bank_name,
                    "routing_number" => $bank_code,
                    "account_holder_type" => "individual",
                ],
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'business_profile' => [
                    'url' => "https://cleanmyplace.com"
                ],
                'tos_acceptance' => [
                    'date' => time(),
                    'ip' => \Request::ip()
                ],
            ]);
            // You cannot accept the Terms of Service on behalf of Standard and Express connected accounts.
            // echo "<pre>"; print_r($res);exit;
            $strip_requirements = $res->requirements;
            $strip_requires = $this->getCreateAccountRequirements($strip_requirements);


            $stripe_response["id"] = $res["id"];
            $stripe_response["status"] = "success";
            $stripe_response["message"] = implode(" ", $strip_requires);
        } catch (\Throwable $th) {
            $stripe_response["id"] = "";
            $stripe_response["status"] = "error";
            $stripe_response["message"] = $th->getMessage();
        }

        return $stripe_response;

    }

    public function get_account_balance_retrive($account_id)
    {
        try {
            $stripe_secret_key = env("STRIPE_SECRET_KEY");
            $stripe = new \Stripe\StripeClient($stripe_secret_key);
            $balance = $stripe->balance->retrieve([], ['stripe_account' => $account_id]);
            $stripe_response["data"] = $balance;
            $stripe_response["status"] = "success";
            $stripe_response["message"] = "";
        } catch (\Throwable $th) {
            $stripe_response["data"] = [];
            $stripe_response["status"] = "error";
            $stripe_response["message"] = $th->getMessage();
        }

        return $stripe_response;

    }

    public function get_account_payouts($account_id, $arrival_date_end = null)
    {
        try {
            $stripe_secret_key = env("STRIPE_SECRET_KEY");
            $stripe = new \Stripe\StripeClient($stripe_secret_key);

            // Set the end date to 4 weeks from the current time if not provided
            $arrival_date_end = $arrival_date_end ?? time() + (4 * 7 * 24 * 60 * 60);

            $params = [
                'limit' => 10,
                 'arrival_date' => [
                 'lte' => $arrival_date_end
                 ]
            ];

            $payouts = $stripe->payouts->all($params, ['stripe_account' => $account_id]);

            $stripe_response = [
                "data" => $payouts,
                "status" => "success",
                "message" => ""
            ];
        } catch (\Throwable $th) {
            $stripe_response = [
                "data" => [],
                "status" => "error",
                "message" => $th->getMessage()
            ];
        }

        return $stripe_response;
    }


    public function get_account_balance_transactions_payouts($account_id, $payout_id = null, $created_after = null)
    {
        try {
            // Initialize Stripe with the secret key from the environment
            $stripe_secret_key = env("STRIPE_SECRET_KEY");
            $stripe = new \Stripe\StripeClient($stripe_secret_key);

            // Prepare parameters for the API call
            $params = [
                'type' => "charge", // Filter transactions of type 'charge' or 'payment'
                'limit' => 10         // Limit the number of transactions retrieved
            ];

            // Add payout ID to the parameters if provided
            if (!is_null($payout_id)) {
                $params['payout'] = $payout_id;
            }

            // Filter transactions created after the specified timestamp, if provided
            if (!is_null($created_after) && is_numeric($created_after)) {
                $params['created'] = ['gt' => $created_after];
            }

            // Retrieve balance transactions with specified filters
            $balanceTransactions = $stripe->balanceTransactions->all($params, ['stripe_account' => $account_id]);

            // Prepare the successful response
            $stripe_response = [
                "data" => $balanceTransactions,
                "status" => "success",
                "message" => ""
            ];
        } catch (\Throwable $th) {
            // Handle any exceptions that occur and prepare an error response
            $stripe_response = [
                "data" => [],
                "status" => "error",
                "message" => $th->getMessage()
            ];
        }

        return $stripe_response;
    }


    public function getCreateAccountRequirements($requirements){
        $res = [];
        if(!empty($requirements)){
            $alternatives           = $this->createAccountReqIndividual($requirements->alternatives);
            if(!empty($alternatives)){
                $res[] = "alternatives: ".implode(", ", $alternatives).". ";
            }

            $current_deadline       = $this->createAccountReqIndividual($requirements->current_deadline);
            if(!empty($current_deadline)){
                $res[] = "current_deadline: ".implode(", ", $current_deadline).". ";
            }
            $currently_due          = $this->createAccountReqIndividual($requirements->currently_due);
            if(!empty($currently_due)){
                $res[] = "currently_due: ".implode(", ", $currently_due).". ";
            }
            $disabled_reason        = $requirements->disabled_reason;
            if(!empty($disabled_reason)){
                $res[] = "disabled_reason: ".$disabled_reason.". ";
            }
            $errors                 = $this->createAccountReqIndividual($requirements->errors);
            if(!empty($errors)){
                $res[] = "errors: ".implode(", ", $errors).". ";
            }
            $eventually_due         = $this->createAccountReqIndividual($requirements->eventually_due);
            if(!empty($eventually_due)){
                $res[] = "eventually_due: ".implode(", ", $eventually_due).". ";
            }
            $past_due               = $this->createAccountReqIndividual($requirements->past_due);
            if(!empty($past_due)){
                $res[] = "past_due: ".implode(", ", $past_due).". ";
            }
            $pending_verification   = $this->createAccountReqIndividual($requirements->pending_verification);
            if(!empty($pending_verification)){
                $res[] = "pending_verification: ".implode(", ", $pending_verification).". ";
            }
        }
        return $res;
    }

    public function createAccountReqIndividual($req){
        $res = [];
        if(!empty($req)){
            foreach ($req as $req_value) {
                $res[] = $req_value;
            }
        }
        return $res;
    }
}
