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
use App\Models\Banner;
use App\Models\TaxSetting;
use App\Models\ExtraService;

use App\Models\Cleans;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Validator;

use Unirest;
use Newsletter;

class ApiController extends Controller
{
    public $uploadPath = 'uploads';
    public $UtilityController, $StripeController, $MailController, $PushController;

    public function __construct()
    {
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

   public function writeToLog($message) {
       $logFile = 'logs/apilogs.log';
       $timestamp = date('Y-m-d H:i:s');
       $logMessage = $timestamp . ' - ' . $message . PHP_EOL;
       // Appends the log message to the log file
       //$file = 'logs/testfile.txt';
       //$content = "Test content\n";
       //file_put_contents($file, $content);
       file_put_contents($logFile, $logMessage, FILE_APPEND);
   }

    public function test()
    {
        echo "API test";
    }

    public function test_mail()
    {
        // return Newsletter::getApi()->getLastError();
        // if(!Newsletter::isSubscribed('monsusnighty@gmail.com')){
        Newsletter::subscribe('v1technologiessumandutta@gmail.com');

        // }

    }


    // user =========================================================
    public function user_register(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required',
            // 'token' => 'required',
            // 'device' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $data = [
            'password' => Hash::make($request->post('password')),
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'phone' => $request->post('phone'),
            'token' => $request->post('token'),
            'device' => $request->post('device'),
            'status' => $request->post('status'),
        ];
        $code = mt_rand(100000, 999999);
        $data['code'] = $code;

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // if no status sent unset it, so by default will be 0
        if (!$request->post('status')) {
            unset($data['status']);
        }

        // if registers by social then we save profile image
        if ($request->post('avatar')) {
            $image_content = @file_get_contents($request->post('avatar'));
            $file_name = 'img_' . uniqid() . '.jpg';
            $file_path = $this->uploadPath . '/profile/' . $file_name;
            @file_put_contents($file_path, $image_content);
            $data['image'] = $file_name;
        }
        // if apple login then set apple_id for the first time
        if ($request->post('apple_id')) {
            $data['apple_id'] = $request->post('apple_id');
        }


        try {
            // approve by default, status 1
            $data['status'] = 1;
            // send an email with code
            $user = User::create($data);
            if (!$request->post('status')) {
                //$mail = $this->MailController->mailer('verification', $data['email'], 'Verify your email address', ['name'=> $user->name, 'id'=> $user->id, 'code'=> $user->code]);
            }

            // send welcome email directly
            $mail = $this->MailController->mailer('registration', $user->email, 'Welcome to ' . env('APP_NAME'), ['name' => $user->name]);


            return response()->json($user);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['success' => false, 'error' => 'Registration failed', 'response' => $ex], 400);
        }
    }

    public function user_login(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'email' => 'required',
            'password' => 'required',
            //'device' => 'required',
            // 'token' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $user = User::where(['email' => $request->post('email'), 'status' => 1])->first();
        if ($user) {
            $check = Hash::check($request->post('password'), $user->password);
            if ($check) {
                $update = User::where(['id' => $user->id])->update(['device' => $request->post('device'), 'token' => $request->post('token')]);
                return response()->json($user);
            } else {
                return response()->json(['success' => false, 'error' => 'Invalid login'], 403);
            }
        } else {
            return response()->json(['success' => false, 'error' => 'User not found'], 404);
        }
    }

    public function user_forgot(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'email' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $data = [
            'email' => $request->post('email'),
        ];
        $code = mt_rand(100000, 999999);
        try {
            // send an email with code
            $user = User::where(['email' => $data['email']])->first();
            if ($user) {
                $update = User::where(['id' => $user->id])->update(['code' => $code]);
                $mail = $this->MailController->mailer('forgot', $data['email'], 'Reset password', ['name' => $user->name, 'code' => $code]);
                $data = User::findOrFail($user->id);
                return response()->json($data);
            } else {
                return response()->json(['success' => false, 'error' => 'User not found'], 404);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['success' => false, 'error' => 'Reset failed', 'response' => $ex], 400);
        }
    }

    public function user_verify(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'email' => 'required',
            'code' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $user = User::where(['email' => $request->post('email')])->first();
        if ($user) {
            if ($request->post('code') == $user->code) {
                //correct
                $update = User::where(['id' => $user->id])->update(['status' => 1]);

                // send a welcome mail for registration
                $mail = $this->MailController->mailer('registration', $user->email, 'Welcome to ' . env('APP_NAME'), ['name' => $user->name]);

                return response()->json($user);
            } else {
                return response()->json(['success' => false, 'error' => 'Code does not match'], 403);
            }
        } else {
            return response()->json(['success' => false, 'error' => 'User not found'], 404);
        }
    }

    public function user_reset(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'id' => 'required',
            'password' => 'required',
            'code' => 'required',

        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $user = User::findOrFail($request->post('id'));
        if ($request->post('code') == $user->code) {
            //correct
            $user->password = Hash::make($request->post('password'));
            $user->save();
            return response()->json($user);
        } else {
            return response()->json(['success' => false, 'error' => 'Code does not match'], 403);
        }
    }

    public function user_get($id)
    {
        $user = User::findOrFail($id);
        $user->ratings;
        $user->orders;
        $user->transactions;
        $user->favourites;
        return response()->json($user);
    }

    public function user_update_profile(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'id' => 'required',
            'image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $user = User::findOrFail($request->post('id'));
        if ($request->post('name')) {
            $user->name = $request->post('name');
        }
        if ($request->post('email')) {
            $user->email = $request->post('email');
        }
        if ($request->post('phone')) {
            $user->phone = $request->post('phone');
        }
        if ($request->post('newpassword') && $request->post('oldpassword')) {
            $check = Hash::check($request->post('oldpassword'), $user->password);
            if ($check) {
                $user->password = Hash::make($request->post('newpassword'));
            } else {
                return response()->json(['success' => false, 'error' => 'Old password did not match'], 403);
            }

        }
        if ($request->post('status')) {
            $user->status = $request->post('status');
        }

        if ($request->post('house_number')) {
            $user->house_number = $request->post('house_number');
        }

        // address with postcode work here
        if ($request->post('address') && $request->post('postcode')) {
            // if not the current address or postcode then update else do nothing on address or postcode
            if (($user->address != $request->post('address')) || ($user->postcode != $request->post('postcode'))) {
                $fullAddress = $request->post('address') . ',' . $request->post('postcode');
                // check for geocoding address
                $latlong = $this->UtilityController->getLatLong($fullAddress);
                if (!$latlong) {
                    return response()->json(['success' => false, 'error' => 'Address could not be geocoded'], 404);
                } else {
                    $user->latitude = $latlong->lat;
                    $user->longitude = $latlong->lng;
                    $user->address = $request->post('address');
                    $user->postcode = $request->post('postcode');
                }
            }
        } elseif ($request->post('address') && !$request->post('postcode')) {
            // if not the current address then update else do nothing on address
            if ($user->address != $request->post('address')) {
                $fullAddress = $request->post('address');
                // check for geocoding address
                $latlong = $this->UtilityController->getLatLong($fullAddress);
                if (!$latlong) {
                    return response()->json(['success' => false, 'error' => 'Address could not be geocoded'], 404);
                } else {
                    $user->latitude = $latlong->lat;
                    $user->longitude = $latlong->lng;
                    $user->address = $request->post('address');
                }
            }
        } elseif ($request->post('postcode') && !$request->post('address')) {
            // if not the current postcode then update else do nothing on postcode
            if ($user->postcode != $request->post('postcode')) {
                $fullAddress = $request->post('postcode');
                // check for geocoding address
                $latlong = $this->UtilityController->getLatLong($fullAddress);
                if (!$latlong) {
                    return response()->json(['success' => false, 'error' => 'Address could not be geocoded'], 404);
                } else {
                    $user->latitude = $latlong->lat;
                    $user->longitude = $latlong->lng;
                    $user->postcode = $request->post('postcode');
                }
            }
        }

        $user->save();
        return response()->json($user);
    }

    public function user_bookings($id)
    {
        $orders = Order::with('user')->with('cleaner')->where('user_id', $id)->where('status', '!=', 'pending')->where('status', '!=', 'cancelled-user')->where('status', '!=', 'cancelled-admin')->where('cleaner_id', '!=', NULL)->orderBy('id', 'DESC')->get();
        return response()->json($orders);
    }

    public function user_pending_bookings($id)
    {
        $pendingorders = Order::with('user')->with('cleaner')->where('user_id', $id)->where('user_id', $id)->where('status', 'pending')->get()->all();
        $unconfirmedorders = Order::with('user')->with('cleaner')->where('user_id', $id)->where('user_id', $id)->where('cleaner_id', '=', NULL)->where('status', '!=', 'pending')->where('status', '!=', 'cancelled-user')->where('status', '!=', 'cancelled-admin')->get()->all();
        $orders = array_merge($unconfirmedorders, $pendingorders);

        usort($orders, function ($item1, $item2) {
            return $item2['id'] <=> $item1['id'];
        });
        return response()->json($orders);
    }

    public function user_transactions($id)
    {
        $transactions = Transaction::with(['user', 'cleaner', 'order', 'ordercleans'])->where(['user_id' => $id])->orderBy('id', 'desc')->orderBy('id', 'DESC')->get();
        return response()->json($transactions);
    }


    // cleaner =========================================================
    public function cleaner_register(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email|max:255|unique:cleaners',
            'password' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'postcode' => 'required',
            //'token' => 'required',
            // 'device' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $data = [
            'password' => Hash::make($request->post('password')),
            'name' => $request->post('firstname') . ' ' . $request->post('lastname'),
            'firstname' => $request->post('firstname'),
            'lastname' => $request->post('lastname'),
            'email' => $request->post('email'),
            'phone' => $request->post('phone'),
            'token' => $request->post('token'),
            'device' => $request->post('device'),
            'dob' => date("Y-m-d", strtotime($request->post('dob'))),
        ];
        $code = mt_rand(100000, 999999);
        $data['code'] = $code;
        $data['account_status'] = 'active';
        $data['stripe_acc_id'] = '';

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        try {
            // address with postcode work here
            if ($request->post('address') && $request->post('postcode')) {
                $fullAddress = $request->post('address') . ',' . $request->post('postcode');
                // check for geocoding address
                $latlong = $this->UtilityController->getLatLong($fullAddress);
                if (!$latlong) {
                    return response()->json(['success' => false, 'error' => 'Address could not be geocoded'], 404);
                } else {
                    $data['latitude'] = $latlong->lat;
                    $data['longitude'] = $latlong->lng;
                    $data['address'] = $request->post('address');
                    $data['postcode'] = $request->post('postcode');
                }
            } elseif ($request->post('address') && !$request->post('postcode')) {
                $fullAddress = $request->post('address');
                // check for geocoding address
                $latlong = $this->UtilityController->getLatLong($fullAddress);
                if (!$latlong) {
                    return response()->json(['success' => false, 'error' => 'Address could not be geocoded'], 404);
                } else {
                    $data['latitude'] = $latlong->lat;
                    $data['longitude'] = $latlong->lng;
                    $data['address'] = $request->post('address');
                }
            } elseif ($request->post('postcode') && !$request->post('address')) {
                $fullAddress = $request->post('postcode');
                // check for geocoding address
                $latlong = $this->UtilityController->getLatLong($fullAddress);
                if (!$latlong) {
                    return response()->json(['success' => false, 'error' => 'Address could not be geocoded'], 404);
                } else {
                    $data['latitude'] = $latlong->lat;
                    $data['longitude'] = $latlong->lng;
                    $data['postcode'] = $request->post('postcode');
                }
            }

            // approve by default, status 1
            $data['status'] = 1;
            // send an email with code
            $cleaner = Cleaner::create($data);
            //$mail = $this->MailController->mailer('verification', $data['email'], 'Verify your email address', ['name'=> $cleaner->name, 'id'=> $cleaner->id, 'code'=> $cleaner->code]);

            return response()->json($cleaner);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['success' => false, 'error' => 'Registration failed', 'response' => $ex], 400);
        }
    }

    public function cleaner_login(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'email' => 'required',
            'password' => 'required',
            // 'device' => 'required',
            //'token' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $cleaner = Cleaner::where(['email' => $request->post('email'), 'account_status' => 'active'])->first();
        if ($cleaner) {
            $check = Hash::check($request->post('password'), $cleaner->password);
            if ($check) {
                $update = Cleaner::where(['id' => $cleaner->id])->update(['device' => $request->post('device'), 'token' => $request->post('token')]);
                return response()->json($cleaner);
            } else {
                return response()->json(['success' => false, 'error' => 'Invalid login'], 403);
            }
        } else {
            return response()->json(['success' => false, 'error' => 'Cleaner not found'], 404);
        }
    }

    public function cleaner_forgot(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'email' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $data = [
            'email' => $request->post('email'),
        ];
        $code = mt_rand(100000, 999999);
        try {
            // send an email with code
            $cleaner = Cleaner::where(['email' => $data['email']])->first();
            if ($cleaner) {
                $update = Cleaner::where(['id' => $cleaner->id])->update(['code' => $code]);
                $mail = $this->MailController->mailer('forgot', $data['email'], 'Reset password', ['name' => $cleaner->name, 'code' => $code]);
                $data = Cleaner::findOrFail($cleaner->id);
                return response()->json($data);
            } else {
                return response()->json(['success' => false, 'error' => 'Cleaner not found'], 404);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['success' => false, 'error' => 'Reset failed', 'response' => $ex], 400);
        }
    }

    public function cleaner_verify(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'email' => 'required',
            'code' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $cleaner = Cleaner::where(['email' => $request->post('email')])->first();
        if ($cleaner) {
            if ($request->post('code') == $cleaner->code) {
                //correct
                $update = Cleaner::where(['id' => $cleaner->id])->update(['status' => 1]);

                // send a welcome mail for registration
                // dont send any mail before admin verification
                //$mail = $this->MailController->mailer('registration', $cleaner->email, 'Welcome to '.env('APP_NAME'), ['name'=> $cleaner->name]);

                return response()->json($cleaner);
            } else {
                return response()->json(['success' => false, 'error' => 'Code does not match'], 403);
            }
        } else {
            return response()->json(['success' => false, 'error' => 'Cleaner not found'], 404);
        }
    }

    public function cleaner_reset(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'id' => 'required',
            'password' => 'required',
            'code' => 'required',

        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $cleaner = Cleaner::findOrFail($request->post('id'));
        if ($request->post('code') == $cleaner->code) {
            //correct
            $cleaner->password = Hash::make($request->post('password'));
            $cleaner->save();
            return response()->json($cleaner);
        } else {
            return response()->json(['success' => false, 'error' => 'Code does not match'], 403);
        }
    }

    public function cleaner_get($id)
    {
        $cleaner = Cleaner::findOrFail($id);
        $cleaner->ratings;
        $cleaner->orders;
        $cleaner->transactions;
        $cleaner->schedules;
        return response()->json($cleaner);
    }

    public function cleaner_update_profile(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'id' => 'required',
            'image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $cleaner = Cleaner::findOrFail($request->post('id'));
        if ($request->post('name')) {
            $cleaner->name = $request->post('name');
        }
        if ($request->post('email')) {
            $cleaner->email = $request->post('email');
        }
        if ($request->post('phone')) {
            $cleaner->phone = $request->post('phone');
        }
        if ($request->post('password')) {
            $cleaner->password = Hash::make($request->post('password'));
        }
        if ($request->post('status')) {
            $cleaner->status = $request->post('status');
        }
        if ($request->post('about')) {
            $cleaner->about = $request->post('about');
        }
        if ($request->post('qualification')) {
            $cleaner->qualification = $request->post('qualification');
        }
        if ($request->post('available')) {
            $cleaner->available = $request->post('available');
        }

        if ($request->post('distance')) {
            $cleaner->distance = $request->post('distance');
        }


        // update password
        if ($request->post('newpassword') && $request->post('oldpassword')) {
            $check = Hash::check($request->post('oldpassword'), $cleaner->password);
            if ($check) {
                $cleaner->password = Hash::make($request->post('newpassword'));
            } else {
                return response()->json(['success' => false, 'error' => 'Old password did not match'], 403);
            }

        }

        // address with postcode work here
        if ($request->post('address') && $request->post('postcode')) {
            // if not the current address or postcode then update else do nothing on address or postcode
            if (($cleaner->address != $request->post('address')) || ($cleaner->postcode != $request->post('postcode'))) {
                $fullAddress = $request->post('address') . ',' . $request->post('postcode');
                // check for geocoding address
                $latlong = $this->UtilityController->getLatLong($fullAddress);
                if (!$latlong) {
                    return response()->json(['success' => false, 'error' => 'Address could not be geocoded'], 404);
                } else {
                    $cleaner->latitude = $latlong->lat;
                    $cleaner->longitude = $latlong->lng;
                    $cleaner->address = $request->post('address');
                    $cleaner->postcode = $request->post('postcode');
                }
            }
        } elseif ($request->post('address') && !$request->post('postcode')) {
            // if not the current address then update else do nothing on address
            if ($cleaner->address != $request->post('address')) {
                $fullAddress = $request->post('address');
                // check for geocoding address
                $latlong = $this->UtilityController->getLatLong($fullAddress);
                if (!$latlong) {
                    return response()->json(['success' => false, 'error' => 'Address could not be geocoded'], 404);
                } else {
                    $cleaner->latitude = $latlong->lat;
                    $cleaner->longitude = $latlong->lng;
                    $cleaner->address = $request->post('address');
                }
            }
        } elseif ($request->post('postcode') && !$request->post('address')) {
            // if not the current postcode then update else do nothing on postcode
            if ($cleaner->postcode != $request->post('postcode')) {
                $fullAddress = $request->post('postcode');
                // check for geocoding address
                $latlong = $this->UtilityController->getLatLong($fullAddress);
                if (!$latlong) {
                    return response()->json(['success' => false, 'error' => 'Address could not be geocoded'], 404);
                } else {
                    $cleaner->latitude = $latlong->lat;
                    $cleaner->longitude = $latlong->lng;
                    $cleaner->postcode = $request->post('postcode');
                }
            }
        }

        $cleaner->save();
        return response()->json($cleaner);
    }

    //Clean My Place Cleaner Booking
    public function cleaner_bookings($id)
    {
        $currentDate = date('Y-m-d');

        $this->writeToLog("cleaner bookings called");

        // Fetching all cleans for the cleaner, then filtering them based on status and date
        $cleans = Cleans::with(['order', 'order.cleaner', 'order.user'])
            ->where('cleaner_id', $id)
            ->get()
            ->groupBy(function($clean) use ($currentDate) {
                if ($clean->status === 'completed') {
                    return 'completed';
                } elseif ($clean->status === 'pending' && $clean->date > $currentDate) {
                    return 'upcoming';
                } elseif ($clean->status === 'pending' && $clean->date <= $currentDate) {
                    return 'pending';
                }
                // Missed or any other status are not included
            })->toArray();

        $upcoming_new = $cleans['upcoming'] ?? [];
        $completed_new = $cleans['completed'] ?? [];
        $pending_new = $cleans['pending'] ?? [];

        // Optionally limit the number of upcoming cleans
        $UPCOMING_CLEANS_LIMIT = env("UPCOMING_CLEANS_LIMIT");
        $upcoming_new = array_slice($upcoming_new, 0, $UPCOMING_CLEANS_LIMIT, true);

        return response()->json([
            'upcoming' => $upcoming_new,
            'completed' => $completed_new,
            'pending' => $pending_new
        ]);
    }

    public function cleaner_pending_bookings($id)
    {
        $orders = Order::where('cleaner_id', $id)->where('status', 'pending')->orderBy('id', 'DESC')->get();
        return response()->json($orders);
    }

    public function cleaner_schedule($cleaner_id)
    {
        $res = Schedule::where(['cleaner_id' => $cleaner_id])->get();
        $arr = ['mon' => [], 'tue' => [], 'wed' => [], 'thu' => [], 'fri' => [], 'sat' => [], 'sun' => []];

        // now group the results
        foreach ($res as $single) {
            switch ($single->day) {
                case 'mon':
                    array_push($arr['mon'], $single);
                    break;
                case 'tue':
                    array_push($arr['tue'], $single);
                    break;
                case 'wed':
                    array_push($arr['wed'], $single);
                    break;
                case 'thu':
                    array_push($arr['thu'], $single);
                    break;
                case 'fri':
                    array_push($arr['fri'], $single);
                    break;
                case 'sat':
                    array_push($arr['sat'], $single);
                    break;
                case 'sun':
                    array_push($arr['sun'], $single);
                    break;
            }
        }
        return response()->json($arr);
    }

    public function cleaner_transactions($id, Request $request)
    {
        $js_months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        if ($request->post('month')) {
            $monthExample = date('Y') . '-' . $js_months[$request->post('month')] . '-' . date('d');
        } else {
            // current month
            $monthExample = date('Y-m-d');
        }
        $yearExample = date('Y') . '-01-01';

        // get transactions based on service date from transactions table
        $transactions = Transaction::with(['cleaner', 'user', 'order'])->where('cleaner_id', $id)->whereRaw('MONTH(service_date) = MONTH(\'' . $monthExample . '\') AND YEAR(service_date) = YEAR(\'' . $yearExample . '\')')->where('order_status', 'complete')->orderBy('id', 'desc')->get();

        // get number of full refunded orders
        $total_number_of_full_refunded_orders = Transaction::where('cleaner_id', $id)->where('status', 'refunded')->whereRaw('MONTH(service_date) = MONTH(\'' . $monthExample . '\') AND YEAR(service_date) = YEAR(\'' . $yearExample . '\')')->where('order_status', 'complete')->count();


        // get partial refund fee percentage
        $partial_percentage = Setting::where(['key' => 'paid_cancellation_charge'])->first();

        $total = 0;
        $full_refunds = 0;
        $partial_refunds = 0;

        $total_count = count($transactions);
        $full_refunds_count = 0;
        $partial_refunds_count = 0;

        foreach ($transactions as $transaction) {
            if ($transaction->original_amount) {
                $total += $transaction->original_amount;
            }
            if ($transaction->status == 'refunded') {
                $full_refunds += $transaction->original_amount;
                $full_refunds_count++;
            } else if ($transaction->status == 'partial-refunded') {
                if ($partial_percentage) {
                    $partial_percentage = $partial_percentage->value;
                    $refunded = intval(($transaction->original_amount * $partial_percentage) / 100);
                } else {
                    // deduct 25% static
                    $refunded = intval(($transaction->original_amount * 25) / 100);
                }
                $partial_refunds += $refunded;
                $partial_refunds_count++;
            }
        }

        $final_payout = $total - ($full_refunds + $partial_refunds);

        // first get commision type
        $commission_type = Setting::where(['key' => 'commission_type'])->first();
        if (!$commission_type) {
            $commission_type = 'percentage';
        } else {
            $commission_type = $commission_type->value;
        }

        $commission = Setting::where(['key' => 'commission'])->first();
        if ($commission) {
            $commission = $commission->value;
            // deduct commission fee
            if ($commission_type == 'percentage') {
                // percentage
                $total = floatval($total - ($total * $commission) / 100);
                $full_refunds = floatval($full_refunds - (($full_refunds * $commission) / 100));
                $partial_refunds = floatval($partial_refunds - (($partial_refunds * $commission) / 100));
                $final_payout = ($total - ($full_refunds + $partial_refunds) > 0) ? floatval($total - ($full_refunds + $partial_refunds)) : 0;
            } else {
                // flat fee applicable
                $total_fee = (($transactions->count() - $total_number_of_full_refunded_orders) * $commission);

                $total = ($total > 0) ? floatval($total - $total_fee) : $total;
                $full_refunds = floatval($full_refunds);
                $partial_refunds = floatval($partial_refunds);
                $final_payout = ($total - ($full_refunds + $partial_refunds) > 0) ? floatval($total - ($full_refunds + $partial_refunds)) : 0;
            }
        } else {
            // no commission deducted
            $total = $total;
            $full_refunds = $full_refunds;
            $partial_refunds = $partial_refunds;
            $final_payout = ($total - ($full_refunds + $partial_refunds) > 0) ? floatval($total - ($full_refunds + $partial_refunds)) : 0;
        }


        // check if already paid or not
        $paid = 0;
        $payment_status = Payout::where(['month' => $js_months[$request->post('month')], 'year' => date('Y'), 'cleaner_id' => $id])->first();
        if ($payment_status) {
            // paid till now
            $paid = $payment_status->amount;
            $payment_status = $payment_status->status;
        } else {
            // no record found so unpaid by default
            $payment_status = 'pending';
        }

        return response()->json(['total' => $total, 'total_transactions' => $total_count, 'full_refunds' => $full_refunds, 'full_refunds_transactions' => $full_refunds_count, 'partial_refunds' => $partial_refunds, 'partial_refunds_transactions' => $partial_refunds_count, 'payout' => $final_payout, 'payment_status' => $payment_status, 'paid' => $paid]);
    }

    public function cleaner_check_verification($id, Request $request)
    {
        $data = CleanerData::where(['cleaner_id' => $id])->first();
        if ($data) {
            if ($data->status == 'pending') {
                // wait for verification
                return response()->json(['error' => true, 'message' => 'Please wait untill document vericification is done'], 401);
            } elseif ($data->status == 'rejected') {
                // reupload data
                return response()->json(['error' => true, 'message' => 'Need to resubmit details', 'reason' => $data->reason], 403);
            } elseif ($data->status == 'accepted') {
                return response()->json($data);
            }
        } else {
            // no record found
            return response()->json(['error' => true, 'message' => 'Need to submit details'], 404);
        }
    }

    public function cleaner_updatedocument($id, Request $request)
    {
        $cleaner = Cleaner::findOrFail($id);
        $data = CleanerData::where(['cleaner_id' => $id])->first();
        if ($cleaner && $request->post('distance')) {
            $cleaner->distance = $request->post('distance');
            $cleaner->save();
        }
        if ($data) {
            // update record
            $res = CleanerData::where(['cleaner_id' => $id])->update(['bank_details' => json_encode($request->post('bank')), 'experience' => $request->post('experience'), 'status' => 'pending']);
        } else {
            // insert record
            $res = CleanerData::insert(['cleaner_id' => $id, 'bank_details' => json_encode($request->post('bank')), 'experience' => $request->post('experience'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        }

        // inform admin to know about awaiting verification
        $verification_email = Setting::where(['key' => 'verification_email'])->first();
        $verification_email = $verification_email->value;
        $subject = 'Awaiting document verification';

        $this->MailController->mailer('default', $verification_email, $subject, ['name' => 'Admin', 'text' => 'A new cleaner has joined with documents awaiting verification']);

        return response()->json($res);
    }

    // Global =========================================================
    public function order_details($id)
    {
        $order = Order::with('user')->with('cleaner')->with('transactions')->with('rating')->with('cleans')->with('notifications')->with('notifications.cleaner')->where(['id' => $id])->first();
        return response()->json($order);
    }

    public function cleans_details($id)
    {
        $order = Cleans::with(['order', 'order.user', 'order.cleaner'])->where(['id' => $id])->first();
        if(!empty($order)){
            $order->order->can_change_status = ($order->status == "pending" && strtotime($order->date) <= time())?1:0;
        }
        return response()->json($order);
    }

    //Clean My Place order Create
    public function order_create(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'user_id' => 'required',
            'address' => 'required',
            'postcode' => 'required',
            'date' => 'required',
            'hours' => 'required',
            'total' => 'required',
            'price' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }

        $post = $request->all();
        if ($request->post('address') && $request->post('postcode')) {
            $fullAddress = $request->post('address') . ',' . $request->post('postcode');
            $latlong = $this->UtilityController->getLatLong($fullAddress);
            if (!$latlong) {
                return response()->json(['success' => false, 'error' => 'Please provide a valid address & postcode'], 403);
            } else {
                $post['latitude'] = $latlong->lat;
                $post['longitude'] = $latlong->lng;
            }
        } elseif ($request->post('address') && !$request->post('postcode')) {
            $fullAddress = $request->post('address');
            $latlong = $this->UtilityController->getLatLong($fullAddress);
            if (!$latlong) {
                return response()->json(['success' => false, 'error' => 'Please provide a valid address'], 403);
            } else {
                $post['latitude'] = $latlong->lat;
                $post['longitude'] = $latlong->lng;
            }
        } elseif ($request->post('postcode') && !$request->post('address')) {
            $fullAddress = $request->post('postcode');
            $latlong = $this->UtilityController->getLatLong($fullAddress);
            if (!$latlong) {
                return response()->json(['success' => false, 'error' => 'Please provide a valid postcode'], 403);
            } else {
                $post['latitude'] = $latlong->lat;
                $post['longitude'] = $latlong->lng;
            }
        }

        // For Testing Purpose
        // $post['latitude'] = "21.8954503";
        // $post['longitude'] = "70.4904404";

        $hours = $request->post('hours');
        $post['price'] = $request->post('price');
        $post['total'] = $request->post('total');
        $post['time_slots'] = json_encode($request->post('time_slots'));
        $post['created_at'] = date('Y-m-d H:i:s');
        $post['updated_at'] = date('Y-m-d H:i:s');

        $order = Order::insertGetId($post);
        $order = Order::findOrFail($order);
        // $order->user;

        $this->order_status_update($order->id, 'paid');
        return response()->json($order);
    }

    //Clean My Place order Status Change
    public function order_status_update($id, $status)
    {
        $order = Order::findOrFail($id);
        $stamp = strtotime($order->date . ' ' . $order->time);
        $interval = '+' . $order->frequency_days . ' day';
        $next_date = date('Y-m-d H:i:s', strtotime($interval, $stamp));
        $date = date('Y-m-d H:i:s', strtotime($order->date . ' ' . $order->time));

        // Insert the initial clean (applies to both one-off and repeat orders)
        Cleans::insert([
            'order_id' => $order->id,
            'date' => $date,
            'time' => $order->time,
            'cleaner_id' => $order->cleaner_id,
            'frequency' => $order->frequency,
            'customer_id' => $order->user_id,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // For repeat orders, insert additional cleans based on the order's frequency
        if($order->frequency != "oneoff"){
            $REPEAT_CLEANS_ADD_IN_ADVANCED = env("REPEAT_CLEANS_ADD_IN_ADVANCED");
            for ($i = 1; $i <= $REPEAT_CLEANS_ADD_IN_ADVANCED; $i++) {
                $interval = '+' . $order->frequency_days * $i . ' day';
                $date = date('Y-m-d', strtotime($interval, $stamp));

                Cleans::insert([
                    'order_id' => $order->id,
                    'date' => $date,
                    'time' => $order->time,
                    'cleaner_id' => $order->cleaner_id,
                    'frequency' => $order->frequency,
                    'customer_id' => $order->user_id,
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        $order->status = $status;
        $order->payment_status = $status;
        $cleaners = $this->get_nearest_cleaners($order->id);
        foreach ($cleaners as $cleaner) {
            Notification::insert(['user_id' => $order->user_id, 'cleaner_id' => $cleaner->id, 'order_id' => $order->id, 'date' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $this->MailController->mailer('ordercreated', $cleaner->email, env('APP_NAME') . ': New Order Request', ['id' => $order->id, 'name' => $cleaner->name, 'order_details' => $order]);
        }
        $this->PushController->send_notification_cleaner($order->id, $cleaners, 'nearby');
        $user = User::findOrFail($order->user_id);
        // $this->MailController->mailer('default', $user->email, env('APP_NAME').': New Order Request', ['name'=> $user->name, 'text'=> 'New order created successfully!']);

        $order->save();
        return response()->json($order);
    }

    public function order_status_change(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'id' => 'required',
            'status' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $order = Order::findOrFail($request->post('id'));
        // if status = paid & frequency != oneoff then create subscription if not has
        if ($request->post('status') == 'paid') {
            if ($order->frequency != 'oneoff') {
                // check for existing subscription
                //calculate next service date
                $stamp = strtotime($order->date . ' ' . $order->time);
                if ($order->frequency == 'weekly') {
                    $interval = '+1 week';
                } else if ($order->frequency == 'biweekly') {
                    $interval = '+2 weeks';
                } else if ($order->frequency == 'monthly') {
                    $interval = '+1 month';
                } else if ($order->frequency == 'daily') {
                    $interval = '+1 day';
                }

                // new: insert order history for next 1 year for subscriptions || for daily 3 months
                $REPEAT_CLEANS_ADD_IN_ADVANCED = env("REPEAT_CLEANS_ADD_IN_ADVANCED");
                if ($order->frequency == 'weekly') {
                    // weekly
                    for ($i = 1; $i <= $REPEAT_CLEANS_ADD_IN_ADVANCED; $i++) {
                        $interval = '+' . $i . ' week';
                        $date = date('Y-m-d', strtotime($interval, $stamp));
                        Cleans::insert([
                                'order_id' => $order->id,
                                'date' => $date,
                                'time' => $order->time,
                                'cleaner_id' => $order->cleaner_id,
                                'frequency' => $order->frequency,
                                'customer_id' => $order->user_id,
                                'status' => 'pending',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                    }
                } elseif ($order->frequency == 'biweekly') {
                    // bi weekly
                    for ($i = 1; $i <= $REPEAT_CLEANS_ADD_IN_ADVANCED; $i++) {
                        // if ($i % 2 == 0) {
                            $interval = '+' . ($i*2) . ' week';
                            $date = date('Y-m-d', strtotime($interval, $stamp));
                            Cleans::insert([
                                'order_id' => $order->id,
                                'date' => $date,
                                'time' => $order->time,
                                'cleaner_id' => $order->cleaner_id,
                                'frequency' => $order->frequency,
                                'customer_id' => $order->user_id,
                                'status' => 'pending',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        // }
                    }
                } elseif ($order->frequency == 'monthly') {
                    // monthly
                    for ($i = 1; $i <= $REPEAT_CLEANS_ADD_IN_ADVANCED; $i++) {
                        $interval = '+' . $i . ' month';
                        $date = date('Y-m-d', strtotime($interval, $stamp));
                        Cleans::insert([
                            'order_id' => $order->id,
                            'date' => $date,
                            'time' => $order->time,
                            'cleaner_id' => $order->cleaner_id,
                            'frequency' => $order->frequency,
                            'customer_id' => $order->user_id,
                            'status' => 'pending',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                } elseif ($order->frequency == 'daily') {
                    // daily
                    for ($i = 1; $i <= $REPEAT_CLEANS_ADD_IN_ADVANCED; $i++) {
                        $interval = '+' . $i . ' day';
                        $date = date('Y-m-d', strtotime($interval, $stamp));
                        Cleans::insert([
                            'order_id' => $order->id,
                            'date' => $date,
                            'time' => $order->time,
                            'cleaner_id' => $order->cleaner_id,
                            'frequency' => $order->frequency,
                            'customer_id' => $order->user_id,
                            'status' => 'pending',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
                $order->status = $request->post('status');
                $order->payment_status = $request->post('status');
            } else {
                // for oneoff only update status
                $order->status = $request->post('status');
                $order->payment_status = $request->post('status');
            }
            // TODO: send notification to nearest cleaners if no cleaner selected else send notification to that cleaner
            if (!$order->cleaner_id) {
                $cleaners = $this->get_nearest_cleaners($order->id);
                // log order notifications
                foreach ($cleaners as $cleaner) {
                    Notification::insert(['user_id' => $order->user_id, 'cleaner_id' => $cleaner->id, 'order_id' => $order->id, 'date' => $order->date, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                }
                // send push
                $this->PushController->send_notification_cleaner($order->id, $cleaners, 'nearby');
            } else {
                $cleaner = Cleaner::findOrFail($order->cleaner_id);
                $cleaners_arr = [];
                array_push($cleaners_arr, $cleaner);
                $this->PushController->send_notification_cleaner($order->id, $cleaners_arr, 'selected');

                // create order notification for that cleaner only
                Notification::insert(['user_id' => $order->user_id, 'cleaner_id' => $order->cleaner_id, 'order_id' => $order->id, 'date' => $order->date, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

                // == ## According to new logic cleaner needs to accept or reject so removing the selected cleaner again from order
                $order->cleaner_id = NULL;

            }

        } elseif ($request->post('status') == 'completed' || $request->post('status') == 'accepted') {
            $order->status = $request->post('status');
            // TODO: send notification to user;
            // TODO: send notification to user about rating the order
            $this->PushController->send_notification_user($order->id, $request->post('status'));

            // if status is completed silently charge for next schedule
            if ($request->post('status') == 'completed') {
                $this->charge_for_upcoming($order->id);

                // ****** update the transaction's order status to complete & also update the cleaner's id into the transaction(payout for only completed orders)
                Transaction::where('order_id', $order->id)->whereNull('order_cleans_id')->update(['order_status' => 'complete', 'cleaner_id' => $order->cleaner_id]);
            }
        }
        // save the changes
        $order->save();
        return response()->json($order);
    }

    public function history_status_change(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $cleansHistory = Cleans::with('order')->findOrFail($request->post('id'));
        $cleansHistory->status = 'completed';
        $cleansHistory->save();

        // Create New Cleans on Completing Current Cleans


        // TODO: send notification to user
        $this->PushController->send_notification_user($cleansHistory->order_id, 'history_completed');

        // if status is completed silently charge for next schedule
        $this->charge_for_upcoming($cleansHistory->order->id);

        // ****** update the transaction's order status to complete & update the current cleaner in transaction (payout for only completed order histories)
        Transaction::where(['order_cleans_id' => $request->post('id')])->update(['order_status' => 'complete', 'cleaner_id' => $cleansHistory->order->cleaner_id]);

        return response()->json($cleansHistory);
    }

    public function accept_order(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'order_id' => 'required',
            'cleaner_id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }

        $orderDetails = Order::findOrFail($request->post('order_id'));
        if ($orderDetails->status == 'accepted' || $orderDetails->status == 'completed') {
            return response()->json(['success' => false, 'error' => 'This order is no longer available'], 403);
        }

        // get required timings
        $required_timings = [];
        $orderTimeSlot = (strlen($orderDetails->time) == 5)?$orderDetails->time.":00":$orderDetails->time;
        // $start_position = array_search($orderTimeSlot, array_keys($this->UtilityController::$Times));
        $start_position = "";
        $start_position = array_search($orderTimeSlot, $this->UtilityController::$Times);
        for ($i = 0; $i < $orderDetails->hours; $i++) {
            array_push($required_timings, $this->UtilityController::$Times[$start_position + $i]);
        }

        // get schedules of the selected day
        $strtotime = strtotime($orderDetails->date);
        $day_name = strtolower(date('D', $strtotime));

        // check the day's timings of the cleaner
        // echo $day_name;exit;
        $timings = Schedule::where(['day' => $day_name, 'cleaner_id' => $request->post('cleaner_id')])->get();
        // echo "<pre>"; print_r($timings);exit;

        $schedule_timings = [];
        foreach ($timings as $time) {
            array_push($schedule_timings, $time->start);
        }


        // check if the required timings include cleaner's schedule
        // echo "<pre>"; print_r($schedule_timings);exit;
        // echo "<pre>"; print_r($required_timings);exit;

        foreach ($required_timings as $item) {
            if (!in_array($item, $schedule_timings)) {
                // time is not available for booking
                return response()->json(['success' => false, 'error' => 'The time is not available in your schedule'], 403);
            }
        }


        // check the day's normal orders
        $orders = Order::where('date', $orderDetails->date)->where('cleaner_id', $request->post('cleaner_id'))->where(function ($query) {
            $query->orWhere('status', 'paid');
            $query->orWhere('status', 'accepted');
        })->get();

        // echo "<pre>"; print_r($orders);exit;

        // get booked times
        $booked_times = [];
        foreach ($orders as $order) {
            $start_position = array_search($order->time, array_keys($this->UtilityController::$Times));
            for ($i = 0; $i < $order->hours; $i++) {
                array_push($booked_times, $this->UtilityController::$Times[$start_position + $i]);
            }
        }

        // check if alreday the time slot for the day booked or not
        if (count($orders) > 0) {
            // order exists for same day check further for remaining available times
            foreach ($required_timings as $required) {
                if (in_array($required, $booked_times)) {
                    return response()->json(['success' => false, 'error' => 'You already have another booking'], 403);
                }
            }
        }


        // ================= get cleaner's subscriptions to check if the required timing has any other recurring orders or not

        // check the day's recurring orders
        $ordercleans = Cleans::where('date', $orderDetails->date)
            ->with('order')->whereHas('order', function ($query) use ($request) {
                $query->where('cleaner_id', $request->post('cleaner_id'));
            })->get();

        // get booked times
        $booked_times = [];
        foreach ($ordercleans as $orderclean) {
            $start_position = array_search($orderclean->order->time, array_keys($this->UtilityController::$Times));
            for ($i = 0; $i < $orderclean->order->hours; $i++) {
                array_push($booked_times, $this->UtilityController::$Times[$start_position + $i]);
            }
        }

        // check if alreday the time slot for the day booked or not
        if (count($ordercleans) > 0) {
            // order exists for same day check further for remaining available times
            foreach ($required_timings as $required) {
                if (in_array($required, $booked_times)) {
                    return response()->json(['success' => false, 'error' => 'You already have another recurring booking'], 403);
                }
            }
        }

        // assign cleaner to order
        $order = Order::findOrFail($request->post('order_id'));
        $order->cleaner_id = $request->post('cleaner_id');
        $order->status = 'accepted';
        $order->save();

        // update cleaner to transaction
        Transaction::where(['order_id' => $request->post('order_id')])->update(['cleaner_id' => $request->post('cleaner_id')]);

        // Update Cleans as We are creating Cleans on Order Create and We don't have cleaner_id on Order Create
        Cleans::where(['order_id' => $request->post('order_id')])->update(['cleaner_id' => $request->post('cleaner_id'), 'updated_at' => date('Y-m-d H:i:s')]);

        // delete from notification log
        Notification::where(['order_id' => $request->post('order_id')])->delete();

        // TODO: Send notification to user
        $this->PushController->send_notification_user($order->id, 'accepted');

        return response()->json($order);
    }

    public function accept_request(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'order_id' => 'required',
            'cleaner_id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }

        $orderDetails = order::findOrFail($request->post('order_id'));
        if ($orderDetails->status == 'pending' || $orderDetails->status == 'accepted' || $orderDetails->status == 'completed') {
            return response()->json(['success' => false, 'error' => 'This order is no longer available'], 403);
        }
        $order = Order::findOrFail($request->post('order_id'));
        Notification::where(['order_id' => $request->post('order_id'), 'cleaner_id' => $request->post('cleaner_id')])->update(['notification_status' => 'active']);
        // $this->PushController->send_notification_user_request($request->post('order_id'), $request->post('cleaner_id'));

        return response()->json($order);
    }

    public function cancel_order(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'order_id' => 'required',
            'status' => 'required',
            //'reason' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }

        $order = Order::findOrFail($request->post('order_id'));
        // if status == cancelled-cleaner or cancelled-user & last status == paid then refund
        if (($request->post('status') == 'cancelled-user' || $request->post('status') == 'cancelled-cleaner')) {
            if ($request->post('status') == 'cancelled-user') {
                // status = paid || accepted then refund the user
                if ($order->status == 'paid' || $order->status == 'accepted') {
                    // TODO: Refund the user
                    // get the payment intenet from transaction
                    $transaction = Transaction::where(['order_id' => $order->id, 'user_id' => $order->user_id])->first();
                    if ($transaction) {
                        // if before free_cancellation_days then full refund else deduct {{paid_cancellation_charge}}% charge
                        $free_cancellation_days = Setting::where(['key' => 'free_cancellation_days'])->first();
                        $paid_cancellation_hours = Setting::where(['key' => 'paid_cancellation_hours'])->first();

                        if ($free_cancellation_days) {
                            $free_cancellation_days = $free_cancellation_days->value;

                            $service_date = strtotime($order->date . ' ' . $order->time);
                            $now = time();

                            if (($service_date - $now) > (86400 * $free_cancellation_days)) {
                                // initiate full refund
                                $this->StripeController->refund($transaction->transaction_id, 'full');

                            } else {
                                // if before {{paid_cancellation_hours}} deduct {{paid_cancellation_charge}}% and refund the rest else no refund
                                if ($paid_cancellation_hours) {
                                    $paid_cancellation_hours = $paid_cancellation_hours->value;
                                    if (($service_date - $now) > (3600 * $paid_cancellation_hours)) {
                                        // partial
                                        $this->StripeController->refund($transaction->transaction_id, 'partial');
                                    } else {
                                        // no refund as time is over to cancel
                                    }
                                } else {
                                    $this->StripeController->refund($transaction->transaction_id, 'partial');
                                }
                            }

                        } else {
                            // initiate full refund
                            $this->StripeController->refund($transaction->transaction_id, 'full');
                        }
                    } else {
                        // no transaction found hence no refund required from stripe
                    }

                    $order->payment_status = $request->post('refunded');

                } else {
                    // only subscriptions need to cancel/delete
                }

                // TODO: send notification to user & cleaner
                if ($order->cleaner_id) {
                    $cleaner = Cleaner::findOrFail($order->cleaner_id);
                    $cleaners_arr = [];
                    array_push($cleaners_arr, $cleaner);
                    $this->PushController->send_notification_cleaner($order->id, $cleaners_arr, 'cancelled');
                }
                $this->PushController->send_notification_user($order->id, 'cancelled');
            } else {
                // if cancelled-cleaner then it goes to admin for reassign new cleaner
            }

            // for pending & cancelled-user & cancelled-cleaner change status and inactive subscription if any exists
            $order->status = $request->post('status');
            if ($request->post('reason')) {
                $order->reason = $request->post('reason');
            }
            // check for existing subscription
                // ============ new added

            // if already paid for future upcoming task then we need to refund those also
            $paid_future_order_histories = Cleans::where('order_id', $order->id)->where('status', 'paid')->where('date', '>=', date('Y-m-d'))->get();
            foreach ($paid_future_order_histories as $order_history) {
                // now check for transaction
                $trns = Transaction::where('order_cleans_id', $order_history->id)->where('status', 'succeeded')->first();
                if ($trns) {
                    // full refund
                    $this->StripeController->refund($trns->transaction_id, 'full');
                }
            }

            // =========== end

            // if cancelled by user, then we can remove subscription history
            if ($request->post('status') == 'cancelled-user') {
                // delete pending order histories to cleanup the database
                Cleans::where(['order_id' => $order->id, 'status' => 'pending'])->delete();
            }
            $order->save();
            return response()->json($order);
        } else {
            return response()->json(['success' => false, 'error' => 'invalid status sent'], 403);
        }

    }

    public function addrating(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'user_id' => 'required',
            'cleaner_id' => 'required',
            'order_id' => 'required',
            'rating' => 'required',
            // 'comment' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $data = $request->all();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $rating = Rating::insert($data);

        // update cleaner's average rating automatically
        $data = Rating::where(['cleaner_id' => $request->post('cleaner_id')])->get();
        $total = 0;
        foreach ($data as $single) {
            $total += $single->rating;
        }
        $count = count($data);
        $avg = ($total / $count);
        $avg = number_format((float)$avg, 2, '.', '');
        $cleaner = Cleaner::findOrFail($request->post('cleaner_id'));
        $cleaner->rating = $avg;
        $cleaner->save();

        return response()->json($rating);
    }

    public function get_ratings($cleaner_id)
    {
        $ratings = Rating::with('user')->with('cleaner')->with('order')->where(['cleaner_id' => $cleaner_id])->get();
        return response()->json($ratings);
    }

    public function schedule_create(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'cleaner_id' => 'required',
            'day' => 'required',
            'start' => 'required',
            'end' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }

        $times = $this->UtilityController::$Times;

        $flag = 0;
        $tempTimes = [];
        foreach ($times as $time) {
            if ($request->post('start') == $time) {
                $flag = 1;
            }
            if ($flag == 1) {
                array_push($tempTimes, $time);
            }
            if ($request->post('end') == $time) {
                $flag = 0;
            }
        }

        // delete previous records
        $del = Schedule::where(['cleaner_id' => $request->post('cleaner_id'), 'day' => $request->post('day')])->delete();

        // remove the last time from array as upto that time will be created records
        array_pop($tempTimes);
        // insert new records
        foreach ($tempTimes as $single) {
            $start_position = array_search($single, array_keys($times));
            $end_position = $start_position + 1;
            $end = $times[$end_position];
            $ins = Schedule::insert(['cleaner_id' => $request->post('cleaner_id'), 'day' => $request->post('day'), 'start' => $single, 'end' => $end, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        }

        $schedules = Schedule::where(['cleaner_id' => $request->post('cleaner_id')])->get();
        return response()->json($schedules);
    }

    public function schedule_delete(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'cleaner_id' => 'required',
            'id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $res = Schedule::where(['id' => $request->post('id'), 'cleaner_id' => $request->post('cleaner_id')])->delete();
        if ($res) {
            return response()->json($res);
        } else {
            return response()->json(['success' => false, 'error' => 'Error deleting record'], 403);
        }
    }

    public function get_price($hour)
    {
        $price = Setting::where(['key' => 'price_' . $hour . '_hour'])->first();
        if ($price) {
            return response()->json(['price' => $price->value]);
        } else {
            return response()->json(['success' => false, 'error' => 'Price not found'], 404);
        }

    }

    public function get_settings()
    {
        $data = Setting::all();
        $currency = $this->UtilityController::Currencies()[env('CURRENCY')];
        $arr = [];
        foreach ($data as $item) {
            $arr[$item['key']] = $item['value'];
        }
        $arr['currency_symbol'] = $currency;
        $arr['currency'] = env('CURRENCY');


        $arr['STRIPE_PAYMENT_URL'] = env('APP_URL') . '/processpayment';
        $arr['STRIPE_SUCCESS_PAYMENT_URL'] = env('APP_URL') . '/paymentsuccess';
        $arr['STRIPE_FAILED_PAYMENT_URL'] = env('APP_URL') . '/paymentfailed';
        $arr['STRIPE_CARD_UPDATE_URL'] = env('APP_URL') . '/cardupdate';
        $arr['CHAT_MODULE_ENABLED'] = env('CHAT_MODULE_ENABLED');

        return response()->json($arr);
    }

    public function get_tax_settings()
    {
        $data = TaxSetting::orderBy('priority', 'desc')->get();
        return response()->json($data);
    }

    public function get_extra_services()
    {
        $data = ExtraService::orderBy('priority', 'desc')->get();
        return response()->json($data);
    }

    // favourites
    public function favourite_create(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'user_id' => 'required',
            'cleaner_id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }

        $favourite = Favourite::insert(['user_id' => $request->post('user_id'), 'cleaner_id' => $request->post('cleaner_id'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        return response()->json(['success' => true, 'message' => 'favourites added']);
    }

    public function favourite_delete(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'cleaner_id' => 'required',
            'user_id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }

        $favourite = Favourite::where(['cleaner_id' => $request->post('cleaner_id'), 'user_id' => $request->post('user_id')])->first();
        if ($favourite) {
            $delete = Favourite::findOrFail($favourite->id);
            $delete->delete();
            // get all favourites of the user
            $favs = Favourite::with('cleaner')->where(['user_id' => $request->post('user_id')])->get();
            return response()->json($favs);
        } else {
            return response()->json(['success' => false, 'error' => 'Invalid id or unauthorised'], 403);
        }
    }

    public function favourite_get($user_id)
    {
        $favs = Favourite::with('cleaner')->where(['user_id' => $user_id])->get();
        $orders = Order::with('cleaner')->where(['user_id' => $user_id, 'status' => 'completed'])->get();
        $past_orders = $orders->unique('cleaner_id');

        $fav_cleaners = [];
        foreach ($favs as $fav) {
            $fav->cleaner->address = null; //hide cleaner address
            array_push($fav_cleaners, $fav->cleaner);
        }

        $fav_cleaner_ids = [];
        foreach ($favs as $fav) {
            array_push($fav_cleaner_ids, $fav->cleaner_id);
        }
        $past_cleaners = [];
        foreach ($past_orders as $order) {
            if (in_array($order->cleaner_id, $fav_cleaner_ids)) {
                $order->cleaner->address = null; // hide cleaner address
                $temp = $order->cleaner;
                $temp->is_favourite = true;
                array_push($past_cleaners, $temp);
            } else {
                $order->cleaner->address = null; // hide cleaner address
                $temp = $order->cleaner;
                $temp->is_favourite = false;
                array_push($past_cleaners, $temp);
            }
        }

        $output = [
            'favourite' => $fav_cleaners,
            'past' => $past_cleaners
        ];
        return response()->json($output);
    }

    // image uploading

    public function update_user_image($user_id, Request $request)
    {
        $user = User::findOrFail($user_id);
        // file upload tasks
        if ($file = $request->file('image')) {
            $image_path = 'img_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $upload = $file->move($this->uploadPath . '/profile', $image_path);
            // check for old image , if exists remove it from server
            if ($user->image) {
                // unlink file
                @unlink($this->uploadPath . '/profile/' . $user->image);
            }
            // set file name for storing into db
            $user->image = $image_path;
            $user->save();
            return response()->json($user);
        } else {
            return response()->json(['success' => false, 'error' => 'image file not sent'], 403);
        }
    }

    public function update_cleaner_image($cleaner_id, Request $request)
    {
        $this->writeToLog("update cleaner image called");
        $this->writeToLog("Starting upload cleaner image for: $cleaner_id");
        $cleaner = Cleaner::findOrFail($cleaner_id);
        // file upload tasks
        if ($file = $request->file('image')) {
            $image_path = 'img_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $this->writeToLog("img path: $image_path");
            $upload = $file->move($this->uploadPath . '/profile', $image_path);
            $this->writeToLog("upload: $upload");
            // check for old image , if exists remove it from server
            if ($cleaner->image) {
                // unlink file
                @unlink($this->uploadPath . '/profile/' . $cleaner->image);
            }
            // set file name for storing into db
            $cleaner->image = $image_path;

            $cleaner->save();
            return response()->json($cleaner);
        } else {
            $this->writeToLog("upload failed:");
            return response()->json(['success' => false, 'error' => 'image file not sent'], 403);
        }
    }

    public function upload_cleaner_id($cleaner_id, Request $request)
    {
        $this->writeToLog("Starting upload_cleaner_id for cleaner_id: $cleaner_id");
        $cleaner = Cleaner::findOrFail($cleaner_id);
        $cleanerData = CleanerData::where(['cleaner_id' => $cleaner_id])->first();
        // file upload tasks
        if ($file = $request->file('image')) {
            $this->writeToLog("File received for cleaner_id: $cleaner_id");
            $image_path = 'img_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $upload = $file->move($this->uploadPath . '/cleanerdata', $image_path);
            // check for old image , if exists remove it from server
            if ($cleanerData) {
                if ($cleanerData->id_proof) {
                    // unlink file
                    @unlink($this->uploadPath . '/cleanerdata/' . $cleanerData->id_proof);
                }
                // update new file
                CleanerData::where(['cleaner_id' => $cleaner_id])->update(['id_proof' => $image_path]);
            }
            if ($upload) {
                $this->writeToLog("File uploaded successfully for cleaner_id: $cleaner_id. Path: $image_path");
            }
            return response()->json(['file' => $image_path]);
        } else {
            $this->writeToLog("Failed to receive file for cleaner_id: $cleaner_id");
            return response()->json(['success' => false, 'error' => 'image file not sent'], 403);
        }
    }

    public function upload_cleaner_address($cleaner_id, Request $request)
    {
        $cleaner = Cleaner::findOrFail($cleaner_id);
        $cleanerData = CleanerData::where(['cleaner_id' => $cleaner_id])->first();
        // file upload tasks
        if ($file = $request->file('image')) {
            $image_path = 'img_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $upload = $file->move($this->uploadPath . '/cleanerdata', $image_path);
            // check for old image , if exists remove it from server
            if ($cleanerData) {
                if ($cleanerData->address_proof) {
                    // unlink file
                    @unlink($this->uploadPath . '/cleanerdata/' . $cleanerData->address_proof);
                }
                // update new file
                CleanerData::where(['cleaner_id' => $cleaner_id])->update(['address_proof' => $image_path]);
            }
            return response()->json(['file' => $image_path]);
        } else {
            return response()->json(['success' => false, 'error' => 'image file not sent'], 403);
        }
    }

    public function contact(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'text' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }

        $data = [
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'text' => $request->post('text'),
        ];
        // insert user or cleaner id if receive
        if ($request->post('user_id')) {
            $data['user_id'] = $request->post('user_id');
        }
        if ($request->post('cleaner_id')) {
            $data['cleaner_id'] = $request->post('cleaner_id');
        }

        // get admin email
        $email = Setting::where(['key' => 'email'])->first();
        $email = $email->value;

        if ($request->post('subject')) {
            $subject = $request->post('subject');
        } else {
            $subject = 'New enquiry';
        }

        // create a record in enquiries table
        $data['subject'] = $subject;
        Enquiry::create($data);

        // send a copy in email
        $this->MailController->contact($email, $subject, ['name' => $request->post('name'), 'email' => $request->post('email'), 'text' => $request->post('text')], $request->post('email'), $request->post('name'));

        return response()->json(['success' => true, 'message' => 'Enquiry sent']);
    }

    // in controller callable functions
    public function get_nearest_cleaners($order_id)
    {
        $settings_array = [];
        $settings = Setting::all();
        foreach ($settings as $setting) {
            $settings_array[$setting->key] = $setting->value;
        }
        $cleaners_count = isset($settings_array['cleaners_count']) ? $settings_array['cleaners_count'] : env('DEFAULT_CLEANERS_TO_SEND_NOTIFICATION');

        $order = Order::findOrFail($order_id);
        $latitude = $order->latitude;
        $longitude = $order->longitude;

        $cleaners = Cleaner::where(['available' => 'yes', 'status' => 1])
            ->isWithinMaxDistance($latitude, $longitude, $settings_array['distance_type'])
            //with
            ->with('schedules')->with('orders')

            // cleaner's document verification status check
            ->with('details')
            ->whereHas('details', function ($query) {
                $query->where('status', 'accepted');
            })
            ->orderBy('distance_unit', 'asc')
            ->orderBy('rating', 'desc')
            ->limit($cleaners_count)
            ->get();

        return $cleaners;
    }

    // social login
    public function socialLogin(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'email' => 'required',
            //'device' => 'required',
            // 'token' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $user = User::where(['email' => $request->post('email')])->first();
        if ($user) {
            if ($user->status == 1) {
                $update = User::where(['id' => $user->id])->update(['device' => $request->post('device'), 'token' => $request->post('token')]);
                return response()->json($user);
            } else {
                // user inactive
                return response()->json(['success' => false, 'error' => 'Account is not active'], 403);
            }
        } else {
            return response()->json(['success' => false, 'error' => 'Account does not exist'], 404);
        }
    }

    // apple login
    public function appleLogin(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'apple_id' => 'required',
            //'device' => 'required',
            // 'token' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $email = $request->post('email');
        if ($email != '') {
            // first check with email
            $user = User::where(['email' => $request->post('email')])->first();
            if ($user) {
                // update the apple id if the user
                if ($user->status == 1) {
                    $update = User::where(['id' => $user->id])->update(['device' => $request->post('device'), 'token' => $request->post('token'), 'apple_id' => $request->post('apple_id')]);
                    return response()->json($user);
                } else {
                    // user inactive
                    return response()->json(['success' => false, 'error' => 'Account is not active'], 403);
                }
            } else {
                // check with apple id if cannot find with email
                $user = User::where(['apple_id' => $request->post('apple_id')])->first();
                if ($user) {
                    if ($user->status == 1) {
                        $update = User::where(['id' => $user->id])->update(['device' => $request->post('device'), 'token' => $request->post('token')]);
                        return response()->json($user);
                    } else {
                        // user inactive
                        return response()->json(['success' => false, 'error' => 'Account is not active'], 403);
                    }
                } else {
                    return response()->json(['success' => false, 'error' => 'Account does not exist'], 404);
                }
            }
        } else {
            // email is empty so check with apple id only
            $user = User::where(['apple_id' => $request->post('apple_id')])->first();
            if ($user) {
                if ($user->status == 1) {
                    $update = User::where(['id' => $user->id])->update(['device' => $request->post('device'), 'token' => $request->post('token')]);
                    return response()->json($user);
                } else {
                    // user inactive
                    return response()->json(['success' => false, 'error' => 'Account is not active'], 403);
                }
            } else {
                return response()->json(['success' => false, 'error' => 'Account does not exist'], 404);
            }
        }
    }

    public function charge_for_upcoming($order_id)
    {
        $order = Order::with(['user'])->findOrFail($order_id);
            // have subscriptions so charge for upcoming
            $REPEAT_CLEANS_ADD_IN_ADVANCED = env("REPEAT_CLEANS_ADD_IN_ADVANCED");
            $cleansPending = Cleans::where('order_id', $order_id)->whereIn('status', ['pending', 'paid'])->orderBy('id', 'ASC')->get();
            $cleansPendingCount = (!empty($cleansPending))?count($cleansPending):0;
            $newCleansCount     = $REPEAT_CLEANS_ADD_IN_ADVANCED-$cleansPendingCount;
            if($newCleansCount > 0){
                $lastCleanPending = Cleans::where('order_id', $order_id)->orderBy('id', 'DESC')->first();
                $stamp = strtotime($lastCleanPending->date . ' ' . $lastCleanPending->time);
                $interval = '+' . $order->frequency_days . ' day';
                if($order->frequency != "oneoff"){
                    for ($i = 1; $i <= $newCleansCount; $i++) {
                        $interval = '+' . $order->frequency_days * $i . ' day';
                        $date = date('Y-m-d', strtotime($interval, $stamp));

                        Cleans::insert([
                            'order_id' => $order->id,
                            'date' => $date,
                            'time' => $order->time,
                            'cleaner_id' => $order->cleaner_id,
                            'frequency' => $order->frequency,
                            'customer_id' => $order->user_id,
                            'status' => 'pending',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
            $lastSubscriptionHistory = Cleans::where('date', '>', date('Y-m-d'))->where('order_id', $order_id)->where('status', 'pending')->orderBy('id', 'ASC')->first();
            if ($lastSubscriptionHistory) {
                if ($order->price > 0) {
                    // silently charge and update the status to paid
                    $charge = $this->StripeController->charge_existing_customer_silently($order->user->id, $order->id, $order->price, $lastSubscriptionHistory->id);
                    if ($charge) {
                        // charged successfully
                        // change the order history status to paid
                        Cleans::where(['id' => $lastSubscriptionHistory->id])->update(['status' => 'paid']);
                    } else {
                        // send user email & notification regarding hold payment
                        $this->PushController->send_notification_user($order->id, 'hold');
                        $this->MailController->mailer('default', $order->user->email, env('APP_NAME') . ': Upcoming Booking Paused', ['name' => $order->user->name, 'text' => 'We tried to charge you for one of your upcoming booking, it was not successful. So the recurring booking has been paused. You need to login into your account and select the booking and pay manually to resume it again.']);
                    }
                } else {
                    // promo applied & price is 0 so just make the order as paid without need of payment
                    Cleans::where(['id' => $lastSubscriptionHistory->id])->update(['status' => 'paid']);
                }
            }
    }

    public function resumehold(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'order_id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $order = Order::with(['cleans', 'user', 'cleaner'])->findOrFail($request->post('order_id'));
        // calculate next upcoming service date from histories

        if (count($order->cleans) > 0) {
            $upcoming = Cleans::where('date', '>', date('Y-m-d'))->where('order_id', $request->post('order_id'))->orderBy('id', 'ASC')->first();
            if ($upcoming) {
                // change status to paid
                Cleans::where('id', $upcoming->id)->update(['status' => 'paid']);
            }
        }

        return response()->json($order);

    }

    // get chatlist
    public function getChatList(Request $request)
    {
        if ($request->post('user_id')) {
            $id = $request->post('user_id');
            $type = 'user';
        } else if ($request->post('cleaner_id')) {
            $id = $request->post('cleaner_id');
            $type = 'cleaner';
        } else {
            return response()->json(['success' => false, 'error' => 'user_id or cleaner_id required'], 403);
        }
        if (!env('CHAT_MODULE_ENABLED')) {
            return response()->json(['success' => false, 'error' => 'Chat Module not active'], 403);
        }

        Unirest\Request::verifyPeer(false);
        $res = Unirest\Request::get(env('CHAT_SERVER') . '/getchatlist/' . $id . '/' . $type, $headers = array(), $body = null);
        if ($res->code == 200) {
            $newData = [];
            $data = $res->body;
            foreach ($data as $chat) {
                if ($type == 'user') {
                    // get cleaner
                    $temp = Cleaner::find($chat->cleaner_id);
                } else {
                    // get user
                    $temp = User::find($chat->user_id);
                }

                // check last message sender's name
                $decoded = json_decode($chat->messages);
                $last_name = end($decoded)->name;

                $chat->last_name = $last_name;

                if ($temp) {
                    $temp->address = null; // hide address forcefully
                    $chat->with = $temp;
                    array_push($newData, $chat);
                }

            }
            return response()->json($newData);
        } else {
            return response()->json(['success' => false, 'error' => 'Error retriving data'], 400);
        }
    }

    public function deleteChat(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        if (!env('CHAT_MODULE_ENABLED')) {
            return response()->json(['success' => false, 'error' => 'Chat Module not active'], 403);
        }
        Unirest\Request::verifyPeer(false);
        $res = Unirest\Request::get(env('CHAT_SERVER') . '/deletechat/' . $request->post('id'), $headers = array(), $body = null);
        if ($res->code == 200) {
            return response()->json(['success' => true, 'message' => 'Chat messages deleted.']);
        } else {
            return response()->json(['success' => false, 'error' => 'Error deleting record'], 400);
        }
    }

    //Clean My Place Get Notification
    public function get_notifications($id)
    {
        $notifications = Notification::with(['user', 'cleaner', 'order'])->where('cleaner_id',$id)->where('notification_status', "!=", 'rejected')
            ->whereHas('order', function ($query) {
                $query->where(function ($q1) {
                    $q1->whereNotIn('status', ['cancelled-cleaner','cancelled-user','cancelled-admin','completed']);
                });
                $query->where(function ($q2) {
                    $q2->where('orders.cleaner_id', NULL);
                    // $q2->where('date', '>=', date('Y-m-d'));
                    // $q2->where('created_at', '>=', date('Y-m-d'));
                });
            })
            ->orderBy('date', 'ASC')
            ->get();

        // array_multisort(array_map('strtotime',array_column($notifications,'date')), SORT_ASC, $notifications);

        return response()->json($notifications);
    }

    public function reject_order(Request $request)
    {
        // only remove the order request from notifications
        $credentials = $request->all();
        $rules = [
            'cleaner_id' => 'required',
            'order_id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }

        Notification::where(['cleaner_id' => $request->post('cleaner_id'), 'order_id' => $request->post('order_id')])->delete();

        return response()->json(['success' => true, 'message' => 'Order rejected.']);
    }

    // skip next clean
    public function skip_next_clean(Request $request, $id)
    {
        $credentials = $request->all();
        $rules = [
            'order_id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }

        // authenticate user with order
        $orderDetails = Order::findOrFail($request->post('order_id'));
        if ($id != $orderDetails->user_id) {
            return response()->json(['success' => false, 'message' => 'user not authorised to access the order'], 401);
        }
        // get last paid cleaning details from order history
        $lastPaidCleans = Cleans::where(['status' => 'paid', 'order_id' => $request->post('order_id')])->orderBy('id', 'DESC')->first();
        if ($lastPaidCleans) {
            // make it as pending and transfer payment to next pending Cleans
            // get transaction details of last paid history
            $lastPaidCleans_transaction = Transaction::where(['order_cleans_id' => $lastPaidCleans->id, 'order_id' => $request->post('order_id')])->first();

            // get next pending order history
            $nextPendingCleans = Cleans::where(['status' => 'pending', 'order_id' => $request->post('order_id')])->where('id', '>', $lastPaidCleans->id)->orderBy('id', 'ASC')->first();

            if ($nextPendingCleans) {
                // calculate charge & fees and do charge if necessary =====
                // calculate hours difference
                $hours_diff = strtotime($lastPaidCleans->date . ' ' . $lastPaidCleans->time) - time();
                $hours_diff = intval($hours_diff / (60 * 60));

                $charge_amount = 0;

                if ($hours_diff < 3) {
                    // short notice skip/cancellation
                    $charge_amount = Setting::where(['key' => 'short_notice_skip_charge'])->first()->value;
                } else if ($hours_diff < 24) {
                    // same day skip/cancellation
                    $charge_amount = Setting::where(['key' => 'same_day_skip_charge'])->first()->value;
                } else {
                    // no skip/cancellation fee
                }

                // charge user for the fees
                if ($charge_amount > 0) {
                    $charge = $this->StripeController->charge_custom($orderDetails->user_id, $orderDetails->id, $charge_amount, $lastPaidCleans->id);
                    if (!$charge) {
                        return response()->json(['success' => false, 'message' => 'Charge not possible to the existing user'], 403);
                    }
                }

                // update next pending history paid and old paid history pending
                $update_last = Cleans::findOrFail($lastPaidCleans->id);
                $update_last->status = 'pending';
                $update_last->save();

                $update_next = Cleans::findOrFail($nextPendingCleans->id);
                $update_next->status = 'paid';
                $update_next->save();

                // update transaction's service date & Cleans id
                if ($lastPaidCleans_transaction) {
                    $update_transaction = Transaction::findOrFail($lastPaidCleans_transaction->id);
                    $update_transaction->order_cleans_id = $nextPendingCleans->id;
                    $update_transaction->service_date = $nextPendingCleans->date;
                    $update_transaction->save();
                }

                // send notification to the user itself & the cleaner regarding successfull skip
                $user = User::findOrFail($id);
                if ($user->token) {
                    $tokens = [$user->token];
                    $this->PushController->custom_push($tokens, 'Your next cleaning service has been skipped successfully');
                }
                if ($orderDetails->cleaner_id) {
                    $cleaner = Cleaner::findOrFail($orderDetails->cleaner_id);
                    if ($cleaner->token) {
                        $tokens = [$cleaner->token];
                        $this->PushController->custom_push($tokens, ucwords($user->name) . ' has skipped your next cleaning service');
                    }
                }

                return response()->json(['success' => true, 'message' => 'Skip of next cleaning is successful']);
            } else {
                // no more pending Cleans found
                return response()->json(['success' => false, 'message' => 'No more pending order history found'], 403);
            }

        } else {
            return response()->json(['success' => false, 'message' => 'No paid order history found'], 404);
        }

    }

    // available at alternate date time
    public function available_alternate(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'order_id' => 'required',
            'cleaner_id' => 'required',
            'date' => 'required',
            'time' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        // find the notification and toggle value
        $notification = Notification::where(['cleaner_id' => $request->post('cleaner_id'), 'order_id' => $request->post('order_id')])->first();
        $notification->available_alternate = $request->post('date') . ' ' . $request->post('time') . ':00';
        $notification->save();

        //send a notification to user about alternate availabilities only if order not accepted by anyone in same date time
        $order = Order::findOrFail($request->post('order_id'));
        if ($order->user->token) {
            if ($order->status == 'paid' || $order->status == 'cancelled-cleaner') {
                $this->PushController->reminder($order->id, 'order', [$order->user->token], 'One of the cleaner is available at alternative date & time. Please accept or reject now', 'reminder', 'user');
            }
        }
        return response()->json(['success' => true, 'message' => 'Alternate datetime availability notified to the customer.']);
    }

    public function accept_alternative(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'id' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $notification = Notification::findOrFail($request->post('id'));
        $datetime = explode(' ', $notification->available_alternate);
        $newDate = $datetime[0];
        $timeTemp = explode(':', $datetime[1]);
        $newTime = $timeTemp[0] . ':' . $timeTemp[1];

        // assign cleaner to order
        $order = Order::findOrFail($notification->order_id);
        $order->cleaner_id = $notification->cleaner_id;
        $order->status = 'accepted';
        $order->date = $newDate;
        $order->time = $newTime;
        $order->save();

        // update cleaner to transaction
        Transaction::where(['order_id' => $notification->order_id])->update(['cleaner_id' => $notification->cleaner_id, 'service_date' => $newDate]);

        if ($order->frequency != 'oneoff') {
            // update the subscription
                // update the dates according to the alternative date & time ================

                // remove old pending order histories and insert new ones according to the frequency ================
                Cleans::where(['order_id' => $notification->order_id, 'status' => 'pending'])->delete();
                $stamp = strtotime($newDate . ' ' . $newTime);
                $REPEAT_CLEANS_ADD_IN_ADVANCED = env("REPEAT_CLEANS_ADD_IN_ADVANCED");
                if ($order->frequency == 'weekly') {
                    // weekly
                    for ($i = 1; $i <= $REPEAT_CLEANS_ADD_IN_ADVANCED; $i++) {
                        $interval = '+' . $i . ' week';
                        $date = date('Y-m-d', strtotime($interval, $stamp));
                        if ($date > date('Y-m-d')) {
                            Cleans::insert([
                                'order_id' => $order->id,
                                'date' => $date,
                                'time' => $order->time,
                                'cleaner_id' => $order->cleaner_id,
                                'frequency' => $order->frequency,
                                'customer_id' => $order->user_id,
                                'status' => 'pending',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        }

                    }
                } elseif ($order->frequency == 'biweekly') {
                    // bi weekly
                    for ($i = 1; $i <= $REPEAT_CLEANS_ADD_IN_ADVANCED; $i++) {
                        // if ($i % 2 == 0) {
                            $interval = '+' . ($i*2) . ' week';
                            $date = date('Y-m-d', strtotime($interval, $stamp));
                            if ($date > date('Y-m-d')) {
                                Cleans::insert([
                                    'order_id' => $order->id,
                                    'date' => $date,
                                    'time' => $order->time,
                                    'cleaner_id' => $order->cleaner_id,
                                    'frequency' => $order->frequency,
                                    'customer_id' => $order->user_id,
                                    'status' => 'pending',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                            }
                        // }
                    }
                } elseif ($order->frequency == 'monthly') {
                    // monthly
                    for ($i = 1; $i <= $REPEAT_CLEANS_ADD_IN_ADVANCED; $i++) {
                        $interval = '+' . $i . ' month';
                        $date = date('Y-m-d', strtotime($interval, $stamp));
                        if ($date > date('Y-m-d')) {
                            Cleans::insert([
                                'order_id' => $order->id,
                                'date' => $date,
                                'time' => $order->time,
                                'cleaner_id' => $order->cleaner_id,
                                'frequency' => $order->frequency,
                                'customer_id' => $order->user_id,
                                'status' => 'pending',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        }

                    }
                } elseif ($order->frequency == 'daily') {
                    // daily
                    for ($i = 1; $i <= $REPEAT_CLEANS_ADD_IN_ADVANCED; $i++) {
                        $interval = '+' . $i . ' day';
                        $date = date('Y-m-d', strtotime($interval, $stamp));
                        if ($date > date('Y-m-d')) {
                            Cleans::insert([
                                'order_id' => $order->id,
                                'date' => $date,
                                'time' => $order->time,
                                'cleaner_id' => $order->cleaner_id,
                                'frequency' => $order->frequency,
                                'customer_id' => $order->user_id,
                                'status' => 'pending',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        }

                    }
                }

        }

        // delete from notification log
        Notification::where(['order_id' => $notification->order_id])->delete();

        // send notification to the cleaner
        if ($order->cleaner->token) {
            $this->PushController->reminder($notification->order_id, 'order', [$order->cleaner->token], 'Good news! Your alternative date & time has been accepted. See your cleans on the app.', 'reminder', 'cleaner');
        }
        return response()->json($notification);
    }

    public function reject_alternative(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'id' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }
        $notification = Notification::findOrFail($request->post('id'));
        $notification->delete();
        return response()->json(['success' => true, 'message' => 'Rejection successful.']);
    }

    // banners
    public function getUserBanners()
    {
        $banners = Banner::where('status', 1)->where('type', 'user')->orWhere('type', 'all')->orderBy('priority', 'desc')->get();
        return response()->json($banners);
    }

    public function getCleanerBanners()
    {
        $banners = Banner::where('status', 1)->where('type', 'cleaner')->orWhere('type', 'all')->orderBy('priority', 'desc')->get();
        return response()->json($banners);
    }

    public function testDebug()
    {
        $mail = $this->MailController->mailer('registration', 'v1technologiessumandutta@gmail.com', 'Welcome to ' . env('APP_NAME'), ['name' => 'Vineet']);

        // $mail = $this->MailController->mailer('forgot', 'vineet@v1technologies.com', 'Reset password', ['name'=> 'Vineet', 'code'=> '123456']);

        // $this->MailController->mailer('default', 'vineet@v1technologies.com', 'Awaiting document verification', ['name'=> 'Admin', 'text'=> 'A new cleaner has joined with documents awaiting verification']);

        // $this->MailController->contact('vineet@v1technologies.com', 'New Enquiry', ['name'=> 'Vineet', 'email'=> 'vineet@v1technologies.com', 'text'=> 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec lobortis, magna eget eleifend pulvinar, tortor diam malesuada sem, eu pretium leo risus consequat arcu. Nulla hendrerit semper tellus. Donec posuere, est vulputate blandit aliquam, ex mi fringilla tortor, in eleifend neque dolor at neque.'], 'vineet@v1technologies.com', 'Vineet');

        // $mail = $this->MailController->mailer('default', 'vineet@v1technologies.com', env('APP_NAME').': Account verified', ['name'=> 'Vineet', 'text'=> 'Welcome to '.env('APP_NAME').'!<br/><br/>Congratulations, your account has now been verified. You may now log in and start accepting orders through '.env('APP_NAME').'.']);

        // $mail = $this->MailController->mailer('default', 'vineet@v1technologies.com', env('APP_NAME').': Account verification unsuccessful', ['name'=> 'Vineet', 'text'=> 'We were unable to verify your documents. Please open the app to resubmit the documents.']);

        return response()->json(['message' => 'Mails sent']);
    }

    public function delete_cleaner(Request $request)
    {
        $input = $request->all();
        $rules = [
            'id' => 'required'
        ];
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 403);
        }

        $cleaner = Cleaner::find($input['id']);

        if ($cleaner) {
            $cleaner->account_status = 'deleted';
            $cleaner->save();
        } else {
            return response()->json(['message' => 'Account Not Found'], 404);
        }

        return response()->json(['message' => 'successfully updated.']);

    }

    public function get_balance($cleaner_id)
    {

        $cleaner = Cleaner::find($cleaner_id);
        if ($cleaner) {
            $stripe_acct_id = $cleaner->stripe_acct_id;
            if(!empty($stripe_acct_id)){
                $account_balance = $this->StripeController->get_account_balance_retrive($stripe_acct_id);
                return response()->json($account_balance);
            }else{
                return response()->json(['message' => 'Stripe Account Not Found'], 404);
            }
        } else {
            return response()->json(['message' => 'Account Not Found'], 404);
        }
    }

    public function get_payouts($cleaner_id)
    {
        $cleaner = Cleaner::find($cleaner_id);
        if ($cleaner) {
            $stripe_acct_id = $cleaner->stripe_acct_id;
            if (!empty($stripe_acct_id)) {
                // Assuming getPayouts is a method in StripeController that handles the Stripe Payouts API call
                $four_weeks_from_now = time() + (4 * 7 * 24 * 60 * 60);
                $account_payouts = $this->StripeController->get_account_payouts($stripe_acct_id);
                return response()->json($account_payouts);
            } else {
                return response()->json(['message' => 'Stripe Account Not Found'], 404);
            }
        } else {
            return response()->json(['message' => 'Account Not Found'], 404);
        }
    }

    public function get_balance_payouts($cleaner_id, $created_after = null)
    {
        $cleaner = Cleaner::find($cleaner_id);
        if ($cleaner) {
            $stripe_acct_id = $cleaner->stripe_acct_id;
            if (!empty($stripe_acct_id)) {
                // Check if created_after parameter is provided and is a valid timestamp
                if ($created_after && is_numeric($created_after)) {
                    // getPayouts is a method in StripeController that handles the Stripe Payouts API call
                    // and now also accepts a timestamp to filter transactions created after this timestamp
                    $account_payouts = $this->StripeController->get_account_balance_transactions_payouts($stripe_acct_id, $created_after);
                } else {
                    // Call without the created_after filter if no valid timestamp is provided
                    $account_payouts = $this->StripeController->get_account_balance_transactions_payouts($stripe_acct_id);
                }
                return response()->json($account_payouts);
            } else {
                return response()->json(['message' => 'Stripe Account Not Found'], 404);
            }
        } else {
            return response()->json(['message' => 'Account Not Found'], 404);
        }
    }

}
