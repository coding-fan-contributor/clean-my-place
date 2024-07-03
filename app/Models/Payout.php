<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    //
    protected $fillable = [
        'cleaner_id', 'year', 'month', 'amount', 'status',
    ];

    // relations =============================
    public function cleaner(){
        return $this->belongsTo('App\Models\Cleaner');
    }
}
