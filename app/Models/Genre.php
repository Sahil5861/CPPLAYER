<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    public function channels()
    {
        return $this->hasMany('App\Models\ChannelGenre')->join('channels', 'channels.id', '=', 'channel_genre.channel_id')->where('channels.status',1)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc');
    }

    public function channelspopular()
    {
        return $this->hasMany('App\Models\ChannelGenre')->join('channels', 'channels.id', '=', 'channel_genre.channel_id')->where('channels.status',1)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc');
    }

    public function netadminchannels(){
        return $this->hasMany('App\Models\ChannelGenre')->join('channels', 'channels.id', '=', 'channel_genre.channel_id')->where('channels.status',1)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc');
    }
}
