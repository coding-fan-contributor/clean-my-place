<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CleanerData extends Model
{
    //
    protected $fillable = [ 'cleaner_id', 'id_proof', 'address_proof', 'bank_details', 'status', 'reason', 'experience'
    ];
    

    // relations =============================
    public function cleaner(){
        return $this->belongsTo('App\Models\Cleaner');
    }
}
