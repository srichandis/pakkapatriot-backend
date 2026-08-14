<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreateActivity extends Model
{
    protected $guarded = [];

    protected $casts = [
        'known_for' => 'array',
        'try_this' => 'array',
        'related' => 'array',
    ];
}
