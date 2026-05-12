<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerAdminPlan extends Model
{
    use HasFactory;

    protected $table = "reseller_admin_plans";

    public function splans()
    {
        return $this->hasMany('App\Models\ResellerPlan','id','reseller_plan_id');
    }
}
