<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportMerchMockups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:merch-mockups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach the generated product mockups (mug, hoodie, tote, poster, sticker, notebook, cap) to the merch products, replacing the t-shirt artwork images';

    /**
     * Category SKU segment => mockup folder.
     */
    protected array $categories = [
        'hoodie' => 'hoodie',
        'mug' => 'mug',
        'tote-bag' => 'tote-bag',
        'poster' => 'poster',
        'sticker-pack' => 'sticker-pack',
        'notebook' => 'notebook',
        'cap' => 'cap',
    ];

    /**
     * Design slugs (matching the mockup filenames).
     */
    protected array $designs = [
        'tajmahal',
        'hampi',
        'indiagate',
        'khajuraho',
        'konark',
        'netaji',
        'chanakya',
        'savarkar',
        'shivaji',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updated = 0;
        $missing = 0;

        foreach ($this->categories as $category => $folder) {
            foreach ($this->designs as $design) {
                $sku = 'pp-'.$category.'-'.$design;

                $product = DB::table('products')->where('sku', $sku)->first();

                if (! $product) {
                    $this->components->twoColumnDetail("  – No product: {$sku}", '(skipped)');
                    $missing++;

                    continue;
                }

                $source = 'mockups/'.$folder.'/'.$design.'.png';
                $target = 'product/'.$product->id.'/mockup.png';

                if (! Storage::disk('public')->exists($source)) {
                    $this->components->twoColumnDetail("  – Mockup missing: {$source}", '(skipped)');
                    $missing++;

                    continue;
                }

                Storage::disk('public')->copy($source, $target);

                // Replace the colourway images with the single mockup photo.
                DB::table('product_images')->where('product_id', $product->id)->delete();

                DB::table('product_images')->insert([
                    'product_id' => $product->id,
                    'type' => 'images',
                    'path' => $target,
                    'position' => 0,
                ]);

                $this->components->twoColumnDetail("  ✓ {$sku}", $target);
                $updated++;
            }
        }

        $this->newLine();
        $this->components->info('Mockup Import Summary');
        $this->newLine();
        $this->components->twoColumnDetail('Products Updated', (string) $updated);
        $this->components->twoColumnDetail('Skipped / Missing', (string) $missing);

        return Command::SUCCESS;
    }
}
