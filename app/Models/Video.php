<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id', 'soundtrack_id', 'status', 'path', 'webhook'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function soundtrack(): BelongsTo
    {
        return $this->belongsTo(Soundtrack::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(VideoImage::class);
    }
}
