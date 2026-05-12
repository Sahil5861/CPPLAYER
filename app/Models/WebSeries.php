<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebSeries extends Model
{
    use HasFactory;

    use SoftDeletes;

    public function genres(){
        return $this->belongsToMany(Genre::class, 'web_series_genres', 'web_series_id', 'genre_id');
    }
    
    public function networks(){
        return $this->belongsToMany(ContentNetwork::class, 'web_series_content_network', 'webseries_id', 'network_id');
    }

    public function seasons(){
        return $this->hasMany(WebSeriesSeason::class, 'web_series_id');
    }

    public function sliders(){
        return $this->hasMany(WebseriesSlider::class,'webseries_id');
    }
}
