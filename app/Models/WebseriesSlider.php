<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebseriesSlider extends Model
{
    use HasFactory;
    protected $table = 'webseries_slider';

    public function network()
    {
        return $this->belongsTo(ContentNetwork::class, 'content_network_id', 'id');
    }

    
}   
