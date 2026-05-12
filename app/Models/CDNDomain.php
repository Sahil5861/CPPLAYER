<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CDNDomain extends Model
{
    use HasFactory;

    protected $fillable = ['cdn_setting_id', 'domain_name', 'url'];

    protected $table = 'domains';

    public function cdnSetting()
    {
        return $this->belongsTo(CdnSetting::class, 'cdn_setting_id');
    }
}


