<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    public $table = 'orders';

    protected $fillable = [
        'user_id', 'cleaner_id', 'date', 'time', 'frequency', 'status', 'address', 'postcode', 'hours', 'reason', 'bedrooms', 'bathrooms', 'payment_status',
        'total', 'price', 'latitude', 'longitude',
        'extra_services', 'tax_applied',
    ];

    protected $casts = [
        'extra_services' => 'array',
        'tax_applied' => 'array',
    ];

    // relations =============================
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function cleaner(){
        return $this->belongsTo('App\Models\Cleaner');
    }
    public function transactions(){
        return $this->hasMany('App\Models\Transaction');
    }
    public function rating(){
        return $this->hasOne('App\Models\Rating');
    }

    public function notifications(){
        return $this->hasMany('App\Models\Notification');
    }

    public function cleans(){
        return $this->hasMany('App\Models\Cleans');
    }
}
