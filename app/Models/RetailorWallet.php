<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailorWallet extends Model
{
    use HasFactory;

    protected $table = "credit_debit_retailor_amount";

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
        return $this->hasOne(ClientUser::class,'id','user_id');
    }
}
