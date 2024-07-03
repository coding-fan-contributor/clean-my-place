<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cleaner extends Model
{
    //
    protected $fillable = [
        'name', 'email', 'password', 'image', 'phone', 'status', 'address', 'postcode', 'latitude', 'longitude', 'code', 'token', 'device', 'rating', 'qualification', 'about', 'distance', 'account_status', 'dob'
    ];
    protected $hidden = [
        'password',
    ];


    // relations =============================
    public function ratings(){
        return $this->hasMany('App\Models\Rating');
    }
    public function orders(){
        return $this->hasMany('App\Models\Order');
    }
    public function transactions(){
        return $this->hasMany('App\Models\Transaction');
    }
    public function schedules(){
        return $this->hasMany('App\Models\Schedule');
    }
    public function payouts(){
        return $this->hasMany('App\Models\Payout');
    }
    public function details(){
        return $this->hasOne('App\Models\CleanerData');
    }

    // distance wise search users -> helper
    public function scopeIsWithinMaxDistance($query, $latitude, $longitude, $distance_type) {
        if($distance_type == 'km'){
            $var = 6371; // km
        }else{
            $var = 3958.8; // mile
        }
        $q = "($var * acos(cos(radians($latitude))
            * cos(radians(latitude))
            * cos(radians(longitude)
            - radians($longitude))
            + sin(radians($latitude))
            * sin(radians(latitude))))";
        return $query
           ->select('*') //pick the columns you want here.
           ->selectRaw("{$q} AS distance_unit")
           ->whereRaw("{$q} < distance"); // dynamic distance for users
   }
}
