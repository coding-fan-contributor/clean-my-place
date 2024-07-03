<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraService extends Model
{
    //
    protected $fillable = [
        'name', 'price', 'priority',
    ];

    protected $hidden = [
       'priority', 'created_at', 'updated_at'
    ];
}
