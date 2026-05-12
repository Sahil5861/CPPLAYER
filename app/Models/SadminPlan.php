<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SadminPlan extends Model
{
    use HasFactory;

    protected $table = "super_admin_plans";

    public function getChannel(){
        return $this->hasMany('App\Models\PackageChannel','plan_id','id')->leftJoin('channels', 'channels.id','=','package_channels.channel_id');
    }
    
} 
