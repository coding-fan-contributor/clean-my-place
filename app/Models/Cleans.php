<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cleans extends Model
{
    //
    protected $table = 'cleans';

    protected $primaryKey = 'id';

    public $timestamps = true;


   // relations =============================
   // orders actually, it's an Orders table
    public function order(){
        return $this->belongsTo('App\Models\Order');
    }

}
