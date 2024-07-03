<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    //
    protected $fillable = [
        'rating', 'comment', 'cleaner_id', 'user_id', 'order_id'
    ];

    // relations =============================
    public function cleaner(){
        return $this->belongsTo('App\Models\Cleaner');
    }
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function order(){
        return $this->belongsTo('App\Models\Order');
    }
}
