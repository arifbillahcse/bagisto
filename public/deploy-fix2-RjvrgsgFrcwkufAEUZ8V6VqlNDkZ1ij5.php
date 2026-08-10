<?php

/**
 * Temporary, token-protected one-time fix (round 2).
 *
 * The flash-sale route (registered by the same ShopExtensionServiceProvider,
 * same boot() call, in the SAME file as the promo-banner admin route) is
 * confirmed working, but 'admin.settings.themes.promo_banner.update' is
 * reported as not defined. Since both route groups are registered by the
 * same provider file in the same method, the most likely explanation left
 * is PHP's OPcache holding a stale compiled copy of
 * ShopExtensionServiceProvider.php from before the admin routes were added
 * to it -- independent of Laravel's own artisan caches.
 *
 * This resets OPcache directly (if available) and re-checks the route.
 *
 * DELETE THIS FILE (via cPanel File Manager) once you're done running it.
 */

header('Content-Type: text/plain; charset=utf-8');

$basePath = dirname(__DIR__);

function section(string $title): void
{
    echo "\n=== {$title} ===\n";
}

section('1. Resetting OPcache');

if (function_exists('opcache_reset')) {
    $status = function_exists('opcache_get_status') ? opcache_get_status(false) : null;

    if ($status) {
        echo 'OPcache was enabled, memory used: '.round(($status['memory_usage']['used_memory'] ?? 0) / 1024 / 1024, 1)."MB\n";
    }

    $reset = opcache_reset();
    echo $reset ? "opcache_reset() succeeded.\n" : "opcache_reset() returned false (may be restricted).\n";

    // Explicitly invalidate the two files we care about most, in case the
    // wildcard reset above is restricted but per-file invalidation isn't.
    if (function_exists('opcache_invalidate')) {
        $filesToInvalidate = [
            $basePath.'/packages/Webkul/ShopExtension/src/Providers/ShopExtensionServiceProvider.php',
            $basePath.'/packages/Webkul/ShopExtension/src/Routes/admin.php',
            $basePath.'/packages/Webkul/ShopExtension/src/Http/Controllers/Admin/PromoBannerController.php',
            $basePath.'/bootstrap/providers.php',
        ];

        foreach ($filesToInvalidate as $file) {
            if (file_exists($file)) {
                $ok = opcache_invalidate($file, true);
                echo 'opcache_invalidate('.basename($file).'): '.($ok ? "OK\n" : "not cached / no-op\n");
            } else {
                echo basename($file).": FILE MISSING ON DISK -- git pull may not have brought this in.\n";
            }
        }
    }
} else {
    echo "opcache_reset() not available -- OPcache extension may be disabled, so it's not the cause.\n";
}

section('2. Re-checking the admin route directly (fresh boot in this request)');

try {
    require $basePath.'/vendor/autoload.php';

    $app = require $basePath.'/bootstrap/app.php';

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $router = $app->make('router');

    $hasFlashSale = $router->getRoutes()->hasNamedRoute('shop.api.products.flash_sale.index');
    $hasPromoBanner = $router->getRoutes()->hasNamedRoute('admin.settings.themes.promo_banner.update');

    echo 'shop.api.products.flash_sale.index registered: '.($hasFlashSale ? "YES\n" : "NO\n");
    echo 'admin.settings.themes.promo_banner.update registered: '.($hasPromoBanner ? "YES\n" : "NO\n");

    if (! $hasPromoBanner) {
        echo "\nStill missing after OPcache reset -- printing the actual file contents\n";
        echo "this request read from disk, to rule out a stale/partial file:\n";

        $adminRoutesFile = $basePath.'/packages/Webkul/ShopExtension/src/Routes/admin.php';
        echo "\n--- {$adminRoutesFile} ---\n";
        echo file_exists($adminRoutesFile) ? file_get_contents($adminRoutesFile) : "FILE DOES NOT EXIST\n";

        $providerFile = $basePath.'/packages/Webkul/ShopExtension/src/Providers/ShopExtensionServiceProvider.php';
        echo "\n--- {$providerFile} ---\n";
        echo file_exists($providerFile) ? file_get_contents($providerFile) : "FILE DOES NOT EXIST\n";
    }

    section('3. Clearing Laravel caches again');
    $exitCode = $kernel->call('optimize:clear');
    echo "optimize:clear exit code: {$exitCode}\n";
    echo $kernel->output();
} catch (\Throwable $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
}

section('Done -- now reload the admin edit page for theme id 20 and check');

echo "\nDelete this file (public/deploy-fix2-RjvrgsgFrcwkufAEUZ8V6VqlNDkZ1ij5.php) via cPanel File Manager when done.\n";
