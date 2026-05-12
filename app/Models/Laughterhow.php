<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laughterhow extends Model
{
    use HasFactory;

    protected $table = 'laughter_show';

    use SoftDeletes;    
}
