<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class PublicAuthController extends Controller
{

    // ========== login admin
    public function login_admin(Request $request){
        $email = $request->post('email');
        $password = $request->post('password');

        $admin = Admin::where(['email'=> $email, 'status'=> 1])->first();
        if($admin){
            $check = Hash::check($password, $admin->password);
            if($check){
                // generate session
                $request->session()->put('admin', $admin);

                return redirect('/admin/dashboard');
            }else{
                return redirect('/admin/login');
            }
        }else{
            return redirect('/admin/login');
        }
    }



    // ========== login user
    public function login_user(Request $request){
        $email = $request->post('email');
        $password = $request->post('password');

        $user = User::where(['email'=> $email, 'status'=> 1])->first();
        if($user){
            $check = Hash::check($password, $user->password);
            if($check == true){
                // generate session
                $request->session()->put('user', $user);
                return redirect('/dashboard');
            }else{
                return redirect('/login')->with('error', 'Invalid login credentials entered!');
            }
        }else{
            return redirect('/login')->with('error', 'User not found!');
        }
    }
}
