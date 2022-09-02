<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RpaType extends Model
{
    protected $table = 'rpa_type';
    
      public function rpa()
    {
        return $this->hasMany(Rpa::class,"id_rpa_type");
      
    }
}
