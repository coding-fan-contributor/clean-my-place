<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    protected $fillable = [
        'user_id', 'cleaner_id', 'order_id', 'stripe_customer_id', 'status', 'transaction_id', 'data', 'original_amount', 'amount', 'order_cleans_id', 'service_date',
        //'subscription_id',
    ];

    // relations =============================
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function cleaner(){
        return $this->belongsTo('App\Models\Cleaner');
    }
    public function order(){
        return $this->belongsTo('App\Models\Order');
    }
    
    public function ordercleans(){
        return $this->belongsTo('App\Models\Cleans', 'order_cleans_id');
    }
}
