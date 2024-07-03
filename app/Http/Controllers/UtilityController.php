<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Unirest;

class UtilityController extends Controller
{
    // times
    public static $Times = ['00:00:00', '01:00:00', '02:00:00', '03:00:00', '04:00:00', '05:00:00', '06:00:00', '07:00:00', '08:00:00', '09:00:00', '10:00:00', '11:00:00', '12:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00', '18:00:00', '19:00:00', '20:00:00', '21:00:00', '22:00:00', '23:00:00', '24:00:00'];

    // currencies
    public static function Currencies(){
        return ['GBP'=> '£', 'USD'=> '$', 'EUR'=> '€', 'INR'=> '₹', 'CAD'=> 'CA$', 'AED'=> 'AED', 'ARS'=> 'AR$', 'BRL'=> 'R$', 'CHF'=> 'CHF', 'CNY'=> 'CN¥', 'COP'=> 'CO$', 'CRC'=> '₡', 'HKD'=> 'HK$', 'IDR'=> 'RP', 'JPY'=> '¥', 'KRW'=> '₩', 'MYR'=> 'RM', 'NGN'=> '₦', 'NZD'=> 'NZ$', 'PHP'=> '₱', 'PKR'=> '₨', 'RUB'=> '₽', 'QAR'=> 'ر.ق', 'SAR'=> 'ر.س.‏', 'SEK'=> 'kr', 'SGD'=> 'S$', 'THB'=> '฿', 'TRY'=> '₺', 'TWD'=> 'NT$', 'UAH'=> '₴', 'VND'=> '₫', 'YER'=> 'ر.ي.', 'ZAR'=> 'R'];
    }

    // geocoding decoding
    public function getLatLong($address){
        $address = $address;
        $key = env('GOOGLE_API_KEY', false);
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key='.$key;
        if($key){
            Unirest\Request::verifyPeer(false);
            $res = Unirest\Request::get($url, $headers = array(), $body = null);
            if($res->code == 200 && count($res->body->results) > 0){
                return $res->body->results[0]->geometry->location;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }


}
