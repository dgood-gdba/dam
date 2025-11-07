<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetComment extends Model
{
    protected $fillable = [
        'asset_id',
        'user_id',
        'comment',
        'asset_comment_id'
    ];

    protected function casts(): array
    {
        return [
            'comment' => 'array'
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(self::class, 'asset_comment_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
