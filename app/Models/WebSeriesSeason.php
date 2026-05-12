<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebSeriesSeason extends Model
{
    use HasFactory;

    protected $table = 'web_series_seasons';

    public function episodes(){
        return $this->hasMany(WebSeriesEpisode::class, 'season_id');
    }

}
