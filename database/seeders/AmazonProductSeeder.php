<?php

namespace Database\Seeders;

use App\Models\AmazonProduct;
use Illuminate\Database\Seeder;

class AmazonProductSeeder extends Seeder
{
    /**
     * Curated starter set of made-in-India products on Amazon.in.
     *
     * Images are served from Amazon's public image CDN keyed by ASIN,
     * so no manual image upload is needed.
     */
    public function run(): void
    {
        $products = [
            // ── Traditional Wear ────────────────────────────────────────────────
            [
                'name' => 'Pure Khadi Cotton Kurta for Men — Traditional Ethnic Wear',
                'category' => 'Traditional Wear',
                'description' => 'Classic knee-length khadi cotton kurta with side slits, button placket and mandarin collar — handwoven in Bhārat.',
                'asin' => 'B0FKN8PDVN',
                'rating' => null,
                'ratings_count' => null,
            ],
            [
                'name' => 'Hand-Woven Khadi Long Kurta — Printed Ethnic Wear',
                'category' => 'Traditional Wear',
                'description' => 'Printed long kurta in hand-woven khadi with mandarin collar and full sleeves. 100% cotton.',
                'asin' => 'B0DTN76NW5',
                'rating' => 5.0,
                'ratings_count' => 4,
            ],

            // ── Banarasi Silk ──────────────────────────────────────────────────
            [
                'name' => 'Women\'s Banarasi Silk Saree with Zari Work',
                'category' => 'Banarasi Silk',
                'description' => 'Luxurious Banarasi silk saree with intricate zari borders — woven by the master weavers of Varanasi.',
                'asin' => 'B084R81QWP',
                'price' => '₹3,488',
                'rating' => null,
                'ratings_count' => null,
            ],
            [
                'name' => 'Banarasi Silk Saree — Navy Blue, Wedding & Party Wear',
                'category' => 'Banarasi Silk',
                'description' => 'Navy blue Banarasi silk saree with woven work — 5.5 m saree with 0.8 m unstitched blouse piece.',
                'asin' => 'B0H9KLJ25H',
                'rating' => null,
                'ratings_count' => null,
            ],

            // ── Ayurveda & Wellness ────────────────────────────────────────────
            [
                'name' => 'Dabur Chyawanprash — 1.5 kg | 3X Immunity Action',
                'category' => 'Ayurveda & Wellness',
                'description' => 'Time-honoured ayurvedic supplement with 40+ herbs. Helps build strength, stamina and overall health.',
                'asin' => 'B09J7YY7CJ',
                'rating' => 4.4,
                'ratings_count' => 58260,
            ],
            [
                'name' => 'Dabur Chyawanprash — 500 g | 3X Immunity Action',
                'category' => 'Ayurveda & Wellness',
                'description' => 'The classic chyawanprash in a handy 500 g jar — same 40+ ayurvedic herbs, great for travel and gifting.',
                'asin' => 'B09PBCK7LX',
                'rating' => 4.4,
                'ratings_count' => 58262,
            ],

            // ── Spices & Masala ────────────────────────────────────────────────
            [
                'name' => 'MDH Rajmah Masala 100 g (Pack of 2)',
                'category' => 'Spices & Masala',
                'description' => 'Authentic MDH spice blend for a rich, homestyle rajma — from Bhārat\'s most loved masala makers.',
                'asin' => 'B07K8K6GHC',
                'price' => '₹180',
                'rating' => null,
                'ratings_count' => null,
            ],

            // ── Dhokra Art ──────────────────────────────────────────────────────
            [
                'name' => 'Handcrafted Dhokra Art Peacock — Brass Showpiece',
                'category' => 'Dhokra Art',
                'description' => 'Ancient lost-wax (dhokra) casting from tribal Bhārat. Intricate brass peacock showpiece for home decor.',
                'asin' => 'B0BRCK36TQ',
                'rating' => 4.0,
                'ratings_count' => 1,
            ],
            [
                'name' => 'Dhokra Brass Net Peacock — Handcrafted Sculpture',
                'category' => 'Dhokra Art',
                'description' => '100% handmade dhokra peacock using the traditional lost-wax technique. A timeless piece of tribal craft.',
                'asin' => 'B08GM9WZYC',
                'rating' => 4.7,
                'ratings_count' => 10,
            ],

            // ── Pooja & Lamps ──────────────────────────────────────────────────
            [
                'name' => 'Brass Diya for Pooja — Traditional Oil Lamp for Home Temple',
                'category' => 'Pooja & Lamps',
                'description' => 'Handmade brass diya, the traditional oil lamp that lights up every home and temple across Bhārat.',
                'asin' => 'B0G1HGG5VJ',
                'rating' => null,
                'ratings_count' => null,
            ],
            [
                'name' => 'Pure Brass Diya — Traditional Oil Puja Lamp',
                'category' => 'Pooja & Lamps',
                'description' => 'Pure brass oil lamp for temple, mandir, festival decoration and Diwali gifting.',
                'asin' => 'B0FT3XNZZ9',
                'rating' => 4.3,
                'ratings_count' => 72,
            ],

            // ── Idols & Murtis ─────────────────────────────────────────────────
            [
                'name' => 'Brass Statue of Lord Vishnu — Home Temple Decor Gift',
                'category' => 'Idols & Murtis',
                'description' => 'Beautifully detailed brass Vishnu idol, hand-finished for home temples, decor and festive gifting.',
                'asin' => 'B0C1CHKNCV',
                'rating' => 5.0,
                'ratings_count' => 1,
            ],
            [
                'name' => 'Brass Tirupati Bala Ji God Idol Statue (1 kg)',
                'category' => 'Idols & Murtis',
                'description' => 'Solid brass Tirupati Balaji idol — a devotional centrepiece for home mandirs and gifts.',
                'asin' => 'B0DGXX691C',
                'rating' => null,
                'ratings_count' => null,
            ],
        ];

        foreach ($products as $index => $product) {
            $product['image_url'] = "https://images-na.ssl-images-amazon.com/images/P/{$product['asin']}.01._SCLZZZZZZZ_.jpg";
            $product['sort_order'] = $index;

            AmazonProduct::updateOrCreate(
                ['asin' => $product['asin']],
                $product
            );
        }
    }
}
