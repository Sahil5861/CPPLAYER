<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageSlider extends Model
{
    use HasFactory;
    protected $table = 'language_slider';

    public function network()
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    
}   
