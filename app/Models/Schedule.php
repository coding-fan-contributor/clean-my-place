<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    //
    protected $fillable = [
        'cleaner_id', 'day', 'start', 'end',
    ];

    // relations =============================
    public function cleaner(){
        return $this->belongsTo('App\Models\Cleaner');
    }
}
