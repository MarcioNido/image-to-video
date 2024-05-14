<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoImage extends Model
{
    protected $fillable = ['video_id', 'path', 'order'];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

}
