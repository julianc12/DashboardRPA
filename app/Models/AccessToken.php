<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    protected $table = 'oauth_access_tokens';
    public $incrementing = false;
}
