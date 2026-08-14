<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Product\Helpers\Indexers\Flat as FlatIndexer;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Product\Repositories\ProductImageRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Product\Repositories\ProductRepository;

class ImportFromWeToyToyCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:wetoytoy
        {file : Path to the WeToyToy products CSV file}
        {--force : Re-import even if previously imported (by SKU)}
        {--limit= : Max number of products to import}
        {--download-images : Download product images from URLs (disabled by default)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from WeToyToy CSV export into Bagisto';

    /**
     * Counters for import summary.
     */
    protected array $counters = [
        'products_imported' => 0,
        'products_skipped' => 0,
        'products_failed' => 0,
        'images_downloaded' => 0,
        'images_failed' => 0,
    ];

    /**
     * Create a new command instance.
     */
    public function __construct(
        protected ProductRepository $productRepository,
        protected ProductAttributeValueRepository $attributeValueRepository,
        protected ProductImageRepository $productImageRepository,
        protected ProductInventoryRepository $productInventoryRepository,
        protected AttributeRepository $attributeRepository,
        protected FlatIndexer $flatIndexer
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (! file_exists($filePath)) {
            $this->components->error("File not found: {$filePath}");

            return Command::FAILURE;
        }

        $this->newLine();
        $this->components->info('Importing products from WeToyToy CSV...');
        $this->components->twoColumnDetail('<fg=green>File</>', $filePath);
        $this->newLine();

        // Read CSV
        $rows = $this->parseCSV($filePath);

        if (empty($rows)) {
            $this->components->error('No valid rows found in CSV.');

            return Command::FAILURE;
        }

        $this->components->twoColumnDetail('<fg=green>Total rows in CSV</>', (string) count($rows));

        // Check if Bagisto is set up
        $attributeFamily = DB::table('attribute_families')->where('code', 'default')->first();
        if (! $attributeFamily) {
            $this->components->warn('Default attribute family not found. Please run migrations and seeders first.');

            return Command::FAILURE;
        }

        $defaultChannel = DB::table('channels')->where('code', 'default')->first()
            ?: DB::table('channels')->first();

        if (! $defaultChannel) {
            $this->components->error('No channels found. Please set up channels first.');

            return Command::FAILURE;
        }

        $defaultInventorySource = DB::table('inventory_sources')
            ->where('status', 1)
            ->first();

        // Fetch existing Bagisto categories keyed by name for quick lookup
        $bagistoCategories = DB::table('categories')->get()->keyBy('name');

        $limit = (int) ($this->option('limit') ?: 0);
        $imported = 0;
        $downloadImages = (bool) $this->option('download-images');

        foreach ($rows as $row) {
            if ($limit && $imported >= $limit) {
                break;
            }

            $this->importSingleProduct($row, $attributeFamily, $bagistoCategories, $defaultChannel, $defaultInventorySource, $downloadImages);
            $imported++;
        }

        $this->newLine();
        $this->printSummary();

        return Command::SUCCESS;
    }

    /**
     * Parse a CSV file into an array of associative arrays.
     */
    protected function parseCSV(string $filePath): array
    {
        $rows = [];

        if (($handle = fopen($filePath, 'r')) === false) {
            $this->components->error("Cannot open file: {$filePath}");

            return [];
        }

        // Read header
        $headers = fgetcsv($handle);

        if ($headers === false || empty($headers)) {
            fclose($handle);

            return [];
        }

        // Clean BOM and trim headers
        $headers = array_map(function ($header) {
            return trim(preg_replace('/^\xEF\xBB\xBF/', '', $header));
        }, $headers);

        while (($data = fgetcsv($handle)) !== false) {
            $row = [];

            foreach ($headers as $index => $header) {
                if (isset($data[$index])) {
                    $row[$header] = trim($data[$index]);
                } else {
                    $row[$header] = '';
                }
            }

            // Only add rows that have at least a title or SKU
            if (! empty($row['Title']) || ! empty($row['SKU'])) {
                $rows[] = $row;
            }
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Map WeToyToy product type to Bagisto product type.
     */
    protected function mapProductType(string $type): string
    {
        $type = strtolower(trim($type));

        $map = [
            'simple' => 'simple',
            'variable' => 'simple',
            'variation' => 'simple',
            'board games' => 'simple',
            'premium' => 'simple',
            '' => 'simple',
        ];

        return $map[$type] ?? 'simple';
    }

    /**
     * Import a single product from a CSV row.
     */
    protected function importSingleProduct(
        array $row,
        \stdClass $attributeFamily,
        \Illuminate\Support\Collection $bagistoCategories,
        \stdClass $defaultChannel,
        ?\stdClass $defaultInventorySource,
        bool $downloadImages
    ): void {
        $sku = ! empty($row['SKU']) ? $row['SKU'] : 'wetoytoy-'.Str::slug($row['Handle'] ?: $row['Title']);
        $slug = ! empty($row['Handle']) ? $row['Handle'] : Str::slug($row['Title']);
        $title = html_entity_decode($row['Title'] ?? '', ENT_QUOTES, 'UTF-8');

        if (empty($title)) {
            $this->counters['products_skipped']++;

            return;
        }

        // Check if product already exists
        $existingProduct = DB::table('products')->where('sku', $sku)->first();
        if ($existingProduct && ! $this->option('force')) {
            $this->counters['products_skipped']++;

            return;
        }

        try {
            $productType = $this->mapProductType($row['Product Type'] ?? '');

            // Parse prices
            $price = (float) str_replace([',', '₹', ' '], '', $row['Price (₹)'] ?? 0);
            $compareAtPrice = (float) str_replace([',', '₹', ' '], '', $row['Compare At Price (₹)'] ?? 0);
            $salePrice = null;

            // If compare at price is higher than price, use it as regular price and price as special price
            if ($compareAtPrice > $price && $price > 0) {
                $salePrice = $price;
            } elseif ($compareAtPrice > 0 && $compareAtPrice > $price) {
                $salePrice = $price;
            }

            $available = strtolower($row['Available'] ?? 'yes') === 'yes';

            // Parse tags
            $tags = [];
            if (! empty($row['Tags'])) {
                $tags = array_map('trim', explode(';', $row['Tags']));
            }

            // Parse collections (categories)
            $collections = [];
            if (! empty($row['Collections'])) {
                $collections = array_map('trim', explode(';', $row['Collections']));
            }

            // Build description
            $description = $row['Description'] ?? '';

            // Build data for product creation
            $data = [
                'type' => $productType,
                'attribute_family_id' => $attributeFamily->id,
                'sku' => $sku,
            ];

            // Build attribute values array
            $attributeData = [
                'name' => $title,
                'description' => $description,
                'short_description' => Str::limit(strip_tags($description), 300),
                'url_key' => $slug,
                'price' => $compareAtPrice > $price ? $compareAtPrice : $price,
                'special_price' => $salePrice,
                'weight' => 0.5, // Default weight since CSV doesn't have it
                'status' => $available ? 1 : 0,
                'visible_individually' => 1,
                'guest_checkout' => 1,
                'new' => 0,
                'featured' => 0,
                'manage_stock' => 1,
                'meta_title' => $title,
                'meta_description' => Str::limit(strip_tags($description), 160),
                'meta_keywords' => implode(', ', $tags),
                'product_number' => $sku,
            ];

            if ($salePrice) {
                $attributeData['special_price_from'] = now()->toDateString();
                $attributeData['special_price_to'] = now()->addYear()->toDateString();
            }                if ($existingProduct) {
                // Update existing product
                $product = $this->productRepository->find($existingProduct->id);
                if ($product) {
                    $this->productRepository->update($attributeData, $existingProduct->id);

                    // Update attribute values
                    $attributes = $product->attribute_family->custom_attributes;
                    $this->attributeValueRepository->saveValues($attributeData, $product, $attributes);

                    // Re-sync categories so collection changes are picked up
                    if (! empty($categoryIds)) {
                        $product->categories()->sync($categoryIds);
                    }
                }
            } else {
                // Create new product
                $product = $this->productRepository->create($data);

                $attributes = $product->attribute_family->custom_attributes;
                $this->attributeValueRepository->saveValues($attributeData, $product, $attributes);

                // Populate product_flat table (required for admin listing visibility)
                $this->flatIndexer->refresh($product);

                $product->channels()->sync([$defaultChannel->id]);

                // Set inventory with default source
                if ($defaultInventorySource) {
                    $this->productInventoryRepository->saveInventories([
                        'inventories' => [$defaultInventorySource->id => 100], // Default stock
                    ], $product);
                }

                // Map collections to Bagisto category IDs
                $categoryIds = [];
                foreach ($collections as $collectionName) {
                    if ($bagistoCategories->has($collectionName)) {
                        $categoryIds[] = $bagistoCategories[$collectionName]->id;
                    }
                }

                if (! empty($categoryIds)) {
                    $product->categories()->sync($categoryIds);
                }

                // Download and attach product image
                if ($downloadImages && ! empty($row['Image URL'])) {
                    $this->attachProductImage($product, $row['Image URL'], 0);
                }
            }

            $this->counters['products_imported']++;
            $this->components->twoColumnDetail("  ✓ {$title}", $sku);
        } catch (\Exception $e) {
            $this->counters['products_failed']++;
            $this->components->twoColumnDetail("  ✗ Failed: {$title}", $e->getMessage());
            Log::error('WeToyToy import failed', [
                'sku' => $sku,
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Download and attach a product image.
     */
    protected function attachProductImage($product, string $imageUrl, int $position): void
    {
        if (empty($imageUrl)) {
            return;
        }

        try {
            $tempPath = tempnam(sys_get_temp_dir(), 'wt_import_');
            $context = stream_context_create([
                'http' => ['timeout' => 30, 'user_agent' => 'Bagisto-Importer/1.0'],
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
            ]);

            $fileContent = @file_get_contents($imageUrl, false, $context);
            if ($fileContent === false) {
                $this->counters['images_failed']++;
                Log::warning('Failed to download image', ['url' => $imageUrl]);

                return;
            }

            file_put_contents($tempPath, $fileContent);

            $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
            if (! $filename || ! str_contains($filename, '.')) {
                $filename = 'product-'.$product->id.'-'.$position.'.jpg';
            }

            // Store via the product image repository
            $path = 'product/'.$product->id.'/'.$filename;
            Storage::disk('public')->put($path, $fileContent);

            $product->images()->create([
                'type' => 'images',
                'path' => $path,
                'position' => $position,
            ]);

            $this->counters['images_downloaded']++;

            @unlink($tempPath);
        } catch (\Exception $e) {
            $this->counters['images_failed']++;
            Log::warning('Failed to download product image', [
                'product_id' => $product->id,
                'url' => $imageUrl,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Print import summary.
     */
    protected function printSummary(): void
    {
        $this->newLine();
        $this->components->info('Import Summary');
        $this->newLine();

        $this->components->twoColumnDetail('Products Imported', (string) $this->counters['products_imported']);
        $this->components->twoColumnDetail('Products Skipped', (string) $this->counters['products_skipped']);
        $this->components->twoColumnDetail('Products Failed', (string) $this->counters['products_failed']);

        if ($this->option('download-images')) {
            $this->newLine();
            $this->components->twoColumnDetail('Images Downloaded', (string) $this->counters['images_downloaded']);
            $this->components->twoColumnDetail('Images Failed', (string) $this->counters['images_failed']);
        }

        $this->newLine();

        if ($this->counters['products_imported'] > 0) {
            $this->components->info('✅ Import completed successfully!');
        }

        $this->newLine();
        $this->components->bulletList([
            'To re-import (overwrite existing): php artisan import:wetoytoy /path/to/file.csv --force',
            'To download images: php artisan import:wetoytoy /path/to/file.csv --download-images',
            'To limit: php artisan import:wetoytoy /path/to/file.csv --limit=10',
        ]);
    }
}
