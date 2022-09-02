<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RpaOrquestacion extends Model
{
    protected $table = 'rpa_orquestacion';
    
      public function rpa() {
        return $this->hasMany(OrqRpa::class, 'idorquestacion');
    }
}
