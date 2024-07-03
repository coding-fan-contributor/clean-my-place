<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CronLog extends Model
{
    //
    public $table = 'cron_log';

    protected $fillable = [
        'id', 'cron_name', 'create_date'
    ];  
}
