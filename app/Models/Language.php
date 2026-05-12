<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    public function slider()
    {
        return $this->hasMany(LanguageSlider::class, 'language_id', 'id')
            ->where(function ($q) {
                $q->whereNull('deleted_at')
                  ->orWhere('deleted_at', '0000-00-00 00:00:00');
            });
    }
}
