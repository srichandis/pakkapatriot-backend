<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JourneySubmission extends Model
{
    protected $guarded = [];

    protected $casts = [
        'age' => 'integer',
        'interests' => 'array',
    ];

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%");
        });
    }
}
