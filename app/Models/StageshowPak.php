<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StageshowPak extends Model
{
    use HasFactory;

    protected $table = 'stage_shows_pak';

    use SoftDeletes;

    public function genres(){
        return $this->belongsToMany(Genre::class, 'movie_genres', 'movie_id', 'genre_id');
    }
    public function networks(){
        return $this->belongsToMany(ContentNetwork::class, 'movie_content_network', 'movie_id', 'network_id');
    }
}
