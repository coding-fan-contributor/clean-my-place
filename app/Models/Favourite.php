<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    //
    protected $fillable = [
        'user_id', 'cleaner_id',
    ];

    // relations =============================
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function cleaner(){
        return $this->belongsTo('App\Models\Cleaner');
    }
}