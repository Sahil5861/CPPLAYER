<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class SadminWallet extends Model
{
    use HasFactory;

    protected $table = "credit_debit_amounts";

    protected $fillable = [
        'user_id',
        'credit_amount',
        'debit_amount',
        'credit_amount_by',
        'amount_method',
        'message',
    ];

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

}
