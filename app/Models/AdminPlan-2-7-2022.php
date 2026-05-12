<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_number',
        'name',
        'logo',
        'background',
        'genre',
        'language',
        'stream_type',
        'channel_link',
        'backup_url',
        'status',
        'description',
    ];
}
