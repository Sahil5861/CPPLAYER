<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppDomainContent extends Model
{
    protected $table = 'app_domains_content';

    protected $fillable = [
        'admin_id',
        'domain',
        'content',
        'logo',
        'app_name',
        'theme_color',
        'live_channels',
        'movies',
        'webseries',
        'tvshow',
        'tvshow_pak',
        'kids_show',
        'religious',
        'sports',
        'stage_shows',
        'laughter_shows',
    ];

    // Mutator: when saving to DB
    public function setLiveChannelsAttribute($value)
    {
        $this->attributes['live_channels'] = is_array($value)
            ? implode(',', $value)
            : $value;
    }

    // Accessor: when retrieving from DB
    public function getLiveChannelsAttribute($value)
    {
        return explode(',', $value);
    }
}
