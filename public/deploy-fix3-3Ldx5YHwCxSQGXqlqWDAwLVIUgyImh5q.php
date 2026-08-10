<?php

/**
 * Temporary, token-protected one-time fix (round 3).
 *
 * Round 2 showed that even inside a fresh boot (no response-cache layer
 * involved), NEITHER the flash-sale NOR the promo-banner routes register,
 * despite the provider/routes files being correct on disk. This digs one
 * level deeper: is the class actually autoloadable, is the provider
 * actually listed in bootstrap/providers.php, and if we register the
 * provider by hand, does it boot without error?
 *
 * It also clears Bagisto's response-cache (a separate cache layer from
 * artisan's optimize:clear), which is the likely reason the homepage
 * looked like it was working with stale content earlier.
 *
 * DELETE THIS FILE (via cPanel File Manager) once you're done running it.
 */

header('Content-Type: text/plain; charset=utf-8');

$basePath = dirname(__DIR__);

function section(string $title): void
{
    echo "\n=== {$title} ===\n";
}

section('1. Current autoload_psr4.php entry for ShopExtension');

$autoloadPsr4Path = $basePath.'/vendor/composer/autoload_psr4.php';
$psr4Contents = @file_get_contents($autoloadPsr4Path);

if ($psr4Contents === false) {
    echo "Could not read {$autoloadPsr4Path}\n";
} elseif (str_contains($psr4Contents, "'Webkul\\\\ShopExtension\\\\'")) {
    // Print just the matching line for confirmation.
    foreach (explode("\n", $psr4Contents) as $line) {
        if (str_contains($line, 'ShopExtension')) {
            echo trim($line)."\n";
        }
    }
} else {
    echo "MISSING -- our earlier patch is gone. Something (a real composer run,\n";
    echo "a deploy script, or a fresh vendor/ install) has regenerated this file\n";
    echo "since we patched it, without including ShopExtension.\n";
}

section('2. autoload_classmap.php / autoload_static.php (optimized autoloader check)');

foreach (['autoload_classmap.php', 'autoload_static.php'] as $f) {
    $path = $basePath.'/vendor/composer/'.$f;
    if (! file_exists($path)) {
        echo "{$f}: not present\n";
        continue;
    }
    $c = file_get_contents($path);
    echo "{$f}: ".(str_contains($c, 'ShopExtension') ? "mentions ShopExtension\n" : "does NOT mention ShopExtension\n");
}

section('3. Booting Laravel and checking class + provider list');

$app = null;

try {
    require $basePath.'/vendor/autoload.php';

    echo 'class_exists(ShopExtensionServiceProvider) right after vendor/autoload.php: ';
    echo class_exists(\Webkul\ShopExtension\Providers\ShopExtensionServiceProvider::class) ? "YES\n" : "NO\n";

    $app = require $basePath.'/bootstrap/app.php';

    $providersConfigPath = $basePath.'/bootstrap/providers.php';
    $providersList = require $providersConfigPath;
    $inList = in_array(\Webkul\ShopExtension\Providers\ShopExtensionServiceProvider::class, $providersList, true);
    echo 'Listed in bootstrap/providers.php: '.($inList ? "YES\n" : "NO\n");

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $loadedProviders = $app->getLoadedProviders();
    $wasBooted = isset($loadedProviders[\Webkul\ShopExtension\Providers\ShopExtensionServiceProvider::class]);
    echo 'Actually booted by the framework: '.($wasBooted ? "YES\n" : "NO\n");

    if (! $wasBooted) {
        section('4. Forcing manual registration to isolate the failure');
        try {
            $app->register(\Webkul\ShopExtension\Providers\ShopExtensionServiceProvider::class, true);
            echo "Manual register() succeeded with no exception.\n";

            $router = $app->make('router');
            $hasFlashSale = $router->getRoutes()->hasNamedRoute('shop.api.products.flash_sale.index');
            echo 'Route registered after manual register(): '.($hasFlashSale ? "YES\n" : "NO\n");
        } catch (\Throwable $e) {
            echo 'Manual register() THREW: '.get_class($e).': '.$e->getMessage()."\n";
            echo "This is the real root cause -- see the message above.\n";
            echo $e->getTraceAsString()."\n";
        }
    }
} catch (\Throwable $e) {
    echo 'ERROR during boot: '.get_class($e).': '.$e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
}

section('5. Clearing Bagisto response cache (separate from artisan optimize:clear)');

if ($app) {
    try {
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $exitCode = $kernel->call('responsecache:clear');
        echo "responsecache:clear exit code: {$exitCode}\n";
        echo $kernel->output();
    } catch (\Throwable $e) {
        echo 'responsecache:clear not available or failed: '.$e->getMessage()."\n";
    }

    try {
        $exitCode = $kernel->call('optimize:clear');
        echo "optimize:clear exit code: {$exitCode}\n";
    } catch (\Throwable $e) {
        echo 'optimize:clear failed: '.$e->getMessage()."\n";
    }
}

section('Done');

echo "\nSend back everything printed above -- especially sections 1, 2, and 4.\n";
echo "Delete this file (public/deploy-fix3-3Ldx5YHwCxSQGXqlqWDAwLVIUgyImh5q.php) via cPanel File Manager when done.\n";
