<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_number',
        'channel_name',
        'channel_logo',
        'channel_bg',
        'genres',
        'channel_language',
        'stream_type',
        'channel_link',
        'backup_url',
        'status',
        'channel_description',
    ];

    public function language()
    {
        return $this->hasOne(Language::class,'id','channel_language');
    }
}
