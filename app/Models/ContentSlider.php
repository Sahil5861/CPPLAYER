<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentSlider extends Model
{
    use HasFactory;
    protected $table = 'content_network_slider';

    public function network()
    {
        return $this->belongsTo(ContentNetwork::class, 'content_network_id', 'id');
    }

    
}   
