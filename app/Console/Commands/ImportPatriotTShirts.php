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

class ImportPatriotTShirts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:patriot-tshirts
        {--dir= : Path to the t-shirt images folder (default: <project>/public/tshirts)}
        {--force : Re-import even if the SKU already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Pakka Patriot t-shirts (one product per design with colour variants) into Bagisto';

    /**
     * Design folder => product meta. The folder contains one image per colour.
     */
    protected array $designs = [
        'tajmahal' => [
            'name' => 'Taj Mahal T-Shirt',
            'price' => 499,
            'description' => "Wear the world's greatest monument to love. This premium cotton tee features a minimal line-art print of the Taj Mahal — Bhārat's most recognised face — in a timeless all-over composition. Soft, breathable, and made to be lived in.",
            'short_description' => 'Premium cotton tee with Taj Mahal line-art print.',
            'slug' => 'taj-mahal-t-shirt',
            'sku' => 'pp-tshirt-tajmahal',
        ],
        'hampi' => [
            'name' => 'Hampi T-Shirt',
            'price' => 499,
            'description' => "The stone chariot of Hampi, frozen in motion on your chest. This premium cotton tee celebrates the ruined capital of the Vijayanagara empire — boulders, temples, and the chariot that has turned for five centuries — in a bold minimal print.",
            'short_description' => 'Premium cotton tee with Hampi stone chariot print.',
            'slug' => 'hampi-t-shirt',
            'sku' => 'pp-tshirt-hampi',
        ],
        'indiagate' => [
            'name' => 'India Gate T-Shirt',
            'price' => 499,
            'description' => "Forty-two metres of sandstone pride, worn close to the heart. This premium cotton tee features the India Gate — the war memorial at the heart of New Delhi — rendered in clean, bold lines. A tribute to the soldiers who gave everything for Bhārat.",
            'short_description' => 'Premium cotton tee with India Gate print.',
            'slug' => 'india-gate-t-shirt',
            'sku' => 'pp-tshirt-indiagate',
        ],
        'khajuraho' => [
            'name' => 'Khajuraho T-Shirt',
            'price' => 499,
            'description' => "A thousand years of sculpted stone, distilled into a tee. This premium cotton shirt carries the exquisite temple silhouette of Khajuraho — the UNESCO wonder where architecture became poetry — in a refined minimal print.",
            'short_description' => 'Premium cotton tee with Khajuraho temple print.',
            'slug' => 'khajuraho-t-shirt',
            'sku' => 'pp-tshirt-khajuraho',
        ],
        'konark' => [
            'name' => 'Konark T-Shirt',
            'price' => 499,
            'description' => "The Sun Temple's great wheel, spinning at the speed of Bhārat. This premium cotton tee features the legendary chariot wheel of Konark — a stone sundial of astonishing precision — in a powerful, graphic print.",
            'short_description' => 'Premium cotton tee with Konark Sun Temple wheel print.',
            'slug' => 'konark-t-shirt',
            'sku' => 'pp-tshirt-konark',
        ],
        'netaji' => [
            'name' => 'Netaji Subhas Chandra Bose T-Shirt',
            'price' => 499,
            'description' => "Carry the fire of the fearless. This premium cotton tee honours Netaji Subhas Chandra Bose — the leader who said 'Give me blood, and I will give you freedom' — with his portrait and immortal call to action.",
            'short_description' => 'Premium cotton tee with Netaji portrait and quote.',
            'slug' => 'netaji-t-shirt',
            'sku' => 'pp-tshirt-netaji',
        ],
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
        // Images live in the React project's public/tshirts (sibling of this Laravel app).
        $dir = $this->option('dir') ?: dirname(base_path()).'/public/tshirts';

        if (! is_dir($dir)) {
            $this->components->error("T-shirt images folder not found: {$dir}");
            $this->components->error('Pass a path with --dir= or add public/tshirts to the project root.');

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

        // Find the existing Merchandise > T-Shirts category (created by the Vivekananda import).
        $category = $this->findCategoryBySlug('t-shirts');
        if (! $category) {
            $this->components->error('T-Shirts category not found under Merchandise. Run import:vivekananda-merch once or create it in the admin.');

            return Command::FAILURE;
        }

        $this->newLine();
        $this->components->info('Importing Pakka Patriot t-shirts...');
        $this->components->twoColumnDetail('<fg=green>Images</>', $dir);

        $imported = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($this->designs as $design => $meta) {
            $designDir = $dir.'/'.$design;
            if (! is_dir($designDir)) {
                $this->components->twoColumnDetail("  – Folder missing: {$design}", '(skipped)');
                $skipped++;

                continue;
            }

            $images = collect(glob($designDir.'/*.png') ?: [])
                ->map(fn ($file) => basename($file))
                ->sort()
                ->values();

            if ($images->isEmpty()) {
                $this->components->twoColumnDetail("  – No images in: {$design}", '(skipped)');
                $skipped++;

                continue;
            }

            $sku = $meta['sku'];
            $existing = DB::table('products')->where('sku', $sku)->first();

            if ($existing && ! $this->option('force')) {
                // Self-heal: even when skipping an existing product, make sure its
                // colour images are attached (attachImages is idempotent).
                $product = $this->productRepository->find($existing->id);
                if ($product) {
                    $this->attachImages($product, $designDir, $images);
                }
                $this->components->twoColumnDetail("  – Skipped (exists): {$meta['name']}", $sku);
                $skipped++;

                continue;
            }

            try {
                $product = $this->createOrUpdateProduct(
                    $meta,
                    $existing,
                    $attributeFamily,
                    $defaultChannel,
                    $inventorySource,
                    $category->id,
                    $designDir,
                    $images
                );
                $this->components->twoColumnDetail("  ✓ {$meta['name']}", '#'.$product->id.' '.$sku.' ('.count($images).' colours)');
                $imported++;
            } catch (\Throwable $e) {
                $this->components->twoColumnDetail("  ✗ Failed: {$meta['name']}", $e->getMessage());
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
            $this->components->info('✅ Pakka Patriot t-shirts are live in the Bagisto store!');
        }

        return Command::SUCCESS;
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
     * Create (or update) a single t-shirt product with attributes, flat index,
     * inventory, category and all colour images.
     */
    protected function createOrUpdateProduct(
        array $meta,
        ?\stdClass $existing,
        \stdClass $attributeFamily,
        \stdClass $defaultChannel,
        \stdClass $inventorySource,
        int $categoryId,
        string $designDir,
        \Illuminate\Support\Collection $images
    ) {
        $data = [
            'type' => 'simple',
            'attribute_family_id' => $attributeFamily->id,
            'sku' => $meta['sku'],
        ];

        $attributeData = [
            'name' => $meta['name'],
            'description' => $meta['description'],
            'short_description' => $meta['short_description'],
            'url_key' => $meta['slug'],
            'price' => $meta['price'],
            'weight' => 0.3,
            'status' => 1,
            'visible_individually' => 1,
            'guest_checkout' => 1,
            'new' => 1,
            'featured' => 1,
            'manage_stock' => 1,
            'meta_title' => $meta['name'].' — Pakka Patriot',
            'meta_description' => $meta['short_description'],
            'meta_keywords' => 't-shirt, '.Str::slug($meta['name']).', pakka patriot, merchandise, bharat, india',
            'product_number' => $meta['sku'],
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
            $this->attachImages($product, $designDir, $images);

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

        $this->attachImages($product, $designDir, $images);

        return $product;
    }

    /**
     * Copy every colour variant image into the product's storage folder and
     * register them as product images (position 0 = the lightest/base colour).
     */
    protected function attachImages($product, string $designDir, \Illuminate\Support\Collection $images): void
    {
        // Prefer "white" as the primary (position 0), keep the rest alphabetical.
        $ordered = $images->sort(function ($a, $b) {
            $aWhite = str_contains($a, 'white') ? 0 : 1;
            $bWhite = str_contains($b, 'white') ? 0 : 1;

            return $aWhite === $bWhite ? strcmp($a, $b) : $aWhite - $bWhite;
        })->values();

        foreach ($ordered as $position => $filename) {
            $path = 'product/'.$product->id.'/'.$filename;

            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, file_get_contents($designDir.'/'.$filename));
            }

            $hasImage = DB::table('product_images')
                ->where('product_id', $product->id)
                ->where('path', $path)
                ->exists();

            if (! $hasImage) {
                $product->images()->create([
                    'type' => 'images',
                    'path' => $path,
                    'position' => $position,
                ]);
            }
        }
    }
}
