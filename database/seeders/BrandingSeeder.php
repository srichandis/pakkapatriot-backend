<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Prettus\Repository\Helpers\CacheKeys;
use Webkul\Core\Models\Channel;
use Webkul\Core\Models\CoreConfig;
use Webkul\Core\Repositories\ChannelRepository;
use Webkul\Core\Repositories\CoreConfigRepository;

class BrandingSeeder extends Seeder
{
    /**
     * Admin panel branding.
     *
     * Bagisto stores branding as files on the public disk plus the paths that
     * reference them, so nothing here can be done by committing artwork alone.
     * Two places read the tab icon: `general.design.admin_logo.favicon` for the
     * admin panel, and the channel's `favicon` for the storefront — this keeps
     * both, and the admin logo, pointing at the artwork in public/images.
     *
     * `general.design.admin_logo.logo_image` is rendered through Storage::url(),
     * so the stored value is a path on the public disk — exactly what a
     * Configuration → Admin Logo upload would leave behind. The admin header and
     * sign-in screen sit on white, hence the dark lockup. Every value can still
     * be replaced from the Configuration UI.
     */
    public function run(): void
    {
        $this->publish(
            source: 'images/logo-dark.png',
            target: 'configuration/pakkapatriot-logo.png',
            code: 'general.design.admin_logo.logo_image',
            label: 'admin logo',
        );

        if ($favicon = $this->publish(
            source: 'favicon.png',
            target: 'configuration/pakkapatriot-favicon.png',
            code: 'general.design.admin_logo.favicon',
            label: 'admin favicon',
        )) {
            $this->brandChannels($favicon);
        }

        /**
         * Both repositories cache lookups for a week (see config/repository.php)
         * and nothing in Prettus invalidates them, so rows written outside the
         * Configuration screen stay invisible until those keys expire. Drop them
         * here.
         */
        foreach ([CoreConfigRepository::class, ChannelRepository::class] as $repository) {
            foreach (CacheKeys::getKeys($repository) as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Copy one piece of artwork onto the public disk and point a configuration
     * key at it. Returns the stored path, or null when the source is missing.
     */
    private function publish(string $source, string $target, string $code, string $label): ?string
    {
        $path = public_path($source);

        if (! is_file($path)) {
            $this->command?->warn("Branding: public/{$source} is missing — {$label} skipped.");

            return null;
        }

        Storage::disk('public')->put($target, file_get_contents($path));

        CoreConfig::updateOrCreate(
            [
                'code' => $code,
                'channel_code' => null,
                'locale_code' => null,
            ],
            ['value' => $target],
        );

        $this->command?->info("Branding: {$label} set to {$target}.");

        return $target;
    }

    /**
     * The shop layout reads its favicon from the channel rather than from the
     * configuration, so the storefront only picks the mark up once the channel
     * carries it too. Each channel, so a non-default hostname is covered as well.
     */
    private function brandChannels(string $favicon): void
    {
        $channels = 0;

        foreach (Channel::all() as $channel) {
            $channel->favicon = $favicon;
            $channel->save();

            $channels++;
        }

        $this->command?->info("Branding: favicon set on {$channels} channel(s).");
    }
}
