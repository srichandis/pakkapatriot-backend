<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    protected $guarded = [];

    public function scopeSearch($query, string $term)
    {
        return $query->where('email', 'like', "%{$term}%");
    }
}
