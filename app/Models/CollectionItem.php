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

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
