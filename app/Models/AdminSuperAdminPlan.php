<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSuperAdminPlan extends Model
{
    use HasFactory;

    protected $table = "admin_super_admin_plans";

    public function splans()
    {
        return $this->hasMany('App\Models\AdminPlan','id','admin_plan_id');
    }
}
