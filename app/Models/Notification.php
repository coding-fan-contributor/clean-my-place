<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
    protected $fillable = [
        'order_id', 'user_id', 'cleaner_id',
    ];

    // relations =============================
    public function order(){
        return $this->belongsTo('App\Models\Order');
    }
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function cleaner(){
        return $this->belongsTo('App\Models\Cleaner');
    }  
}
