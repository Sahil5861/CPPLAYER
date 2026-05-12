<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdnSetting extends Model
{
    use HasFactory;

    public function domains(){
        return $this->hasMany(CDNDomain::class, 'cdn_setting_id');
    }

}
