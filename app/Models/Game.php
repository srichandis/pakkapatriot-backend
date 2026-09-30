<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tags' => 'array',
    ];

    /**
     * Public URL of the uploaded cover image, or null when there is none.
     */
    public function getImageUrlAttribute(): ?string
    {
        $image = trim((string) $this->image);

        return $image === '' ? null : asset('storage/'.$image);
    }
}
