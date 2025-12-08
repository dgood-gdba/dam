<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetRevision extends Model
{
    protected $fillable = [
        'asset_id',
        'revision_number',
        'changed_by',
        'changed_at',
        'snapshot',
    ];

    protected $casts = [
        'snapshot' => 'array',
        'changed_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
