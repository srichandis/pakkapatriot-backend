<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Product\Repositories\ProductImageRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Product\Repositories\ProductRepository;

class ImportFromWordPress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:wordpress
        {--wp-url= : WordPress/WooCommerce base URL}
        {--wp-username= : WordPress username for Application Password auth}
        {--wp-password= : WordPress Application Password}
        {--wc-key= : WooCommerce Consumer Key}
        {--wc-secret= : WooCommerce Consumer Secret}
        {--only= : Import only "blogs" or "products"}
        {--force : Re-import even if previously imported (by slug/SKU)}
        {--limit= : Max items to import per type}
        {--batch=50 : Number of items to fetch per API page}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import blogs from WordPress and products from WooCommerce into Bagisto';

    /**
     * WordPress API base URL.
     */
    protected string $wpApiBase;

    /**
     * WooCommerce API base URL.
     */
    protected string $wcApiBase;

    /**
     * WordPress auth credentials for Basic Auth.
     */
    protected array $wpAuth = [];

    /**
     * WooCommerce auth credentials.
     */
    protected array $wcAuth = [];

    /**
     * Counters for import summary.
     */
    protected array $counters = [
        'blogs_imported' => 0,
        'blogs_skipped' => 0,
        'blogs_failed' => 0,
        'products_imported' => 0,
        'products_skipped' => 0,
        'products_failed' => 0,
    ];

    /**
     * Create a new command instance.
     */
    public function __construct(
        protected ProductRepository $productRepository,
        protected ProductAttributeValueRepository $attributeValueRepository,
        protected ProductImageRepository $productImageRepository,
        protected ProductInventoryRepository $productInventoryRepository,
        protected AttributeRepository $attributeRepository
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $wpUrl = $this->option('wp-url') ?: env('WP_URL', $this->ask('WordPress site URL', 'https://pakkapatriot.com'));
        $wpUsername = $this->option('wp-username') ?: env('WP_USERNAME', $this->ask('WordPress username', 'pakkapatriot'));
        $wpPassword = $this->option('wp-password') ?: env('WP_PASSWORD', $this->secret('WordPress Application Password'));

        $this->wpApiBase = rtrim($wpUrl, '/').'/wp-json/wp/v2';
        $this->wcApiBase = rtrim($wpUrl, '/').'/wp-json/wc/v3';
        $this->wpAuth = [$wpUsername, $wpPassword];

        $wcKey = $this->option('wc-key') ?: env('WC_KEY');
        $wcSecret = $this->option('wc-secret') ?: env('WC_SECRET');

        if ($wcKey && $wcSecret) {
            $this->wcAuth = [$wcKey, $wcSecret];
        } else {
            $this->wcAuth = $this->wpAuth;
        }

        $only = $this->option('only');

        $this->newLine();
        $this->components->twoColumnDetail('<fg=green>Source</>', $wpUrl);
        $this->components->twoColumnDetail('<fg=green>Blogs</>', $only && $only !== 'blogs' ? 'Skipped' : 'Enabled');
        $this->components->twoColumnDetail('<fg=green>Products</>', $only && $only !== 'products' ? 'Skipped' : 'Enabled');
        $this->newLine();

        if (! $only || $only === 'blogs') {
            $this->importBlogs();
        }

        if (! $only || $only === 'products') {
            $this->importProducts();
        }

        $this->printSummary();

        return Command::SUCCESS;
    }

    /**
     * Import blog posts from WordPress.
     */
    protected function importBlogs(): void
    {
        $this->components->info('Importing blog posts from WordPress...');

        $page = 1;
        $perPage = (int) ($this->option('batch') ?: 50);
        $limit = (int) ($this->option('limit') ?: 0);
        $imported = 0;

        $categories = $this->fetchCategories();

        do {
            $this->components->task("Fetching page {$page}");

            $response = $this->wpGet('/posts', [
                'per_page' => $perPage,
                'page' => $page,
                'status' => 'publish',
                '_embed' => 'wp:featuredmedia,author',
                'orderby' => 'date',
                'order' => 'desc',
            ]);

            if ($response->failed()) {
                $this->components->error("Failed to fetch posts (HTTP {$response->status()})");
                break;
            }

            $posts = $response->json();

            if (empty($posts)) {
                break;
            }

            foreach ($posts as $post) {
                if ($limit && $imported >= $limit) {
                    break 2;
                }

                $this->importSingleBlog($post, $categories);
                $imported++;
            }

            $page++;
        } while (count($posts) === $perPage);

        $this->newLine();
    }

    /**
     * Import a single blog post.
     */
    protected function importSingleBlog(array $post, array $categories): void
    {
        $slug = $post['slug'] ?? Str::slug($post['title']['rendered']);

        $existing = Blog::where('slug', $slug)->first();
        if ($existing && ! $this->option('force')) {
            $this->counters['blogs_skipped']++;

            return;
        }

        try {
            $title = html_entity_decode(strip_tags($post['title']['rendered'] ?? ''));
            $content = $post['content']['rendered'] ?? '';
            $excerptRaw = strip_tags($post['excerpt']['rendered'] ?? '');
            $excerpt = html_entity_decode($excerptRaw);
            $date = $post['date'] ?? null;

            $metaData = [
                'wordpress_id' => $post['id'],
                'categories' => [],
                'tags' => [],
            ];

            if (! empty($post['categories'])) {
                foreach ($post['categories'] as $catId) {
                    if (isset($categories[$catId])) {
                        $metaData['categories'][] = $categories[$catId]['name'];
                    }
                }
            }

            if (! empty($post['tags'])) {
                $tags = $this->fetchTags($post['tags']);
                $metaData['tags'] = $tags;
            }

            // Get author name from _embedded data
            $authorName = 'Admin';
            $embedded = $post['_embedded'] ?? [];
            if (isset($embedded['author'][0]['name'])) {
                $authorName = $embedded['author'][0]['name'];
            }

            $blogData = [
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'excerpt' => $excerpt ?: Str::limit(strip_tags($content), 200),
                'is_published' => true,
                'published_at' => $date ?: now(),
                'author_name' => $authorName,
                'meta_data' => $metaData,
                'admin_id' => 1,
            ];

            if ($existing) {
                $existing->update($blogData);
                $blog = $existing;
            } else {
                $blog = Blog::create($blogData);
            }

            // Download and attach featured image from _embedded data
            $imageUrl = null;
            if (isset($embedded['wp:featuredmedia'][0]['source_url'])) {
                $imageUrl = $embedded['wp:featuredmedia'][0]['source_url'];
            } elseif (! empty($post['featured_media'])) {
                $mediaResponse = $this->wpGet('/media/'.$post['featured_media']);
                if ($mediaResponse->successful()) {
                    $media = $mediaResponse->json();
                    $imageUrl = $media['source_url'] ?? $media['guid']['rendered'] ?? null;
                }
            }

            if ($imageUrl) {
                $this->attachImageToBlog($blog, $imageUrl);
            }

            $this->counters['blogs_imported']++;
            $this->components->twoColumnDetail("  ✓ {$title}", $slug);
        } catch (\Exception $e) {
            $this->counters['blogs_failed']++;
            $this->components->twoColumnDetail("  ✗ Failed: {$post['title']['rendered']}", $e->getMessage());
            Log::error('Blog import failed', [
                'post_id' => $post['id'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Download and attach an image to a blog post using Spatie MediaLibrary.
     */
    protected function attachImageToBlog(Blog $blog, string $imageUrl): void
    {
        try {
            $tempPath = tempnam(sys_get_temp_dir(), 'wp_import_');
            $context = stream_context_create([
                'http' => ['timeout' => 15, 'user_agent' => 'Bagisto-Importer/1.0'],
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
            ]);

            $fileContent = @file_get_contents($imageUrl, false, $context);
            if ($fileContent === false) {
                return;
            }

            file_put_contents($tempPath, $fileContent);

            $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
            if (! $filename || ! str_contains($filename, '.')) {
                $filename = 'featured-'.$blog->id.'.jpg';
            }

            $blog->addMedia($tempPath)
                ->usingFileName($filename)
                ->toMediaCollection('featured_image');

            @unlink($tempPath);
        } catch (\Exception $e) {
            Log::warning('Failed to download blog image', [
                'blog_id' => $blog->id,
                'url' => $imageUrl,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Import products from WooCommerce.
     */
    protected function importProducts(): void
    {
        $this->components->info('Importing products from WooCommerce...');

        $attributeFamily = DB::table('attribute_families')->where('code', 'default')->first();
        if (! $attributeFamily) {
            $this->components->warn('Default attribute family not found. Please run migrations and seeders first.');

            return;
        }

        $page = 1;
        $perPage = (int) ($this->option('batch') ?: 50);
        $limit = (int) ($this->option('limit') ?: 0);
        $imported = 0;

        $wcCategories = $this->fetchWooCommerceCategories();
        $bagistoCategories = DB::table('categories')->get()->keyBy('name');

        $defaultChannel = DB::table('channels')->where('code', 'default')->first()
            ?: DB::table('channels')->first();

        if (! $defaultChannel) {
            $this->components->error('No channels found. Please set up channels first.');

            return;
        }

        do {
            $this->components->task("Fetching page {$page}");

            $response = $this->wcGet('/products', [
                'per_page' => $perPage,
                'page' => $page,
                'status' => 'publish',
                'orderby' => 'date',
                'order' => 'desc',
            ]);

            if ($response->failed()) {
                if ($response->status() === 401) {
                    $this->components->error('WooCommerce API returned 401 Unauthorized.');
                    $this->line('');
                    $this->components->bulletList([
                        'The Application Password may not have WooCommerce permissions.',
                        'To fix: Generate API keys from WooCommerce → Settings → Advanced → REST API.',
                        'Then re-run: php artisan import:wordpress --wc-key="ck_xxx" --wc-secret="cs_xxx" --force',
                    ]);
                } else {
                    $this->components->error("Failed to fetch products (HTTP {$response->status()})");
                }
                break;
            }

            $products = $response->json();

            if (empty($products)) {
                break;
            }

            foreach ($products as $product) {
                if ($limit && $imported >= $limit) {
                    break 2;
                }

                $this->importSingleProduct($product, $attributeFamily, $wcCategories, $bagistoCategories, $defaultChannel);
                $imported++;
            }

            $page++;
        } while (count($products) === $perPage);

        $this->newLine();
    }

    /**
     * Import a single WooCommerce product into Bagisto.
     */
    protected function importSingleProduct(
        array $productData,
        \stdClass $attributeFamily,
        array $wcCategories,
        Collection $bagistoCategories,
        \stdClass $defaultChannel
    ): void {
        $sku = $productData['sku'] ?: 'wc-'.$productData['id'];
        $slug = $productData['slug'] ?: Str::slug($productData['name']);

        $existingProduct = DB::table('products')->where('sku', $sku)->first();
        if ($existingProduct && ! $this->option('force')) {
            $this->counters['products_skipped']++;

            return;
        }

        try {
            $wcType = $productData['type'] ?? 'simple';
            $bagistoType = match ($wcType) {
                'simple' => 'simple',
                'variable' => 'configurable',
                'grouped' => 'grouped',
                'external' => 'simple',
                'bundle' => 'bundle',
                default => 'simple',
            };

            $name = html_entity_decode($productData['name'] ?? '');
            $description = $productData['description'] ?? '';
            $shortDescription = $productData['short_description'] ?? '';

            $regularPrice = (float) ($productData['regular_price'] ?? $productData['price'] ?? 0);
            $salePrice = $productData['sale_price'] ? (float) $productData['sale_price'] : null;

            // Map WooCommerce categories to Bagisto category IDs
            $categoryIds = [];
            if (! empty($productData['categories'])) {
                foreach ($productData['categories'] as $wcCat) {
                    $catName = $wcCat['name'] ?? null;
                    if ($catName && $bagistoCategories->has($catName)) {
                        $categoryIds[] = $bagistoCategories[$catName]->id;
                    }
                }
            }

            // Build data for product creation
            $data = [
                'type' => $bagistoType,
                'attribute_family_id' => $attributeFamily->id,
                'sku' => $sku,
            ];

            // Build attribute values array
            $attributeData = [
                'name' => $name,
                'description' => $description,
                'short_description' => $shortDescription,
                'url_key' => $slug,
                'price' => $regularPrice,
                'special_price' => $salePrice,
                'weight' => (float) ($productData['weight'] ?? 0),
                'status' => 1,
                'visible_individually' => 1,
                'guest_checkout' => 1,
                'new' => 0,
                'featured' => 0,
                'manage_stock' => $productData['manage_stock'] ?? true,
                'meta_title' => $name,
                'meta_description' => $shortDescription ?: Str::limit(strip_tags($description), 160),
                'meta_keywords' => implode(',', array_column($productData['tags'] ?? [], 'name')),
                'product_number' => $sku,
            ];

            if ($salePrice) {
                $attributeData['special_price_from'] = now()->toDateString();
                $attributeData['special_price_to'] = now()->addYear()->toDateString();
            }

            if ($existingProduct) {
                $product = $this->productRepository->find($existingProduct->id);
                if ($product) {
                    $this->productRepository->update($attributeData, $existingProduct->id);
                }
            } else {
                $product = $this->productRepository->create($data);

                $attributes = $product->attribute_family->custom_attributes;
                $this->attributeValueRepository->saveValues($attributeData, $product, $attributes);

                $product->channels()->sync([$defaultChannel->id]);

                // Set inventory with default source
                $defaultInventorySource = DB::table('inventory_sources')
                    ->where('status', 1)
                    ->first();

                if ($defaultInventorySource) {
                    $stockQty = $productData['stock_quantity'] ?? 100;
                    $this->productInventoryRepository->saveInventories([
                        'inventories' => [$defaultInventorySource->id => max(0, $stockQty)],
                    ], $product);
                }

                // Sync categories
                if (! empty($categoryIds)) {
                    $product->categories()->sync($categoryIds);
                }

                // Download and attach product images using repository
                if (! empty($productData['images'])) {
                    foreach ($productData['images'] as $index => $image) {
                        $imageUrl = $image['src'] ?? null;
                        if ($imageUrl) {
                            $this->attachProductImage($product, $imageUrl, $index);
                        }
                    }
                }
            }

            $this->counters['products_imported']++;
            $this->components->twoColumnDetail("  ✓ {$name}", $sku);
        } catch (\Exception $e) {
            $this->counters['products_failed']++;
            $this->components->twoColumnDetail("  ✗ Failed: {$productData['name']}", $e->getMessage());
            Log::error('Product import failed', [
                'product_id' => $productData['id'] ?? null,
                'sku' => $sku,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Download and attach a product image using the proper repository.
     */
    protected function attachProductImage($product, string $imageUrl, int $position): void
    {
        try {
            $tempPath = tempnam(sys_get_temp_dir(), 'wc_import_');
            $context = stream_context_create([
                'http' => ['timeout' => 15, 'user_agent' => 'Bagisto-Importer/1.0'],
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
            ]);

            $fileContent = @file_get_contents($imageUrl, false, $context);
            if ($fileContent === false) {
                return;
            }

            file_put_contents($tempPath, $fileContent);

            $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
            if (! $filename || ! str_contains($filename, '.')) {
                $filename = 'product-'.$product->id.'-'.$position.'.jpg';
            }

            // Use the proper disk path and create the image via the model relationship
            $path = 'product/'.$product->id.'/'.$filename;
            Storage::disk('public')->put($path, $fileContent);

            $product->images()->create([
                'type' => 'images',
                'path' => $path,
                'position' => $position,
            ]);

            @unlink($tempPath);
        } catch (\Exception $e) {
            Log::warning('Failed to download product image', [
                'product_id' => $product->id,
                'url' => $imageUrl,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Fetch WordPress categories.
     */
    protected function fetchCategories(): array
    {
        $categories = [];
        $page = 1;

        do {
            $response = $this->wpGet('/categories', [
                'per_page' => 100,
                'page' => $page,
                'hide_empty' => false,
            ]);

            if ($response->failed()) {
                break;
            }

            $data = $response->json();
            foreach ($data as $cat) {
                $categories[$cat['id']] = $cat;
            }

            $page++;
        } while (count($data) === 100);

        return $categories;
    }

    /**
     * Fetch WordPress tags by IDs.
     */
    protected function fetchTags(array $tagIds): array
    {
        if (empty($tagIds)) {
            return [];
        }

        $tags = [];
        $ids = implode(',', array_slice($tagIds, 0, 50));

        $response = $this->wpGet('/tags', ['include' => $ids]);
        if ($response->successful()) {
            foreach ($response->json() as $tag) {
                $tags[] = $tag['name'];
            }
        }

        return $tags;
    }

    /**
     * Fetch WooCommerce product categories.
     */
    protected function fetchWooCommerceCategories(): array
    {
        $categories = [];
        $page = 1;

        do {
            $response = $this->wcGet('/products/categories', [
                'per_page' => 100,
                'page' => $page,
                'hide_empty' => false,
            ]);

            if ($response->failed()) {
                break;
            }

            $data = $response->json();
            foreach ($data as $cat) {
                $categories[$cat['id']] = $cat;
            }

            $page++;
        } while (count($data) === 100);

        return $categories;
    }

    /**
     * Make a WordPress API GET request.
     */
    protected function wpGet(string $endpoint, array $query = []): Response
    {
        return Http::withBasicAuth($this->wpAuth[0], $this->wpAuth[1])
            ->withOptions(['verify' => false, 'timeout' => 30])
            ->get($this->wpApiBase.$endpoint, $query);
    }

    /**
     * Make a WooCommerce API GET request.
     */
    protected function wcGet(string $endpoint, array $query = []): Response
    {
        if (! empty($this->wcAuth) && count($this->wcAuth) === 2) {
            return Http::withBasicAuth($this->wcAuth[0], $this->wcAuth[1])
                ->withOptions(['verify' => false, 'timeout' => 30])
                ->get($this->wcApiBase.$endpoint, $query);
        }

        return Http::withOptions(['verify' => false, 'timeout' => 30])
            ->get($this->wcApiBase.$endpoint, $query);
    }

    /**
     * Print import summary.
     */
    protected function printSummary(): void
    {
        $this->newLine();
        $this->components->info('Import Summary');
        $this->newLine();

        $this->components->twoColumnDetail('Blogs Imported', (string) $this->counters['blogs_imported']);
        $this->components->twoColumnDetail('Blogs Skipped', (string) $this->counters['blogs_skipped']);
        $this->components->twoColumnDetail('Blogs Failed', (string) $this->counters['blogs_failed']);
        $this->newLine();
        $this->components->twoColumnDetail('Products Imported', (string) $this->counters['products_imported']);
        $this->components->twoColumnDetail('Products Skipped', (string) $this->counters['products_skipped']);
        $this->components->twoColumnDetail('Products Failed', (string) $this->counters['products_failed']);

        $this->newLine();

        if ($this->counters['products_failed'] > 0) {
            $this->components->bulletList([
                'For WooCommerce products, you may need Consumer Key/Consumer Secret API credentials.',
                'Generate them at: WordPress Admin → WooCommerce → Settings → Advanced → REST API',
                'Then re-run: php artisan import:wordpress --wc-key="ck_your_key" --wc-secret="cs_your_secret" --force',
            ]);
        }
    }
}
