<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetProperty extends Model
{
    protected $fillable = [
        'asset_id',
        'name',
        'type',
        'language',
        'value'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
