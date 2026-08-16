<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Product\Helpers\Indexers\Flat as FlatIndexer;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Product\Repositories\ProductRepository;

class ImportPatriotMerch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:patriot-merch
        {--force : Re-import even if the SKU already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone the Pakka Patriot t-shirt designs into every merchandise category (hoodies, mugs, tote bags, posters, stickers, notebooks, caps)';

    /**
     * Merchandise category slug => product meta. The design artwork from the
     * t-shirts is reused as the product image for every category.
     */
    protected array $categories = [
        'hoodies' => [
            'noun' => 'Hoodie',
            'price' => 899,
            'weight' => 0.6,
            'short_description' => 'Premium cotton-blend hoodie with {design} print.',
            'description' => 'Wrap yourself in {design}. This premium cotton-blend hoodie carries the same iconic print as our best-selling tee — soft, warm, and made for the cool Bhārat evenings.',
        ],
        'mugs' => [
            'noun' => 'Mug',
            'price' => 299,
            'weight' => 0.4,
            'short_description' => 'Ceramic mug with {design} print.',
            'description' => 'Start every morning with {design}. This 350 ml ceramic mug brings the iconic print from our best-selling tee to your desk — vivid, durable, and dishwasher-safe.',
        ],
        'tote-bags' => [
            'noun' => 'Tote Bag',
            'price' => 399,
            'weight' => 0.25,
            'short_description' => '100% cotton tote bag with {design} print.',
            'description' => 'Carry {design} everywhere. This sturdy 100% cotton tote bag features the same iconic print as our best-selling tee — roomy, reusable, and proudly made in Bhārat.',
        ],
        'posters' => [
            'noun' => 'Poster',
            'price' => 249,
            'weight' => 0.1,
            'short_description' => 'Museum-quality A3 poster with {design} print.',
            'description' => 'Celebrate {design} on your wall. This museum-quality A3 poster brings the iconic print from our best-selling tee to life — printed on 200 gsm matte art paper.',
        ],
        'stickers' => [
            'noun' => 'Sticker Pack',
            'price' => 99,
            'weight' => 0.05,
            'short_description' => 'Waterproof vinyl sticker pack with {design} print.',
            'description' => 'Stick {design} on everything. This durable vinyl sticker pack reuses the iconic print from our best-selling tee — waterproof, scratch-resistant, and made in Bhārat.',
        ],
        'notebooks' => [
            'noun' => 'Notebook',
            'price' => 249,
            'weight' => 0.3,
            'short_description' => '100-page lined notebook with {design} cover print.',
            'description' => 'Note it down with {design}. This 100-page lined notebook features the iconic print from our best-selling tee on its cover — premium 100 gsm paper with lay-flat binding.',
        ],
        'caps' => [
            'noun' => 'Cap',
            'price' => 399,
            'weight' => 0.2,
            'short_description' => 'Cotton-blend cap with {design} print.',
            'description' => 'Top it off with {design}. This premium cotton-blend cap carries the iconic print from our best-selling tee — adjustable fit, made in Bhārat.',
        ],
    ];

    /**
     * The t-shirt SKUs whose designs are cloned into every category.
     */
    protected array $tShirtSkus = [
        'pp-tshirt-tajmahal',
        'pp-tshirt-hampi',
        'pp-tshirt-indiagate',
        'pp-tshirt-khajuraho',
        'pp-tshirt-konark',
        'pp-tshirt-netaji',
    ];

    /**
     * Additional designs from public/design that have no t-shirt product yet.
     * They get the same treatment: one product per merch category.
     */
    protected array $extraDesigns = [
        'chanakya' => ['name' => 'Chanakya', 'url_key' => 'chanakya'],
        'savarkar' => ['name' => 'Savarkar', 'url_key' => 'savarkar'],
        'shivaji' => ['name' => 'Shivaji', 'url_key' => 'shivaji'],
    ];

    public function __construct(
        protected ProductRepository $productRepository,
        protected ProductAttributeValueRepository $attributeValueRepository,
        protected ProductInventoryRepository $productInventoryRepository,
        protected FlatIndexer $flatIndexer
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $attributeFamily = DB::table('attribute_families')->where('code', 'default')->first();
        if (! $attributeFamily) {
            $this->components->error('Default attribute family not found. Run migrations and seeders first.');

            return Command::FAILURE;
        }

        $defaultChannel = DB::table('channels')->where('code', 'default')->first()
            ?: DB::table('channels')->first();
        if (! $defaultChannel) {
            $this->components->error('No channels found. Set up channels first.');

            return Command::FAILURE;
        }

        $inventorySource = DB::table('inventory_sources')->where('status', 1)->first();
        if (! $inventorySource) {
            $this->components->error('No active inventory source found.');

            return Command::FAILURE;
        }

        // Source designs: the existing t-shirt products (457–462) in product_flat.
        $tShirts = DB::table('product_flat')
            ->whereIn('sku', $this->tShirtSkus)
            ->where('channel', $defaultChannel->code)
            ->where('locale', 'en')
            ->get()
            ->keyBy('sku');

        if ($tShirts->isEmpty()) {
            $this->components->error('No t-shirt products found. Run import:patriot-tshirts first.');

            return Command::FAILURE;
        }

        // Append designs that exist only in public/design (no t-shirt product).
        foreach ($this->extraDesigns as $slug => $meta) {
            $tShirts->put('pp-tshirt-'.$slug, (object) [
                'sku' => 'pp-tshirt-'.$slug,
                'name' => $meta['name'].' T-Shirt',
                'url_key' => $meta['url_key'],
                'product_id' => null,
            ]);
        }

        $imported = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($this->categories as $categorySlug => $categoryMeta) {
            $category = $this->findCategoryBySlug($categorySlug);
            if (! $category) {
                $this->components->twoColumnDetail("  – Category missing: {$categorySlug}", '(skipped)');
                $skipped++;

                continue;
            }

            foreach ($tShirts as $tShirt) {
                $design = $this->designFromTShirt($tShirt);

                // Keep in sync with createProduct() so re-runs are idempotent.
                $sku = 'pp-'.Str::slug($categoryMeta['noun']).'-'.$design['slug'];
                $existing = DB::table('products')->where('sku', $sku)->first();

                if ($existing && ! $this->option('force')) {
                    $this->components->twoColumnDetail("  – Skipped (exists): {$design['name']} {$categoryMeta['noun']}", $sku);
                    $skipped++;

                    continue;
                }

                try {
                    $product = $this->createProduct(
                        $tShirt,
                        $design,
                        $categoryMeta,
                        $category->id,
                        $existing,
                        $attributeFamily,
                        $defaultChannel,
                        $inventorySource
                    );

                    $this->components->twoColumnDetail(
                        "  ✓ {$design['name']} {$categoryMeta['noun']}",
                        '#'.$product->id.' '.$sku
                    );
                    $imported++;
                } catch (\Throwable $e) {
                    $this->components->twoColumnDetail(
                        "  ✗ Failed: {$design['name']} {$categoryMeta['noun']}",
                        $e->getMessage()
                    );
                    $failed++;
                }
            }
        }

        $this->newLine();
        $this->components->info('Import Summary');
        $this->newLine();
        $this->components->twoColumnDetail('Products Imported', (string) $imported);
        $this->components->twoColumnDetail('Products Skipped', (string) $skipped);
        $this->components->twoColumnDetail('Products Failed', (string) $failed);
        $this->newLine();

        if ($imported > 0) {
            $this->components->info('✅ Pakka Patriot merch is live in the Bagisto store!');
        }

        return Command::SUCCESS;
    }

    /**
     * Derive the design identity (slug, display name, artwork) from a t-shirt.
     */
    protected function designFromTShirt($tShirt): array
    {
        $name = preg_replace('/\s*T-Shirt\s*$/i', '', (string) $tShirt->name) ?: 'Design';
        $slug = Str::after((string) $tShirt->sku, 'pp-tshirt-');

        return [
            'slug' => $slug,
            'name' => $name,
            'url_key' => Str::replaceLast('-t-shirt', '', (string) $tShirt->url_key),
            'images' => $this->tShirtImages((int) $tShirt->product_id),
        ];
    }

    /**
     * The design artwork already stored for the t-shirt product.
     *
     * @return array<int, string> absolute storage paths
     */
    protected function tShirtImages(int $productId): array
    {
        return DB::table('product_images')
            ->where('product_id', $productId)
            ->orderBy('position')
            ->pluck('path')
            ->all();
    }

    /**
     * Create a merch product for one design + category, reusing the design
     * artwork from the t-shirt.
     */
    protected function createProduct(
        $tShirt,
        array $design,
        array $categoryMeta,
        int $categoryId,
        ?\stdClass $existing,
        \stdClass $attributeFamily,
        \stdClass $defaultChannel,
        \stdClass $inventorySource
    ) {
        $name = $design['name'].' '.$categoryMeta['noun'];
        $sku = 'pp-'.Str::slug($categoryMeta['noun']).'-'.$design['slug'];
        $urlKey = $design['url_key'].'-'.Str::slug($categoryMeta['noun']);

        $data = [
            'type' => 'simple',
            'attribute_family_id' => $attributeFamily->id,
            'sku' => $sku,
        ];

        $attributeData = [
            'name' => $name,
            'description' => Str::replace('{design}', $design['name'], $categoryMeta['description']),
            'short_description' => Str::replace('{design}', $design['name'], $categoryMeta['short_description']),
            'url_key' => $urlKey,
            'price' => $categoryMeta['price'],
            'weight' => $categoryMeta['weight'],
            'status' => 1,
            'visible_individually' => 1,
            'guest_checkout' => 1,
            'new' => 1,
            'featured' => 1,
            'manage_stock' => 1,
            'meta_title' => $name.' — Pakka Patriot',
            'meta_description' => $categoryMeta['short_description'],
            'meta_keywords' => Str::slug($categoryMeta['noun']).', '.Str::slug($design['name']).', pakka patriot, merchandise, bharat, india',
            'product_number' => $sku,
        ];

        if ($existing) {
            $product = $this->productRepository->update($attributeData, $existing->id);
            $attributes = $product->attribute_family->custom_attributes;
            $this->attributeValueRepository->saveValues($attributeData, $product, $attributes);

            $this->flatIndexer->refresh($product);
            $product->channels()->sync([$defaultChannel->id]);
            $this->productInventoryRepository->saveInventories([
                'inventories' => [$inventorySource->id => 100],
            ], $product);
            $product->categories()->sync([$categoryId]);
            $this->attachImages($product, $design['images']);

            return $product;
        }

        $product = $this->productRepository->create($data);

        $attributes = $product->attribute_family->custom_attributes;
        $this->attributeValueRepository->saveValues($attributeData, $product, $attributes);

        $this->flatIndexer->refresh($product);

        $product->channels()->sync([$defaultChannel->id]);

        $this->productInventoryRepository->saveInventories([
            'inventories' => [$inventorySource->id => 100],
        ], $product);

        $product->categories()->sync([$categoryId]);

        $this->attachImages($product, $design['images']);

        return $product;
    }

    /**
     * Copy the t-shirt design artwork into the new product's storage folder.
     *
     * @param  array<int, string>  $sourcePaths
     */
    protected function attachImages($product, array $sourcePaths): void
    {
        foreach ($sourcePaths as $position => $sourcePath) {
            $filename = basename($sourcePath);
            $path = 'product/'.$product->id.'/'.$filename;

            if (! Storage::disk('public')->exists($path) && Storage::disk('public')->exists($sourcePath)) {
                Storage::disk('public')->copy($sourcePath, $path);
            }

            $hasImage = DB::table('product_images')
                ->where('product_id', $product->id)
                ->where('path', $path)
                ->exists();

            if (! $hasImage && Storage::disk('public')->exists($path)) {
                $product->images()->create([
                    'type' => 'images',
                    'path' => $path,
                    'position' => $position,
                ]);
            }
        }
    }

    /**
     * Find a category by its translated slug.
     */
    protected function findCategoryBySlug(string $slug): ?\stdClass
    {
        return DB::table('categories as c')
            ->join('category_translations as ct', 'ct.category_id', '=', 'c.id')
            ->where('ct.slug', $slug)
            ->select('c.*')
            ->first();
    }

}
