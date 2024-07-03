<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Cleaner;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Payout;
use App\Models\CleanerData;
use App\Models\Notification;
use App\Models\Enquiry;
use App\Models\Banner;
use App\Models\ExtraService;
use App\Models\TaxSetting;
use App\Models\Mailcontent;

use App\Models\Cleans;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

use DB;

use Google\Cloud\SecretManager\V1\Client\SecretManagerServiceClient;
use Google\Cloud\SecretManager\V1\GetSecretRequest;


class AdminController extends Controller
{
    //
    public $uploadPath = 'uploads', $UtilityController, $MailController, $PushController;

    public function __construct(Request $request)
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
        // check session for logged in already or not
        if (!$request->session()->has('admin')) {
            return redirect()->to('/admin/login')->send();
        }
    }

    // ============ dashboard
    public function index()
    {
        // get statistics
        $orders = Order::all()->count();
        $users = User::all()->count();
        $transactions = Transaction::all()->count();
        $ratings = Rating::all()->count();
        $cleaners = Cleaner::all()->count();

        $orderactions = Order::with('user')->with('cleaner')->where(['status' => 'cancelled-cleaner'])
            ->orWhere(function ($query) {
                $query->where(['status' => 'paid']);
                $query->where(['cleaner_id' => NULL]);
                $query->where('updated_at', '<', date('Y-m-d H:i:s', strtotime('-15 minutes')));
            })
            ->orderBy('id', 'desc')->get();
        $enquiries = Enquiry::with(['user', 'cleaner'])->orderBy('id', 'desc')->get();

        $usercancelledactions = Order::with('user')->with('cleaner')->where(['status' => 'cancelled-user'])->orderBy('id', 'desc')->get();

        $sec_value = $this->secret_from_manager();

        return view('admin.dashboard', ['users' => $users, 'sec' => $sec_value, 'orders' => $orders, 'transactions' => $transactions, 'ratings' => $ratings, 'cleaners' => $cleaners, 'orderactions' => $orderactions, 'enquiries' => $enquiries, 'cancelled' => $usercancelledactions]);
    }

    public function secret_from_manager() {

        $client = new SecretManagerServiceClient();
        $name = $client->secretName('home-cleaning-373216', 'test_secret');
        $request = GetSecretRequest::build($name);
        $secret = $client->getSecret($request);

        $result = $secret->getLabels();
    
        return $result['stripe_live_key'];
    }

    // ============ logout admin
    public function logout_admin(Request $request)
    {
        $request->session()->forget('admin');
        return redirect('/admin/login');
    }

    // =========== view profile
    public function profile(Request $request)
    {
        $id = $request->session()->get('admin')->id;
        $user = Admin::findOrFail($id);
        return view('admin.profile', ['user' => $user]);
    }

    // ============= update profile
    public function updateprofile(Request $request)
    {
        $id = $request->session()->get('admin')->id;
        $res = Admin::findOrFail($id);
        $res->name = $request->post('name');
        $res->email = $request->post('email');
        $res->save();
        if ($res) {
            // update the admin session
            $request->session()->put('admin', $res);
            return redirect('/admin/profile')->with('success', 'Profile updated successfully!');
        } else {
            return redirect('/admin/profile')->with('error', 'Profile update Failed!');
        }
    }

    // =========== update password
    public function updatepassword(Request $request)
    {
        $id = $request->session()->get('admin')->id;
        $res = Admin::findOrFail($id);
        $res->password = Hash::make($request->post('password'));
        $res->save();
        if ($res) {
            return redirect('/admin/profile')->with('success', 'Password updated successfully!');
        } else {
            return redirect('/admin/profile')->with('error', 'Password update Failed!');
        }

    }

    // ========= users
    public function users()
    {
        $users = User::with('orders')->with('ratings')->with('transactions')->get();
        if ($users) {
            return view('admin.user.users', ['users' => $users]);
        } else {
            return view('admin.user.users', ['users' => []]);
        }
    }

    public function createuser_view()
    {
        return view('admin.user.create');
    }

    public function createuser(Request $request)
    {
        $data = $request->all();
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required',
            'address' => 'required',
            'postcode' => 'required',
            'image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return redirect('/CreateCustomer')->with('error', $validator->messages());
        }

        $data = [
            'password' => Hash::make($request->post('password')),
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'phone' => $request->post('phone'),
            'postcode' => $request->post('postcode'),
            'address' => $request->post('address'),
            'device' => 'web',
        ];

        // handle image upload
        if ($file = $request->file('image')) {
            $image_path = 'img_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $upload = $file->move($this->uploadPath . '/profile', $image_path);
            // set file name for storing into db
            $data['image'] = $image_path;
        }

        // status active by default created by admin
        $data['status'] = 1;
        $user = User::create($data);
        if ($user) {
            return redirect('/Customers')->with('success', 'User Created successfully!');
        } else {
            return redirect('/CreateCustomer')->with('error', 'User creation Failed!');
        }
    }

    public function updateuser_view($id)
    {
        $user = User::findOrFail($id);
        $user->transactions;
        $user->orders;
        $currency = $this->UtilityController::Currencies()[env('CURRENCY')];
        if ($user) {
            return view('admin.user.user', ['user' => $user, 'currency' => $currency]);
        } else {
            abort(404);
        }
    }

    public function updateuser($id, Request $request)
    {
        $data = $request->all();
        $user = User::findOrFail($id);

        if ($request->post('name')) {
            $user->name = $request->post('name');
        }
        // update password if entered only
        if ($request->post('password') && $request->post('password') != '') {
            $user->password = Hash::make($request->post('password'));
        }
        if ($request->post('email')) {
            $user->email = $request->post('email');
        }
        if ($request->post('phone')) {
            $user->phone = $request->post('phone');
        }

        if ($request->post('house_number')) {
            $user->house_number = $request->post('house_number');
        }

        // get lat long from the api
        // address with postcode work here
        if ($request->post('address') && $request->post('postcode')) {
            $fullAddress = $request->post('address') . ',' . $request->post('postcode');
            // check for geocoding address
            $latlong = $this->UtilityController->getLatLong($fullAddress);
            if ($latlong) {
                $user->latitude = $latlong->lat;
                $user->longitude = $latlong->lng;
                $user->address = $request->post('address');
                $user->postcode = $request->post('postcode');
            }
        } elseif ($request->post('address') && !$request->post('postcode')) {
            $fullAddress = $request->post('address');
            // check for geocoding address
            $latlong = $this->UtilityController->getLatLong($fullAddress);
            if ($latlong) {
                $user->latitude = $latlong->lat;
                $user->longitude = $latlong->lng;
                $user->address = $request->post('address');
            }
        } elseif ($request->post('postcode') && !$request->post('address')) {
            $fullAddress = $request->post('postcode');
            // check for geocoding address
            $latlong = $this->UtilityController->getLatLong($fullAddress);
            if (!$latlong) {
                $user->latitude = $latlong->lat;
                $user->longitude = $latlong->lng;
                $user->postcode = $request->post('postcode');
            }
        }

        if ($request->post('status')) {
            $user->status = $request->post('status');
        }

        // handle image upload
        if ($file = $request->file('image')) {
            $image_path = 'img_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $upload = $file->move($this->uploadPath . '/profile', $image_path);
            // delete existing image if any exists
            if ($user->image) {
                // unlink old file
                @unlink($this->uploadPath . '/profile/' . $user->image);
            }
            // set file name for storing into db
            $user->image = $image_path;
        }

        $user->save();

        if ($user) {
            return redirect('/admin/customer/' . $id)->with('success', 'Profile updated successfully!');
        } else {
            return redirect('/admin/customer/' . $id)->with('error', 'Profile update Failed!');
        }
    }

    // ========= cleaners
    public function cleaners()
    {
        $cleaners = Cleaner::with('orders')->with('ratings')->with('transactions')->get();
        if ($cleaners) {
            return view('admin.cleaner.cleaners', ['cleaners' => $cleaners]);
        } else {
            return view('admin.cleaner.cleaners', ['cleaners' => []]);
        }
    }

    public function createcleaner_view()
    {
        return view('admin.cleaner.create');
    }

    public function createcleaner(Request $request)
    {
        $data = $request->all();
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required',
            'address' => 'required',
            'postcode' => 'required',
            'image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return redirect('/admin/create/cleaner')->with('error', $validator->messages());
        }

        $data = [
            'password' => Hash::make($request->post('password')),
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'phone' => $request->post('phone'),
            'postcode' => $request->post('postcode'),
            'address' => $request->post('address'),
            'about' => $request->post('about'),
            'qualification' => $request->post('qualification'),
            'device' => 'web',
        ];


        // handle image upload
        if ($file = $request->file('image')) {
            $image_path = 'img_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $upload = $file->move($this->uploadPath . '/profile', $image_path);
            // set file name for storing into db
            $data['image'] = $image_path;
        }

        // get lat long from the api
        // address with postcode work here
        if ($request->post('address') && $request->post('postcode')) {
            $fullAddress = $request->post('address') . ',' . $request->post('postcode');
            // check for geocoding address
            $latlong = $this->UtilityController->getLatLong($fullAddress);
            if ($latlong) {
                $data['latitude'] = $latlong->lat;
                $data['longitude'] = $latlong->lng;
            }
        } elseif ($request->post('address') && !$request->post('postcode')) {
            $fullAddress = $request->post('address');
            // check for geocoding address
            $latlong = $this->UtilityController->getLatLong($fullAddress);
            if ($latlong) {
                $data['latitude'] = $latlong->lat;
                $data['longitude'] = $latlong->lng;
            }
        } elseif ($request->post('postcode') && !$request->post('address')) {
            $fullAddress = $request->post('postcode');
            // check for geocoding address
            $latlong = $this->UtilityController->getLatLong($fullAddress);
            if (!$latlong) {
                $data['latitude'] = $latlong->lat;
                $data['longitude'] = $latlong->lng;
            }
        }

        // status active by default created by admin
        $data['status'] = 1;
        $cleaner = Cleaner::create($data);
        if ($cleaner) {
            return redirect('/admin/cleaners')->with('success', 'Cleaner Created successfully!');
        } else {
            return redirect('/admin/create/cleaner')->with('error', 'Cleaner creation Failed!');
        }
    }

    public function updatecleaner_view($id)
    {
        $cleaner = Cleaner::findOrFail($id);
        $cleaner->transactions;
        $cleaner->orders;
        $cleaner->ratings;
        $cleaner->payouts;
        $currency = $this->UtilityController::Currencies()[env('CURRENCY')];

        // schedule
        $schedule_data = Schedule::where(['cleaner_id' => $id])->orderBy('start')->get();
        $schedules = [];
        foreach ($schedule_data as $item) {
            $schedules[$item['day']][] = ['id' => $item['id'], 'cleaner_id' => $item['cleaner_id'], 'start' => $item['start'], 'end' => $item['end']];
        }
        return view('admin.cleaner.cleaner', ['cleaner' => $cleaner, 'currency' => $currency, 'schedules' => $schedules]);
    }

    public function updatecleaner($id, Request $request)
    {
        $data = $request->all();
        $cleaner = Cleaner::findOrFail($id);

        if ($request->post('name')) {
            $cleaner->name = $request->post('name');
        }
        // update password if entered only
        if ($request->post('password') && $request->post('password') != '') {
            $cleaner->password = Hash::make($request->post('password'));
        }
        if ($request->post('email')) {
            $cleaner->email = $request->post('email');
        }
        if ($request->post('phone')) {
            $cleaner->phone = $request->post('phone');
        }
        if ($request->post('about')) {
            $cleaner->about = $request->post('about');
        }
        if ($request->post('qualification')) {
            $cleaner->qualification = $request->post('qualification');
        }
        if ($request->post('dob')) {
            $cleaner->dob = date("Y-m-d", strtotime($request->post('dob')));
        }

        // get lat long from the api
        // address with postcode work here
        if ($request->post('address') && $request->post('postcode')) {
            $fullAddress = $request->post('address') . ',' . $request->post('postcode');
            // check for geocoding address
            $latlong = $this->UtilityController->getLatLong($fullAddress);
            if ($latlong) {
                $cleaner->latitude = $latlong->lat;
                $cleaner->longitude = $latlong->lng;
                $cleaner->address = $request->post('address');
                $cleaner->postcode = $request->post('postcode');
            }
        } elseif ($request->post('address') && !$request->post('postcode')) {
            $fullAddress = $request->post('address');
            // check for geocoding address
            $latlong = $this->UtilityController->getLatLong($fullAddress);
            if ($latlong) {
                $cleaner->latitude = $latlong->lat;
                $cleaner->longitude = $latlong->lng;
                $cleaner->address = $request->post('address');
            }
        } elseif ($request->post('postcode') && !$request->post('address')) {
            $fullAddress = $request->post('postcode');
            // check for geocoding address
            $latlong = $this->UtilityController->getLatLong($fullAddress);
            if (!$latlong) {
                $cleaner->latitude = $latlong->lat;
                $cleaner->longitude = $latlong->lng;
                $cleaner->postcode = $request->post('postcode');
            }
        }

        if ($request->post('status')) {
            $cleaner->status = $request->post('status');
        }
        if ($request->post('distance')) {
            $cleaner->distance = $request->post('distance');
        }

        // handle image upload
        if ($file = $request->file('image')) {
            $image_path = 'img_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $upload = $file->move($this->uploadPath . '/profile', $image_path);
            // delete existing image if any exists
            if ($cleaner->image) {
                // unlink old file
                @unlink($this->uploadPath . '/profile/' . $cleaner->image);
            }
            // set file name for storing into db
            $cleaner->image = $image_path;
        }

        $cleaner->save();

        if ($cleaner) {
            return redirect('/admin/cleaner/' . $id)->with('success', 'Profile updated successfully!');
        } else {
            return redirect('/admin/cleaner/' . $id)->with('error', 'Profile update Failed!');
        }
    }

    public function updatecleanerchedule($id, Request $request)
    {
        $data = $request->all();
        $times = $this->UtilityController::$Times;

        $flag = 0;
        $tempTimes = [];
        foreach ($times as $time) {
            if ($data['start'] == $time) {
                $flag = 1;
            }
            if ($flag == 1) {
                array_push($tempTimes, $time);
            }
            if ($data['end'] == $time) {
                $flag = 0;
            }
        }

        // delete previous records
        $del = Schedule::where(['cleaner_id' => $id, 'day' => $data['day']])->delete();

        // remove the last time from array as upto that time will be created records
        array_pop($tempTimes);
        // insert new records
        foreach ($tempTimes as $single) {
            $start_position = array_search($single, array_keys($times));
            $end_position = $start_position + 1;
            $end = $times[$end_position];
            $ins = Schedule::insert(['cleaner_id' => $id, 'day' => $data['day'], 'start' => $single, 'end' => $end, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        }
        return redirect('/admin/cleaner/' . $id)->with('success', 'Schedule updated successfully!');
    }

    // ========= mail content
    public function mailcontent()
    {
        $mailcontent = Mailcontent::get();
        if ($mailcontent) {
            return view('admin.mailcontent.mails', ['mailcontent' => $mailcontent]);
        } else {
            return view('admin.mailcontent.mails', ['mailcontent' => []]);
        }
    }

    public function createmailcontent_view()
    {
        return view('admin.mailcontent.create');
    }

    public function add_mail_content(Request $request)
    {
        $data = $request->all();
        $rules = [
            'type' => 'required',
            'content' => 'required'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return redirect('/CreateMailContent')->with('error', $validator->messages());
        }

        $data = [
            'type' => $request->post('type'),
            'content' => $request->post('content')
        ];

        $mailcontent = Mailcontent::create($data);
        if ($mailcontent) {
            return redirect('/MailContent')->with('success', 'Mail Content Created successfully!');
        } else {
            return redirect('/CreateMailContent')->with('error', 'Mail Content creation Failed!');
        }
    }

    public function update_mail_content_view($id)
    {
        $mailcontent = Mailcontent::findOrFail($id);
        return view('admin.mailcontent.mail', ['mailcontent' => $mailcontent]);
    }

    public function update_mail_content($id, Request $request)
    {
        $mailcontent = Mailcontent::findOrFail($id);

        if ($request->post('type')) {
            $mailcontent->type = $request->post('type');
        }

        if ($request->post('content')) {
            $mailcontent->content = $request->post('content');
        }

        $mailcontent->save();

        if ($mailcontent) {
            return redirect('/MailContentEdit/' . $id)->with('success', 'Mail Content updated successfully!');
        } else {
            return redirect('/MailContentEdit/' . $id)->with('error', 'Mail Content update Failed!');
        }
    }

    // public function updatecleanerchedule($id, Request $request){
    //     $data = $request->all();
    //     $times = $this->UtilityController::$Times;

    //     $flag = 0;
    //     $tempTimes = [];
    //     foreach($times as $time){
    //         if($data['start'] == $time){
    //             $flag = 1;
    //         }
    //         if($flag == 1){
    //             array_push($tempTimes, $time);
    //         }
    //         if($data['end'] == $time){
    //             $flag = 0;
    //         }
    //     }

    //     // delete previous records
    //     $del = Schedule::where(['cleaner_id'=> $id, 'day'=> $data['day']])->delete();

    //     // remove the last time from array as upto that time will be created records
    //     array_pop($tempTimes);
    //     // insert new records
    //     foreach($tempTimes as $single){
    //         $start_position = array_search($single, array_keys($times));
    //         $end_position = $start_position+1;
    //         $end = $times[$end_position];
    //         $ins = Schedule::insert(['cleaner_id'=> $id, 'day'=> $data['day'], 'start'=> $single, 'end'=> $end, 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')]);
    //     }
    //     return redirect('/admin/cleaner/'.$id)->with('success', 'Schedule updated successfully!');
    // }

    // ========= Setting
    public function settings()
    {
        $data = Setting::all();
        $currency = $this->UtilityController::Currencies()[env('CURRENCY')];
        $arr = [];
        foreach ($data as $item) {
            $arr[$item['key']] = $item['value'];
        }
        if (count($arr) > 0) {
            return view('admin.settings', ['settings' => $arr, 'currency' => $currency]);
        } else {
            return view('admin.settings', ['settings' => [], 'currency' => $currency]);
        }
    }

    public function createorupdatesetting(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        unset($data['image']);
        unset($data['icon']);
        unset($data['cleaner_login_image']);
        try {
            foreach ($data as $key => $value) {
                $find = Setting::where('key', $key)->get();
                if ($find->count() > 0) {
                    // update
                    $save = Setting::where('key', $key)->update(['value' => $value]);
                } else {
                    // create a new record
                    $arr['key'] = $key;
                    $arr['value'] = $value;
                    $save = Setting::create($arr);
                }
            }

            // handle image upload
            if ($file = $request->file('image')) {
                $image_path = 'img_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $upload = $file->move($this->uploadPath . '/images', $image_path);
                $find = Setting::where('key', 'image')->first();
                if ($find) {
                    // update
                    // delete old image if any
                    if ($find->value) {
                        @unlink($this->uploadPath . '/images/' . $find->value);
                    }
                    Setting::where('key', 'image')->update(['value' => $image_path]);
                } else {
                    //create
                    Setting::create(['key' => 'image', 'value' => $image_path]);
                }
            }

            // handle image upload
            if ($file = $request->file('icon')) {
                $image_path = 'img_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $upload = $file->move($this->uploadPath . '/images', $image_path);
                $find = Setting::where('key', 'icon')->first();
                if ($find) {
                    // update
                    // delete old image if any
                    if ($find->value) {
                        @unlink($this->uploadPath . '/images/' . $find->value);
                    }
                    Setting::where('key', 'icon')->update(['value' => $image_path]);
                } else {
                    //create
                    Setting::create(['key' => 'icon', 'value' => $image_path]);
                }
            }

            // handle image upload
            if ($file = $request->file('cleaner_login_image')) {
                $image_path = 'img_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $upload = $file->move($this->uploadPath . '/images', $image_path);
                $find = Setting::where('key', 'cleaner_login_image')->first();
                if ($find) {
                    // update
                    // delete old image if any
                    if ($find->value) {
                        @unlink($this->uploadPath . '/images/' . $find->value);
                    }
                    Setting::where('key', 'cleaner_login_image')->update(['value' => $image_path]);
                } else {
                    //create
                    Setting::create(['key' => 'cleaner_login_image', 'value' => $image_path]);
                }
            }

            return redirect('/admin/settings')->with('success', 'Settings Updated Successfully!');
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect('/admin/settings')->with('error', 'Settings Update Failed!');
        }
    }

    // ================ Reviews
    public function reviews(Request $request)
    {
        $reviews = Rating::with('user')->with('cleaner')->orderBy('id', 'desc')->get();
        return view('admin.review.reviews', ['reviews' => $reviews]);
    }

    public function createreview_view()
    {
        $users = User::all();
        $cleaners = Cleaner::all();
        return view('admin.review.create', ['users' => $users, 'cleaners' => $cleaners]);
    }

    public function createreview(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'cleaner_id' => 'required',
            'rating' => 'required',
            'user_id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return redirect('/admin/create/review')->with('error', $validator->messages());
        }

        $data = [
            'cleaner_id' => $request->post('cleaner_id'),
            'user_id' => $request->post('user_id'),
            'rating' => $request->post('rating'),
            'comment' => $request->post('comment'),
        ];

        try {
            $save = Rating::create($data);

            // update user average rating
            $ratings = Rating::where(['cleaner_id' => $request->post('cleaner_id')])->get();
            if ($ratings->count() > 0) {
                $total = 0;
                foreach ($ratings as $rating) {
                    $total += $rating->rating;
                }
                $average = ($total / $ratings->count());
                $cleaner = Cleaner::findOrFail($request->post('cleaner_id'));
                $cleaner->rating = $average;
                $cleaner->save();
            } else {
                $cleaner = Cleaner::findOrFail($request->post('cleaner_id'));
                $cleaner->rating = NULL;
                $cleaner->save();
            }
            return redirect('/admin/reviews')->with('success', 'Review Created successfully!');
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect('/admin/create/review')->with('error', 'Review creation Failed!');
        }
    }

    // ======== transaction
    public function transactions(Request $request)
    {
        $start = '';
        $end = '';
        if ($request->post('start') != '') {
            // update start
            $start = $request->post('start');
        }
        if ($request->post('end') != '') {
            // update end
            $end = $request->post('end');
        }

        // trick => end date extend by one day to work proper in between
        $end_date = date('Y-m-d', strtotime($end . '+1 day'));

        if ($start != '' & $end != '') {
            // filter transactions based on service date
            $transactions = Transaction::with('user')->with('cleaner')->whereBetween('service_date', [$start, $end_date])->orWhereBetween('created_at', [$start, $end_date])->get();
        } else {
            // get all transactions
            $transactions = Transaction::with('user')->with('cleaner')->get();
        }
        return view('admin.transaction.transactions', ['transactions' => $transactions, 'start' => $start, 'end' => $end]);
    }
    // ======== orders
    public function orders(Request $request)
    {
        $start = '';
        $end = '';
        if ($request->post('start') != '') {
            // update start
            $start = $request->post('start');
        }
        if ($request->post('end') != '') {
            // update end
            $end = $request->post('end');
        }
        if ($start != '' & $end != '') {
            // trick => end date extend by one day to work proper in between
            $end_date = date('Y-m-d', strtotime($end . '+1 day'));

            // filter transactions based on jop date
            $orders = Order::with('cleaner')->with('user')->orderBy('id', 'desc')->orderBy('status')->whereBetween('date', [$start, $end_date])->get();
        } else {
            // all orders
            $orders = Order::with('cleaner')->with('user')->orderBy('id', 'desc')->orderBy('status')->get();
        }
        return view('admin.order.orders', ['orders' => $orders, 'start' => $start, 'end' => $end]);
    }

    public function order($id)
    {
        $order = Order::findOrFail($id);
        $order->user;
        $order->cleaner;
        $order->transactions;
        $order->cleans;
        $cleaners = Cleaner::where(['status' => 1, 'available' => 'yes'])->whereHas('details', (function ($query) {
            $query->where('status', 'accepted');
        }))->get();
        $notifications = Notification::with(['cleaner'])->where(['order_id' => $id, 'notification_status' => 'active'])->get();
        return view('admin.order.order', ['order' => $order, 'cleaners' => $cleaners, 'notifications' => $notifications]);
    }

    public function updateorder($id, Request $request)
    {
        $order = Order::findOrFail($id);
        $data = $request->all();

        // format date
        $data['date'] = date('Y-m-d', strtotime($data['date']));

        // update normal details
        if ($request->post('address')) {
            if ($order->address != $data['address']) {
                $order->address = $data['address'];
            }
        }
        if ($request->post('house_number')) {
            if ($order->house_number != $data['house_number']) {
                $order->house_number = $data['house_number'];
            }
        }
        if ($request->post('postcode')) {
            if ($order->postcode != $data['postcode']) {
                $order->postcode = $data['postcode'];
            }
        }
        if ($request->post('date')) {
            if ($order->date != $data['date']) {

                // date only changable for oneoff order
                if ($order->frequency == 'oneoff') {
                    // update the transaction date
                    Transaction::where(['order_id' => $order->id])->update(['service_date' => $data['date']]);
                }

                // now date changable for recurring order too =======
                if ($order->frequency != 'oneoff') {
                    // update the transaction date
                    Transaction::where(['order_id' => $order->id, 'service_date' => $order->date])->update(['service_date' => $data['date']]);

                    // only handle here if frequency not changed, else handle in frequency section ==============================
                    if ($data['frequency'] == $order->frequency) {

                        // now update the order histories which are not completed yet
                        $orderhistories = Cleans::where('order_id', $order->id)->where('status', '!=', 'completed')->orderBy('id', 'asc')->get();

                        // if order not started yet
                        if ($order->status == 'paid' || $order->status == 'pending' || $order->status == 'accepted') {
                            if ($order->frequency == 'weekly') {
                                $start_from = date('Y-m-d', strtotime('+1 week', strtotime($data['date'] . ' ' . $order->time)));
                            } else if ($order->frequency == 'biweekly') {
                                $start_from = date('Y-m-d', strtotime('+2 week', strtotime($data['date'] . ' ' . $order->time)));
                            } else if ($order->frequency == 'monthly') {
                                $start_from = date('Y-m-d', strtotime('+1 month', strtotime($data['date'] . ' ' . $order->time)));
                            } else if ($order->frequency == 'daily') {
                                $start_from = date('Y-m-d', strtotime('+1 day', strtotime($data['date'] . ' ' . $order->time)));
                            }

                        } else {
                            // already first order done or cancelled
                            $start_from = $data['date'];
                        }

                        $newdates = [];
                        if ($order->frequency == 'weekly') {
                            for ($i = 0; $i < count($orderhistories); $i++) {
                                array_push($newdates, date('Y-m-d', strtotime('+' . $i . ' week', strtotime($start_from . ' ' . $order->time))));
                            }
                        } else if ($order->frequency == 'biweekly') {
                            for ($i = 0; $i < count($orderhistories); $i++) {
                                array_push($newdates, date('Y-m-d', strtotime('+' . ($i * 2) . ' week', strtotime($start_from . ' ' . $order->time))));
                            }
                        } else if ($order->frequency == 'monthly') {
                            for ($i = 0; $i < count($orderhistories); $i++) {
                                array_push($newdates, date('Y-m-d', strtotime('+' . $i . ' month', strtotime($start_from . ' ' . $order->time))));
                            }
                        } else if ($order->frequency == 'daily') {
                            for ($i = 0; $i < count($orderhistories); $i++) {
                                array_push($newdates, date('Y-m-d', strtotime('+' . $i . ' day', strtotime($start_from . ' ' . $order->time))));
                            }
                        }

                        $i = 0;
                        foreach ($orderhistories as $history) {
                            Cleans::where(['id' => $history->id])->update(['date' => $newdates[$i]]);
                            if ($history->status == 'paid') {
                                // update transaction service date also
                                Transaction::where(['order_id' => $order->id, 'order_cleans_id' => $history->id])->update(['service_date' => $newdates[$i]]);
                            }
                            $i++;
                        }
                    }

                }

                // Update Cleans If Any Cleans are Pending for Current Orders
                Cleans::where(['order_id' => $order->id, 'status' => "peding"])->update(['cleaner_id' => $data['cleaner_id']]);



                // if order already completed donot update the main order date // only update if order still not completed
                if ($order->status != 'completed') {
                    $order->date = $data['date'];
                }

            }
        }
        if ($request->post('time')) {
            if ($order->time != $data['time']) {
                $order->time = $data['time'];
                // if date changed & has recurring booking then need to change the Cleans timings too
                Cleans::where(['order_id' => $order->id])->update(['time' => $data['time']]);
            }
        }
        if ($request->post('hours')) {
            if ($order->hours != $data['hours']) {
                $order->hours = $data['hours'];
            }
        }
        if ($request->post('bedrooms')) {
            if ($order->bedrooms != $data['bedrooms']) {
                $order->bedrooms = $data['bedrooms'];
            }
        }
        if ($request->post('bathrooms')) {
            if ($order->bathrooms != $data['bathrooms']) {
                $order->bathrooms = $data['bathrooms'];
            }
        }
        if ($request->post('reason')) {
            if ($order->reason != $data['reason']) {
                $order->reason = $data['reason'];
            }
        }


        // update complex details
        if ($request->post('frequency')) {
            if ($order->frequency != $data['frequency']) {
                // if there has any subscription associated then update the frequency
                    if ($data['frequency'] == 'oneoff') {
                        // cancel the current subscription

                        // delete order histories those are pending ============
                        Cleans::where(['order_id' => $order->id, 'status' => 'pending'])->delete();

                    } else {


                        // remove old pending order histories and insert new ones according to the frequency ================
                        Cleans::where(['order_id' => $order->id, 'status' => 'pending'])->delete();
                        $stamp = strtotime($data['date'] . ' ' . $data['time']);
                        $REPEAT_CLEANS_ADD_IN_ADVANCED = env("REPEAT_CLEANS_ADD_IN_ADVANCED");
                        if ($data['frequency'] == 'weekly') {
                            // weekly
                            for ($i = 1; $i <= $REPEAT_CLEANS_ADD_IN_ADVANCED; $i++) {
                                $interval = '+' . $i . ' week';
                                $date = date('Y-m-d', strtotime($interval, $stamp));
                                if ($date > date('Y-m-d')) {
                                    Cleans::insert(['order_id' => $order->id, 'date' => $date, 'time' => $order->time, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                                }

                            }
                        } elseif ($data['frequency'] == 'biweekly') {
                            // bi weekly
                            for ($i = 1; $i <= $REPEAT_CLEANS_ADD_IN_ADVANCED; $i++) {
                                // if ($i % 2 == 0) {
                                    $interval = '+' . ($i*2) . ' week';
                                    $date = date('Y-m-d', strtotime($interval, $stamp));
                                    if ($date > date('Y-m-d')) {
                                        Cleans::insert(['order_id' => $order->id, 'date' => $date, 'time' => $order->time, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                                    }
                                // }
                            }
                        } elseif ($data['frequency'] == 'monthly') {
                            // monthly
                            for ($i = 1; $i <= $REPEAT_CLEANS_ADD_IN_ADVANCED; $i++) {
                                $interval = '+' . $i . ' month';
                                $date = date('Y-m-d', strtotime($interval, $stamp));
                                if ($date > date('Y-m-d')) {
                                    Cleans::insert(['order_id' => $order->id, 'date' => $date, 'time' => $order->time, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                                }

                            }
                        } elseif ($data['frequency'] == 'daily') {
                            // daily
                            for ($i = 1; $i <= $REPEAT_CLEANS_ADD_IN_ADVANCED; $i++) {
                                $interval = '+' . $i . ' day';
                                $date = date('Y-m-d', strtotime($interval, $stamp));
                                if ($date > date('Y-m-d')) {
                                    Cleans::insert(['order_id' => $order->id, 'date' => $date, 'time' => $order->time, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                                }

                            }
                        }


                        // ================================================
                        // get paid but not completed histories
                        $paidhistories = Cleans::where(['order_id' => $order->id, 'status' => 'paid'])->get();
                        $pendinghistories = Cleans::where(['order_id' => $order->id, 'status' => 'pending'])->get();
                        // transfer payment status to upcoming histories
                        $i = 0;
                        foreach ($paidhistories as $history) {
                            $transaction = Transaction::where(['order_id' => $order->id, 'order_cleans_id' => $history->id])->first();
                            if ($transaction) {
                                // update transaction service date
                                $trns = Transaction::findOrFail($transaction->id);
                                $trns->service_date = $pendinghistories[$i]->date;
                                $trns->order_cleans_id = $pendinghistories[$i]->id;
                                $trns->save();
                            }
                            // update pending to paid
                            $phst = Cleans::findOrFail($pendinghistories[$i]->id);
                            $phst->status = 'paid';
                            $phst->save();

                            // delete the paid history now
                            Cleans::where(['id' => $history->id, 'order_id' => $order->id])->delete();

                            $i++;
                        }


                    }
                $order->frequency = $data['frequency'];
            }
        }

        if ($request->post('payment_status')) {
            if ($order->payment_status != $data['payment_status']) {
                switch ($data['payment_status']) {
                    case 'paid':
                        $order->payment_status = $data['payment_status'];
                        break;
                    case 'pending':
                    case 'failed':
                    case 'refunded':
                        $order->payment_status = $data['payment_status'];
                        // cancel the subscription
                        break;
                }
            }
        }

        if ($request->post('status')) {

            if ($order->status != $data['status']) {

                switch ($data['status']) {
                    case 'pending':
                        break;
                    case 'paid':
                    case 'accepted':
                        // send notification to user & cleaner : TODO

                        // sending notification and email to the success cleaner
                        $cleaner = Cleaner::findOrFail($request->post('cleaner_id'));
                        $message = 'Good news! You have been accepted for the ' . $order->frequency . ' clean on ' . $order->date . '. You can see this clean in your "My Cleans" section in the app."';
                        $this->MailController->mailer('default', $cleaner->email, "You've been accepted to the order", ['id' => $order->id, 'name' => $cleaner->name, 'text' => $message]);
                        $this->PushController->send_notification_cleaner($order->id, $cleaner,"selected");

                        // send notification and email to the unsuccessful cleaners
                        $unsuccessfulCleaners = Cleaner::where(['status' => 1, 'available' => 'yes'])
                            ->whereDoesntHave('details', function ($query) {
                                $query->where('status', 'accepted');
                            })
                            ->get();
                        foreach ($unsuccessfulCleaners as $cleaner) {
                            $message = 'Unfortunately, you were not selected for the ' . $order->frequency . ' clean on ' . $order->date . '. Please keep an eye out for future order opportunities.';
                            $this->MailController->mailer('default', $cleaner->email, "You've been declined to the order", ['id' => $order->id, 'name' => $cleaner->name, 'text' => $message]);
                            $this->PushController->send_notification_cleaner($order->id, $cleaner, "cancelled");
                        }

                        $order->status = $data['status'];
                        break;
                    case 'completed':
                        // send notification to user for review : TODO
                        $order->status = $data['status'];

                        //******** check if the order has one transaction means fresh order created then mark the transaction's order status as completed for payout
                        Transaction::where('order_id', $order->id)->whereNull('order_cleans_id')->update(['order_status' => 'complete']);
                        break;
                    case 'cancelled-user':
                    case 'cancelled-admin':
                        // admin cancelling the order
                        $order->status = $data['status'];
                        // cancel the subscription

                        // delete pending order histories to cleanup the database
                        Cleans::where(['order_id' => $order->id, 'status' => 'pending'])->delete();

                        // send notification to user : TODO
                        $tokens = [];
                        if ($order->user->token) {
                            array_push($tokens, $order->user->token);
                            $this->PushController->custom_push($tokens, 'One of your booking on ' . date('d-m-Y', strtotime($order->date)) . ' has been cancelled by admin. Payment will be reversed.');
                        }


                        //refund the user : TODO
                        // check if user has only one transaction means fresh order created then refund
                        // get the payment intenet from transaction
                        $transactions_count = Transaction::where(['order_id' => $order->id, 'user_id' => $order->user_id])->count();
                        if ($transactions_count == 1) {
                            $transaction = Transaction::where(['order_id' => $order->id, 'user_id' => $order->user_id])->first();
                            // initiate full refund
                            $this->StripeController->refund($transaction->transaction_id, 'full');
                            $order->payment_status = 'refunded';
                        } else {
                            // no transaction found/not fresh order, hence no refund required from stripe
                        }
                        break;
                    // case 'cancelled-cleaner':
                    //     $order->status = $data['status'];
                    //     // cancel the subscription

                    // break;
                }
            }
        }

        if ($request->post('cleaner_id') && $request->post('cleaner_id') != '') {
            if ($order->cleaner_id != $data['cleaner_id']) {
                // if there has any subscription associated then update the cleaner also

                $order->cleaner_id = $data['cleaner_id'];
                Cleans::where('order_id',$order->id)->where('status', "pending")->update(['cleaner_id' => $data['cleaner_id']]);


                // if only one transaction is there for this order then assign the cleaner_id in transaction record
                $transactions = Transaction::where(['order_id' => $id])->get();
                if (count($transactions) == 1) {
                    // update cleaner to transaction
                    Transaction::where(['order_id' => $id])->update(['cleaner_id' => $request->post('cleaner_id')]);
                }

                // Delete notification logs
                Notification::where(['order_id' => $id])->delete();


                // TODO: Send notification to user
                // TODO: Update new cleaner's calender
                // TODO: Update old cleaner's calender
            }
        }

        $order->save();

        return redirect('/admin/order/' . $id)->with('success', 'Order updated successfully!');
    }

    public function accept_order($order_id, $cleaner_id)
    {
        $order = Order::findOrFail($order_id);
        $order->cleaner_id = $cleaner_id;
        $order->status = 'accepted';
        $order->save();

        Transaction::where(['order_id' => $order_id])->update(['cleaner_id' => $cleaner_id]);

        // return $order;

        Notification::where(['order_id' => $order_id])->delete();
        $this->PushController->send_notification_user($order->id, 'accepted');
        $this->PushController->send_notification_cleaner($order->id, 'accepted');

        return redirect('/admin/order/' . $order_id)->with('success', 'Order updated successfully!');
    }



    // ================ Banners
    public function banners(Request $request)
    {
        $banners = Banner::orderBy('id', 'desc')->get();
        return view('admin.banner.banners', ['banners' => $banners]);
    }

    public function createbanner_view()
    {
        return view('admin.banner.create');
    }

    public function createbanner(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'image' => 'required|image|mimes:jpeg,jpg,png,gif|max:10000',
            'type' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return redirect('/admin/create/banner')->with('error', $validator->messages());
        }

        $data = [
            'type' => $request->post('type'),
            'link' => $request->post('link'),
            'priority' => $request->post('priority'),
        ];

        // handle image upload
        if ($file = $request->file('image')) {
            $image_path = 'img_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $upload = $file->move($this->uploadPath . '/banner', $image_path);
            // set file name for storing into db
            $data['image'] = $image_path;
        }

        try {
            $save = Banner::create($data);
            return redirect('/admin/banners')->with('success', 'Banner Created successfully!');
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect('/admin/create/banner')->with('error', 'Banner creation Failed!');
        }
    }

    // ================ ExtraServices
    public function extraservices(Request $request)
    {
        $extraservices = ExtraService::orderBy('id', 'desc')->get();
        $currency = $this->UtilityController::Currencies()[env('CURRENCY')];
        return view('admin.service.services', ['extraservices' => $extraservices, 'currency' => $currency]);
    }

    public function createextraservice_view()
    {
        $currency = $this->UtilityController::Currencies()[env('CURRENCY')];
        return view('admin.service.create', ['currency' => $currency]);
    }

    public function createextraservice(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'name' => 'required',
            'price' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return redirect('/admin/create/extraservice')->with('error', $validator->messages());
        }

        $data = [
            'name' => $request->post('name'),
            'price' => $request->post('price'),
            'priority' => $request->post('priority'),
        ];

        try {
            $save = ExtraService::create($data);
            return redirect('/admin/extraservices')->with('success', 'Extra service created successfully!');
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect('/admin/create/extraservice')->with('error', 'Extra service creation Failed!');
        }
    }


    // ================ TaxSettings
    public function taxsettings(Request $request)
    {
        $taxsettings = TaxSetting::orderBy('id', 'desc')->get();
        return view('admin.tax.taxes', ['taxsettings' => $taxsettings]);
    }

    public function createtaxsetting_view()
    {
        return view('admin.tax.create');
    }

    public function createtaxsetting(Request $request)
    {
        $credentials = $request->all();
        $rules = [
            'label' => 'required',
            'type' => 'required',
            'value' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return redirect('/admin/create/taxsetting')->with('error', $validator->messages());
        }

        $data = [
            'label' => $request->post('label'),
            'type' => $request->post('type'),
            'value' => $request->post('value'),
            'priority' => $request->post('priority'),
        ];

        try {
            $save = TaxSetting::create($data);
            return redirect('/admin/taxsettings')->with('success', 'Tax setting created successfully!');
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect('/admin/create/taxsetting')->with('error', 'Tax setting creation Failed!');
        }
    }


    // export users
    public function export_users()
    {
        $csvExporter = new \Laracsv\Export();
        $users = User::with('orders')->get();

        // Register the hook before building
        $csvExporter->beforeEach(function ($user) {
            if (count($user->orders) > 0) {
                $user->orders = count($user->orders);
            } else {
                $user->orders = 0;
            }
            if ($user->status == 1) {
                $user->status = 'Active';
            } else {
                $user->status = 'Inactive';
            }
        });

        $csvExporter->build($users, ['id', 'name', 'email', 'phone', 'status', 'address', 'postcode', 'orders', 'subscription_amount', 'device']);
        $csvExporter->download('orders.csv');
        exit;
    }


    // export cleaners
    public function export_cleaners()
    {
        $csvExporter = new \Laracsv\Export();
        $cleaners = Cleaner::with('orders')->get();

        // Register the hook before building
        $csvExporter->beforeEach(function ($cleaner) {
            if (count($cleaner->orders) > 0) {
                $cleaner->orders = count($cleaner->orders);
            } else {
                $cleaner->orders = 0;
            }

            if ($cleaner->status == 1) {
                $cleaner->status = 'Active';
            } else {
                $cleaner->status = 'Inactive';
            }
        });

        $csvExporter->build($cleaners, ['id', 'name', 'email', 'phone', 'status', 'address', 'postcode', 'rating', 'orders', 'device']);
        $csvExporter->download('cleaners.csv');
        exit;
    }

    // export order
    public function export_orders($start = '', $end = '')
    {
        $csvExporter = new \Laracsv\Export();
        if ($start != '' && $end != '') {
            // trick => end date extend by one day to work proper in between
            $end_date = date('Y-m-d', strtotime($end . '+1 day'));

            // filter
            $orders = Order::with('cleaner')->with('user')->whereBetween('date', [$start, $end_date])->get();
        } else {
            // all
            $orders = Order::with('cleaner')->with('user')->get();
        }

        // Register the hook before building
        $csvExporter->beforeEach(function ($order) {
            $order->user = $order->user->name;
            if ($order->cleaner) {
                $order->cleaner = $order->cleaner->name;
            }
            $order->date = date('d-m-Y', strtotime($order->date));
        });

        $csvExporter->build($orders, ['id', 'user', 'cleaner', 'frequency', 'date', 'time', 'address', 'hours', 'bedrooms', 'bathrooms', 'status', 'reason', 'payment_status']);
        $csvExporter->download('orders.csv');
        exit;
    }

    // export orders
    public function export_transactions($start = '', $end = '')
    {
        $csvExporter = new \Laracsv\Export();
        if ($start != '' && $end != '') {
            // trick => end date extend by one day to work proper in between
            $end_date = date('Y-m-d', strtotime($end . '+1 day'));

            // filter
            $transactions = Transaction::with('cleaner')->with('user')->whereBetween('service_date', [$start, $end_date])->get();
        } else {
            // all
            $transactions = Transaction::with('cleaner')->with('user')->get();
        }

        // Register the hook before building
        $csvExporter->beforeEach(function ($transaction) {
            $transaction->user = $transaction->user->name;
            if ($transaction->cleaner) {
                $transaction->cleaner = $transaction->cleaner->name;
            }
            $transaction->service_date = date('d-m-Y', strtotime($transaction->service_date));
        });

        $csvExporter->build($transactions, ['id', 'user', 'cleaner', 'service_date', 'status', 'amount', 'stripe_customer_id', 'transaction_id', 'created_at']);
        $csvExporter->download('transactions.csv');
        exit;
    }


    // payouts
    public function payouts(Request $request)
    {
        $months = ['01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
        // default
        $year = date('Y');
        $month = date('m');

        $start = '';
        $end = '';

        if ($request->post('month')) {
            // update month
            $month = $request->post('month');
        }
        $monthExample = date('Y') . '-' . $month . '-01';
        $yearExample = date('Y') . '-' . $month . '-01';

        if ($request->post('start') != '') {
            // update start
            $start = $request->post('start');
        }
        if ($request->post('end') != '') {
            // update end
            $end = $request->post('end');
        }


        // get transactions as per service date new from transactions table
        if ($start == '' || $end == '') {
            // force empty both if even one is empty
            $start = '';
            $end = '';

            // get monthly data
            $transactions_total = Transaction::with(['cleaner'])->select(DB::raw('SUM(original_amount) as total, SUM(amount) as discounted_total, COUNT(*) as total_number_of_orders'), 'cleaner_id')->whereRaw('MONTH(service_date) = MONTH(\'' . $monthExample . '\') AND YEAR(service_date) = YEAR(\'' . $yearExample . '\')')->where('payout_status', 'pending')->where('order_status', 'complete')->groupBy('cleaner_id')->get();

            $transactions_full_refunded = Transaction::select(DB::raw('SUM(original_amount) as full_refunded, COUNT(*) as total_number_of_full_refunded_orders'), 'cleaner_id')->where('status', 'refunded')->whereRaw('MONTH(service_date) = MONTH(\'' . $monthExample . '\') AND YEAR(service_date) = YEAR(\'' . $yearExample . '\')')->where('payout_status', 'pending')->where('order_status', 'complete')->groupBy('cleaner_id')->get();

            $transactions_partial_refunded = Transaction::select(DB::raw('SUM(original_amount) as partial_refunded, COUNT(*) as total_number_of_partial_refunded_orders'), 'cleaner_id')->where('status', 'partial-refunded')->whereRaw('MONTH(service_date) = MONTH(\'' . $monthExample . '\') AND YEAR(service_date) = YEAR(\'' . $yearExample . '\')')->where('payout_status', 'pending')->where('order_status', 'complete')->groupBy('cleaner_id')->get();
        } else {
            // trick => end date extend by one day to work proper in between
            $end_date = date('Y-m-d', strtotime($end . '+1 day'));

            // get date range data
            $transactions_total = Transaction::with(['cleaner'])->select(DB::raw('SUM(original_amount) as total, SUM(amount) as discounted_total, COUNT(*) as total_number_of_orders'), 'cleaner_id')->whereRaw('MONTH(service_date) = MONTH(\'' . $monthExample . '\') AND YEAR(service_date) = YEAR(\'' . $yearExample . '\')')->where('payout_status', 'pending')->where('order_status', 'complete')->whereBetween('service_date', [$start, $end_date])->groupBy('cleaner_id')->get();

            $transactions_full_refunded = Transaction::select(DB::raw('SUM(original_amount) as full_refunded, COUNT(*) as total_number_of_full_refunded_orders'), 'cleaner_id')->where('status', 'refunded')->whereRaw('MONTH(service_date) = MONTH(\'' . $monthExample . '\') AND YEAR(service_date) = YEAR(\'' . $yearExample . '\')')->where('payout_status', 'pending')->where('order_status', 'complete')->whereBetween('service_date', [$start, $end_date])->groupBy('cleaner_id')->get();

            $transactions_partial_refunded = Transaction::select(DB::raw('SUM(original_amount) as partial_refunded, COUNT(*) as total_number_of_partial_refunded_orders'), 'cleaner_id')->where('status', 'partial-refunded')->whereRaw('MONTH(service_date) = MONTH(\'' . $monthExample . '\') AND YEAR(service_date) = YEAR(\'' . $yearExample . '\')')->where('payout_status', 'pending')->where('order_status', 'complete')->whereBetween('service_date', [$start, $end_date])->groupBy('cleaner_id')->get();
        }


        $custom_arr = [];

        foreach ($transactions_total as $transaction) {
            $cleaner_id = $transaction->cleaner_id;
            if ($cleaner_id) {
                $full_total = 0;
                $total_number_of_full_refunded_orders = 0;
                foreach ($transactions_full_refunded as $full) {
                    if ($full->cleaner_id == $cleaner_id) {
                        $full_total += $full->full_refunded;
                        $total_number_of_full_refunded_orders = $full->total_number_of_full_refunded_orders;
                    }
                }

                // get partial refund fee percentage
                $partial_percentage = Setting::where(['key' => 'paid_cancellation_charge'])->first();
                $partial_total = 0;
                foreach ($transactions_partial_refunded as $partial) {
                    if ($partial->cleaner_id == $cleaner_id) {
                        if ($partial_percentage) {
                            $partial_percentage = $partial_percentage->value;
                            $refunded = intval(($partial->partial_refunded * $partial_percentage) / 100);
                        } else {
                            // deduct 25% static
                            //$refunded = intval(($transaction->amount * 25)/100);
                            $refunded = intval(($partial->partial_refunded * 25) / 100);
                        }
                        $partial_total += $refunded;
                    }
                }


                // commision
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
                        //$total = floatval($transaction->total - ($transaction->total * $commission) / 100);
                        $total = $transaction->total;
                        $full_refunds = $full_total;
                        $partial_refunds = $partial_total;
                        $final_payout = ($total - ($full_refunds + $partial_refunds) > 0) ? floatval($total - ($full_refunds + $partial_refunds)) - (floatval($total - ($full_refunds + $partial_refunds)) * $commission) / 100 : 0;
                    } else {
                        // flat fee applicable

                        $total_fee = (($transaction->total_number_of_orders - $total_number_of_full_refunded_orders) * $commission);

                        $total = ($transaction->total > 0) ? floatval($transaction->total - $total_fee) : $transaction->total;
                        $full_refunds = $full_total;
                        $partial_refunds = $partial_total;
                        $final_payout = ($total - ($full_refunds + $partial_refunds) > 0) ? floatval($total - ($full_refunds + $partial_refunds)) : 0;
                    }

                } else {
                    // no commission deducted
                    $total = $transaction->total;
                    $full_refunds = $full_total;
                    $partial_refunds = $partial_total;
                    $final_payout = ($total - ($full_refunds + $partial_refunds) > 0) ? floatval($total - ($full_refunds + $partial_refunds)) : 0;
                }

                $obj = (object)array();
                $obj->cleaner_id = $cleaner_id;
                $obj->total = $total;
                $obj->full = $full_refunds;
                $obj->partial = $partial_refunds;
                $obj->payble = $final_payout;
                if ($transaction->cleaner_id) {
                    $obj->cleaner_name = $transaction->cleaner->name;
                } else {
                    $obj->cleaner_name = '';
                }

                // check if already paid or not
                $payment_status = Payout::where(['month' => $month, 'year' => $year, 'cleaner_id' => $cleaner_id])->first();
                if ($payment_status) {
                    $obj->status = $payment_status->status;
                } else {
                    // no record found so unpaid by default
                    $obj->status = 'pending';
                }

                array_push($custom_arr, $obj);
            }
        }

        return view('admin.payout.payouts', ['payouts' => $custom_arr, 'months' => $months, 'month' => $month, 'year' => $year, 'start' => $start, 'end' => $end]);
    }


    // ======== Documents
    public function documents()
    {
        $documents = CleanerData::with('cleaner')->orderBy('id', 'desc')->orderBy('status')->get();
        return view('admin.document.documents', ['documents' => $documents]);
    }

    public function document($id)
    {
        $document = CleanerData::findOrFail($id);
        $document->cleaner;
        return view('admin.document.document', ['document' => $document]);
    }

    public function updatedocument($id, Request $request)
    {
        $extra_message = "";

        $document = CleanerData::findOrFail($id);
        $cleaner_id = $document->cleaner_id;
        $cleaner = Cleaner::findOrFail($cleaner_id);
        $data = $request->all();

        // update normal details
        if ($request->post('reason')) {
            if ($document->reason != $data['reason']) {
                $document->reason = $data['reason'];
            }
        }
        if ($request->post('experience')) {
            if ($document->experience != $data['experience']) {
                $document->experience = $data['experience'];
            }
        }
        if ($request->post('bank')) {
            $document->bank_details = json_encode($request->post('bank'));
        }
        if ($request->post('status')) {
            if(!empty($data['stripe_acct_id'])){
                // Update Stripe Account Id which Admin has entered
                $cleaner->stripe_acct_id = $data['stripe_acct_id'];
                $cleaner->save();
            }else{
                    $id_proof       = $document->id_proof;
                    $address_proof  = $document->address_proof;
                    $id_proof_path = "";
                    $address_proof_path = "";
                    // if($id_proof == ""){
                    //     return redirect('/admin/document/'.$document->id)->with("error", "Please upload ID proof");
                    // }else{
                    //     $id_proof_path = "uploads/cleanerdata/".$document->id_proof;
                    //     if(!file_exists($id_proof_path)){
                    //         return redirect('/admin/document/'.$document->id)->with("error", "ID proof file does not exist. Please upload new ID proof");
                    //     }
                    // }

                    // if($address_proof == ""){
                    //     return redirect('/admin/document/'.$document->id)->with("error", "Please upload Address proof");
                    // }else{
                    //     $address_proof_path = "uploads/cleanerdata/".$document->address_proof;
                    //     if(!file_exists($address_proof_path)){
                    //         return redirect('/admin/document/'.$document->id)->with("error", "Address proof file does not exist. Please upload new address proof");
                    //     }
                    // }

                    $cleaner->stripe_acct_id = $data['stripe_acct_id'];
                    $cleaner->save();
                    // Create Strip Account
                    // Create Strip External Bank Account
                    $cleaner->stripe_acct_id = "";
                    $cleaner->save();
                    try {
                        $full_name = explode(" ", $cleaner->name);
                        $first_name = $cleaner->firstname;
                        $last_name = $cleaner->lastname;
                        if($first_name == ""){
                            $first_name = isset($full_name[0])?$full_name[0]:"";
                        }
                        if($last_name == ""){
                            $last_name = isset($full_name[1])?$full_name[1]:"";
                        }
                        $account_data["cleaner_id"] = $cleaner_id;
                        $account_data["email"] = $cleaner->email;
                        $account_data["address"] = $cleaner->address;
                        $account_data["dob"] = $cleaner->dob;
                        $account_data["firstname"] = $first_name;
                        $account_data["lastname"] = $last_name;
                        $account_data["phone"] = $cleaner->phone;
                        $account_data["postcode"] = $cleaner->postcode;
                        $account_data["bank_details"] = $document->bank_details;

                        $account_data["id_proof_path"] = $id_proof_path;
                        $account_data["address_proof_path"] = $address_proof_path;
                        $stripe_response = $this->StripeController->createStripeConnectAccount($account_data);
                        if($stripe_response["status"] == "success"){
                            $cleaner->stripe_acct_id = $stripe_response["id"];
                            $cleaner->save();
                            $extra_message = $stripe_response["message"];
                        }else{
                            return redirect('/admin/documents')->with("error", $stripe_response["message"]);
                        }
                    } catch (\Throwable $th) {
                        return redirect('/admin/documents')->with("error", $th->getMessage());
                    }
            }
            if ($document->status != $data['status']) {
                $document->status = $data['status'];

                // if status == accepted/rejected inform by email & notification
                if ($data['status'] == 'accepted') {
                    $mail = $this->MailController->mailer('default', $cleaner->email, env('APP_NAME') . ': Account verified', ['name' => $cleaner->name, 'text' => 'Welcome to ' . env('APP_NAME') . '!<br/><br/>Congratulations, your account has now been verified. You may now log in and start accepting orders through ' . env('APP_NAME') . '.']);
                    // send push
                    if ($cleaner->token) {
                        $tokens = [];
                        array_push($tokens, $cleaner->token);
                        $this->PushController->custom_push($tokens, 'You account has now been verified');
                    }

                    // if has no previous schedule records , update cleaner schedule automatically on accept
                    // all days 06 to 18 hrs.
                    $schedules = Schedule::where(['cleaner_id' => $cleaner_id])->get();
                    if ($schedules->count() == 0) {
                        $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
                        foreach ($days as $day) {
                            $times = $this->UtilityController::$Times;
                            $flag = 0;
                            $tempTimes = [];
                            foreach ($times as $time) {
                                if ('06:00:00' == $time) {
                                    $flag = 1;
                                }
                                if ($flag == 1) {
                                    array_push($tempTimes, $time);
                                }
                                if ('18:00:00' == $time) {
                                    $flag = 0;
                                }
                            }

                            // remove the last time from array as upto that time will be created records
                            array_pop($tempTimes);
                            // insert new records
                            foreach ($tempTimes as $single) {
                                $start_position = array_search($single, array_keys($times));
                                $end_position = $start_position + 1;
                                $end = $times[$end_position];
                                $ins = Schedule::insert(['cleaner_id' => $cleaner_id, 'day' => $day, 'start' => $single, 'end' => $end, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                            }
                        }
                    }
                } elseif ($data['status'] == 'rejected') {
                    $mail = $this->MailController->mailer('default', $cleaner->email, env('APP_NAME') . ': Account verification unsuccessful', ['name' => $cleaner->name, 'text' => 'We were unable to verify your documents. Please open the app to resubmit the documents.']);
                    // send push
                    if ($cleaner->token) {
                        $tokens = [];
                        array_push($tokens, $cleaner->token);
                        $this->PushController->custom_push($tokens, 'We were unable to verify your documents');
                    }
                }
            }
        }
        if ($request->post('distance')) {
            if ($cleaner->distance != $data['distance']) {
                $cleaner->distance = $data['distance'];
                $cleaner->save();
            }
        }

        $document->save();

        return redirect('/admin/documents')->with('success', 'Document updated successfully! '.$extra_message);
    }

    // push notifications
    public function push_view()
    {
        $types = ['all', 'user', 'cleaner'];
        return view('admin.push.create', ['types' => $types]);
    }

    public function sendpush(Request $request)
    {
        $types = ['all', 'user', 'cleaner'];
        $credentials = $request->all();
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'type' => ['required', Rule::in($types)],
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return redirect('/admin/push')->with('error', $validator->messages());
        }

        $tokens = [];
        switch ($request->post('type')) {
            case 'all':
                $users = User::whereNotNull('token')->get();
                $cleaners = Cleaner::whereNotNull('token')->get();
                foreach ($users as $user) {
                    array_push($tokens, $user->token);
                }
                foreach ($cleaners as $cleaner) {
                    array_push($tokens, $cleaner->token);
                }
                break;
            case 'user':
                $users = User::whereNotNull('token')->get();
                foreach ($users as $user) {
                    array_push($tokens, $user->token);
                }
                break;
            case 'cleaner':
                $cleaners = Cleaner::whereNotNull('token')->get();
                foreach ($cleaners as $cleaner) {
                    array_push($tokens, $cleaner->token);
                }
                break;

            default:
                return redirect('/admin/push')->with('error', 'Proper type must be selected');
                break;
        }

        // stripe id filter ======
        if ($request->post('stripe_id') == 'yes') {
            if ($request->post('type') == 'cleaner') {
                // not possible to filter cleaners with stripe ids
                return redirect('/admin/push')->with('error', 'Stripe id cannot be filtered on cleaner');
            } else {
                // get users with stripe id
                $users = User::whereNotNull('token')->whereNotNull('stripe_customer_id')->get();
                $tokens = [];
                foreach ($users as $user) {
                    array_push($tokens, $user->token);
                }
            }
        } elseif ($request->post('stripe_id') == 'no') {
            if ($request->post('type') == 'cleaner') {
                // not possible to filter cleaners with stripe ids
                return redirect('/admin/push')->with('error', 'Stripe id cannot be filtered on cleaner');
            } else {
                // get users without stripe id
                $users = User::whereNotNull('token')->whereNull('stripe_customer_id')->get();
                $tokens = [];
                foreach ($users as $user) {
                    array_push($tokens, $user->token);
                }
            }
        }

        //  ======== filter by order type & status
        if ($request->post('order_type') || $request->post('order_status')) {
            $tokens = [];
            $conditions = [];
            // order type filter
            if ($request->post('order_type')) {
                $conditions['frequency'] = $request->post('order_type');
            }
            // order status filter
            if ($request->post('order_status')) {
                $conditions['status'] = $request->post('order_status');
            }

            $with = ['user', 'cleaner'];

            $tokens = [];
            $results = Order::with($with)->where($conditions)->get();

            if ($request->post('stripe_id') == 'yes') {
                foreach ($results as $result) {
                    // check user with stripe id
                    if ($result->user) {
                        if ($result->user->stripe_customer_id) {
                            if ($request->post('type') == 'cleaner') {
                                return redirect('/admin/push')->with('error', 'Stripe id cannot be filtered on cleaner');
                            }
                            // check if token exists
                            if ($result->user->token) {
                                array_push($tokens, $result->user->token);
                            }
                        }
                    }
                }
            } elseif ($request->post('stripe_id') == 'no') {
                foreach ($results as $result) {
                    // check user with stripe id
                    if ($result->user) {
                        if (!$result->user->stripe_customer_id) {
                            if ($request->post('type') == 'cleaner') {
                                return redirect('/admin/push')->with('error', 'Stripe id cannot be filtered on cleaner');
                            }
                            // check if token exists
                            if ($result->user->token) {
                                array_push($tokens, $result->user->token);
                            }
                        }
                    }
                }
            } else {
                // no check for stripe id
                foreach ($results as $result) {
                    if ($request->post('type') == 'all' || $request->post('type') == 'user') {
                        // check user with token
                        if ($result->user) {
                            // check if token exists
                            if ($result->user->token) {
                                array_push($tokens, $result->user->token);
                            }
                        }
                    }
                    if ($request->post('type') == 'all' || $request->post('type') == 'cleaner') {
                        // check cleaner with token
                        if ($result->cleaner) {
                            // check if token exists
                            if ($result->cleaner->token) {
                                array_push($tokens, $result->cleaner->token);
                            }
                        }
                    }
                }
            }

        }

        // unique tokens only
        $tokens = array_values(array_unique($tokens));


        // return response()->json($tokens);

        // get icon name
        $icon = Setting::where(['key' => 'icon'])->first();
        if ($icon) {
            $icon = $icon->value;
        }


        if (count($tokens) > 0) {
            // send push notification to the tokens we get above
            $heading = array(
                "en" => $request->post('title')
            );
            $content = array(
                "en" => $request->post('description')
            );
            $fields = array(
                'app_id' => env('ONESIGNAL_APP_ID'),
                'include_player_ids' => $tokens,
                'headings' => $heading,
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
        }


        return redirect('/admin/push')->with('success', 'Push notification sent successfully!');


    }


}
