<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPlanDetails extends Model
{
    use HasFactory;

    protected $table = "user_plan_details";

    public function plan()
    {
        return $this->hasOne(ClientUser::class,'id','user_id');
    }
}
