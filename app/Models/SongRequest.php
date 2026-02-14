<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SongRequest extends Model
{
    protected $fillable = ['song_id', 'song_title', 'song_artist', 'requester_name', 'message', 'status'];
}
