<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rpa extends Model {

    protected $table = 'rpa';

    public function rpaCommand() {
        return $this->hasMany(RpaCommand::class, 'id_rpa');
    }
      public function comments()
    {
        return $this->hasMany(RpaCommand::class);
    }
    

}
