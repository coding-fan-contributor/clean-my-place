<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    //
    protected $fillable = [
        'label', 'type', 'value', 'priority',
    ];

    protected $hidden = [
        'priority', 'created_at', 'updated_at'
    ];
}
