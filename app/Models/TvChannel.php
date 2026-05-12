<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvChannel extends Model
{
    use HasFactory;

    protected $table = 'tv_channels';

    public function networks(){
        return $this->belongsToMany(ContentNetwork::class, 'tv_show_content_network', 'show_id', 'network_id');
    }
}
