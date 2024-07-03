<?php

namespace App\Http\Controllers;

use App\Models\Setting;

use Illuminate\Http\Request;

class WebController extends Controller
{
    //
    public function tnc(){
    	$data = Setting::where(['key' => 'terms'])->first()->value;
    	return view('tnc', ['data' => $data]);
    }
    public function cancellation(){
    	$data = Setting::where(['key' => 'cancellation'])->first()->value;
    	return view('cancellation-policy', ['data' => $data]);
    }
}
