<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    public $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'apple_id', 'image', 'phone', 'status', 'house_number', 'address', 'postcode', 'latitude', 'longitude', 'code', 'token', 'device', 'subscription', 'subscription_amount', 'stripe_customer_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        //'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //'email_verified_at' => 'datetime',
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
    public function favourites(){
        return $this->hasMany('App\Models\Favourite');
    }

}
