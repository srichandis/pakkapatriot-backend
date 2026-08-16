<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmazonProduct extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'description',
        'image_url',
        'asin',
        'price',
        'rating',
        'ratings_count',
        'sort_order',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'decimal:1',
        'ratings_count' => 'integer',
        'sort_order' => 'integer',
        'active' => 'boolean',
    ];

    /**
     * Amazon affiliate link for this product (uses the site's affiliate tag).
     */
    public function getLinkAttribute(): string
    {
        $tag = config('services.amazon.affiliate_tag', 'pakkapatriot05-21');

        return 'https://www.amazon.in/dp/' . urlencode($this->asin) . '?tag=' . urlencode($tag);
    }
}
