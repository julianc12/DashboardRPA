<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RpaCommand extends Model {

    protected $table = 'rpa_command';
    

   public function rpa()
    {
	return $this->belongsTo(Rpa::class);      
    }    

}