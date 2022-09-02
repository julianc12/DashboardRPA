<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';

    public function transactionDetail()
    {
        return $this->hasMany(TransactionDetail::class,"id_transaction");
    }
}
