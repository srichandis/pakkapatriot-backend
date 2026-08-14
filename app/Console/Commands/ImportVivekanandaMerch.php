<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Helpers\Indexers\Flat as FlatIndexer;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Product\Repositories\ProductRepository;

class ImportVivekanandaMerch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:vivekananda-merch
        {--image= : Path to the Swami Vivekananda image (default: <project>/public/merchandise/swami.jpeg)}
        {--force : Re-import even if the SKU already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Swami Vivekananda merchandise (t-shirts, hoodies, mugs, etc.) into Bagisto';

    /**
     * Merchandise catalogue.
     *
     * NOTE: All Swami Vivekananda products (t-shirt, hoodie, mug, tote,
     * poster, stickers, notebook, cap) were removed from the store on request
     * and are intentionally no longer defined here, so re-running this
     * command will not re-import them.
     */
    protected array $products = [];

    /**
     * Category tree created if missing. Each child maps to a product category.
     */
    protected array $categoryTree = [
        'Merchandise' => [
            'T-Shirts',
            'Hoodies',
            'Mugs',
            'Tote Bags',
            'Posters',
            'Stickers',
            'Notebooks',
            'Caps',
        ],
    ];

    public function __construct(
        protected ProductRepository $productRepository,
        protected ProductAttributeValueRepository $attributeValueRepository,
        protected ProductInventoryRepository $productInventoryRepository,
        protected FlatIndexer $flatIndexer,
        protected CategoryRepository $categoryRepository
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $imagePath = $this->option('image') ?: dirname(base_path()).'/public/merchandise/swami.jpeg';

        if (! file_exists($imagePath)) {
            $this->components->error("Product image not found: {$imagePath}");
            $this->components->error('Pass a path with --image= or add public/merchandise/swami.jpeg to the project root.');

            return Command::FAILURE;
        }

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

        $this->newLine();
        $this->components->info('Importing Swami Vivekananda merchandise...');
        $this->components->twoColumnDetail('<fg=green>Image</>', $imagePath);

        $categoryIds = $this->ensureCategories();

        $imported = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($this->products as $row) {
            $sku = $row['sku'];
            $existing = DB::table('products')->where('sku', $sku)->first();

            if ($existing && ! $this->option('force')) {
                $this->components->twoColumnDetail("  – Skipped (exists): {$row['name']}", $sku);
                $skipped++;

                continue;
            }

            try {
                $product = $this->createOrUpdateProduct($row, $existing, $attributeFamily, $defaultChannel, $inventorySource, $categoryIds, $imagePath);
                $this->components->twoColumnDetail("  ✓ {$row['name']}", '#'.$product->id.' '.$sku);
                $imported++;
            } catch (\Throwable $e) {
                $this->components->twoColumnDetail("  ✗ Failed: {$row['name']}", $e->getMessage());
                $failed++;
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
            $this->components->info('✅ Vivekananda merchandise is live in the Bagisto store!');
        }

        return Command::SUCCESS;
    }

    /**
     * Create the "Merchandise" category tree if it doesn't already exist.
     * Returns a map of category name => id.
     */
    protected function ensureCategories(): array
    {
        $ids = [];
        $parentId = null;
        $position = 0;

        foreach ($this->categoryTree as $parentName => $children) {
            $parent = $this->findCategoryBySlug(Str::slug($parentName));

            if (! $parent) {
                $parent = $this->categoryRepository->create([
                    'parent_id' => null,
                    'name' => $parentName,
                    'slug' => Str::slug($parentName),
                    'description' => 'Swami Vivekananda merchandise curated by Pakka Patriot.',
                    'status' => 1,
                    'display_mode' => 'products_and_description',
                    'locale' => 'all',
                    'position' => $position++,
                ]);
                $this->components->twoColumnDetail('  + Category', $parentName);
            }

            $parentId = $parent->id;

            foreach ($children as $childName) {
                $child = $this->findCategoryBySlug(Str::slug($childName));

                if (! $child) {
                    $child = $this->categoryRepository->create([
                        'parent_id' => $parentId,
                        'name' => $childName,
                        'slug' => Str::slug($childName),
                        'description' => $childName.' — Swami Vivekananda merchandise by Pakka Patriot.',
                        'status' => 1,
                        'display_mode' => 'products_and_description',
                        'locale' => 'all',
                        'position' => $position++,
                    ]);
                    $this->components->twoColumnDetail('  + Category', $parentName.' / '.$childName);
                }

                $ids[$childName] = $child->id;
            }
        }

        return $ids;
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

    /**
     * Create (or update) a single product with attributes, flat index,
     * inventory, category and image.
     */
    protected function createOrUpdateProduct(
        array $row,
        ?\stdClass $existing,
        \stdClass $attributeFamily,
        \stdClass $defaultChannel,
        \stdClass $inventorySource,
        array $categoryIds,
        string $imagePath
    ) {
        $data = [
            'type' => 'simple',
            'attribute_family_id' => $attributeFamily->id,
            'sku' => $row['sku'],
        ];

        $attributeData = [
            'name' => $row['name'],
            'description' => $row['description'],
            'short_description' => $row['short_description'],
            'url_key' => $row['slug'],
            'price' => $row['price'],
            'weight' => 0.4,
            'status' => 1,
            'visible_individually' => 1,
            'guest_checkout' => 1,
            'new' => 1,
            'featured' => 1,
            'manage_stock' => 1,
            'meta_title' => $row['name'].' — Pakka Patriot',
            'meta_description' => $row['short_description'],
            'meta_keywords' => 'swami vivekananda, '.$row['category'].', pakka patriot, merchandise, india',
            'product_number' => $row['sku'],
        ];

        if ($existing) {
            $product = $this->productRepository->update($attributeData, $existing->id);
            $attributes = $product->attribute_family->custom_attributes;
            $this->attributeValueRepository->saveValues($attributeData, $product, $attributes);

            // Refresh flat index, channels, inventory and categories for the updated product.
            $this->flatIndexer->refresh($product);
            $product->channels()->sync([$defaultChannel->id]);
            $this->productInventoryRepository->saveInventories([
                'inventories' => [$inventorySource->id => 100],
            ], $product);
            $this->syncCategories($product, $row, $categoryIds);
            $this->attachImage($product, $imagePath);

            return $product;
        }

        $product = $this->productRepository->create($data);

        $attributes = $product->attribute_family->custom_attributes;
        $this->attributeValueRepository->saveValues($attributeData, $product, $attributes);

        // Populate product_flat (required for the public storefront + /api/products).
        $this->flatIndexer->refresh($product);

        $product->channels()->sync([$defaultChannel->id]);

        // Set inventory against the default source.
        $this->productInventoryRepository->saveInventories([
            'inventories' => [$inventorySource->id => 100],
        ], $product);

        $this->syncCategories($product, $row, $categoryIds);
        $this->attachImage($product, $imagePath);

        return $product;
    }

    /**
     * Assign the product to its category (e.g. T-Shirts, Hoodies).
     */
    protected function syncCategories($product, array $row, array $categoryIds): void
    {
        if (isset($categoryIds[$row['category']])) {
            $product->categories()->sync([$categoryIds[$row['category']]]);
        }
    }

    /**
     * Copy the Swami Vivekananda image into the product's storage folder and
     * register it as the product image.
     */
    protected function attachImage($product, string $imagePath): void
    {
        $filename = 'swami-vivekananda.jpeg';
        $path = 'product/'.$product->id.'/'.$filename;

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, file_get_contents($imagePath));
        }

        $hasImage = DB::table('product_images')->where('product_id', $product->id)->exists();

        if (! $hasImage) {
            $product->images()->create([
                'type' => 'images',
                'path' => $path,
                'position' => 0,
            ]);
        }
    }
}
