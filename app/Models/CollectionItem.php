<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'overview' => 'array',
        'core_ideas' => 'array',
    ];

    public function getLatitudeAttribute($value): ?float
    {
        return $value === null ? null : (float) $value;
    }

    public function getLongitudeAttribute($value): ?float
    {
        return $value === null ? null : (float) $value;
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
