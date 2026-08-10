<?php

/**
 * Temporary, token-protected deploy diagnostic + fixer.
 *
 * Visit this file directly in a browser (URL already includes the secret
 * token in the filename, no query string needed) to check whether the
 * latest git commit, the ShopExtension autoload mapping, and the
 * flash-sale route are actually live on this server -- and to clear
 * Laravel's caches without needing shell/SSH access.
 *
 * DELETE THIS FILE (via cPanel File Manager) once you're done. It has no
 * further protection beyond the unguessable filename.
 */

header('Content-Type: text/plain; charset=utf-8');

$basePath = dirname(__DIR__);

function section(string $title): void
{
    echo "\n=== {$title} ===\n";
}

section('1. Git HEAD on this server');

$gitHead = @file_get_contents($basePath.'/.git/HEAD');

if ($gitHead === false) {
    echo "Could not read .git/HEAD -- is this a git checkout?\n";
} else {
    $gitHead = trim($gitHead);
    echo "HEAD points to: {$gitHead}\n";

    if (str_starts_with($gitHead, 'ref: ')) {
        $ref = trim(substr($gitHead, 5));
        $refPath = $basePath.'/.git/'.$ref;

        if (file_exists($refPath)) {
            echo "Current branch: {$ref}\n";
            echo 'Current commit: '.trim(file_get_contents($refPath))."\n";
        } else {
            $packedRefs = @file_get_contents($basePath.'/.git/packed-refs');
            if ($packedRefs && preg_match('/^(\w+) '.preg_quote($ref, '/').'$/m', $packedRefs, $m)) {
                echo "Current branch: {$ref}\n";
                echo "Current commit: {$m[1]}\n";
            }
        }
    }
}

echo "\nExpected latest commit (from the repo we pushed) should be the newest of:\n";
echo "  1f6c6a6 feat: move brand strip section to the bottom of the homepage\n";
echo "  e084cb7 feat: add dynamic promo banner theme customization type\n";
echo "  1450a3d Move flash sale API into a separate ShopExtension package\n";
echo "If the commit above does NOT match one of these, the git pull on this server has not run yet.\n";

section('2. ShopExtension package present on disk?');

$providerFile = $basePath.'/packages/Webkul/ShopExtension/src/Providers/ShopExtensionServiceProvider.php';
echo $providerFile.': '.(file_exists($providerFile) ? "FOUND\n" : "MISSING -- git pull has not brought in this package yet.\n");

section('3. Composer autoload map includes ShopExtension?');

$autoloadPsr4 = $basePath.'/vendor/composer/autoload_psr4.php';

if (! file_exists($autoloadPsr4)) {
    echo "vendor/composer/autoload_psr4.php not found.\n";
} else {
    $contents = file_get_contents($autoloadPsr4);
    $registered = str_contains($contents, 'Webkul\\\\ShopExtension\\\\');
    echo $registered
        ? "Registered in autoload_psr4.php.\n"
        : "NOT registered -- composer dump-autoload has not run successfully since the package was added.\n";
}

section('4. Attempting to boot Laravel and check the route + class directly');

$kernel = null;

try {
    require $basePath.'/vendor/autoload.php';

    $app = require $basePath.'/bootstrap/app.php';

    $classExists = class_exists(\Webkul\ShopExtension\Providers\ShopExtensionServiceProvider::class);
    echo 'Webkul\\ShopExtension\\Providers\\ShopExtensionServiceProvider class_exists(): '.($classExists ? "YES\n" : "NO\n");

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $router = $app->make('router');
    $hasRoute = $router->getRoutes()->hasNamedRoute('shop.api.products.flash_sale.index');
    echo "Route 'shop.api.products.flash_sale.index' registered: ".($hasRoute ? "YES\n" : "NO\n");

    $providers = $app->getLoadedProviders();
    $providerLoaded = isset($providers[\Webkul\ShopExtension\Providers\ShopExtensionServiceProvider::class]);
    echo 'ShopExtensionServiceProvider loaded by the app: '.($providerLoaded ? "YES\n" : "NO\n");
} catch (\Throwable $e) {
    echo 'ERROR while booting Laravel: '.$e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
}

section('5. Clearing Laravel caches (no shell needed)');

if (! $kernel) {
    echo "Skipped -- Laravel failed to boot in section 4, see the error above.\n";
} else {
    try {
        $exitCode = $kernel->call('optimize:clear');
        echo "optimize:clear exit code: {$exitCode}\n";
        echo $kernel->output();
    } catch (\Throwable $e) {
        echo 'ERROR running optimize:clear: '.$e->getMessage()."\n";
    }
}

section('6. Attempting composer dump-autoload via shell (if available)');

if (! function_exists('shell_exec')) {
    echo "shell_exec() is disabled on this server -- cannot run composer this way.\n";
    echo "See section 3 above: if the mapping is missing, ask your host for the Composer CLI path\n";
    echo "and run it via cPanel's Cron Jobs (one-off), or via cPanel Terminal if available.\n";
} else {
    $composerPaths = ['composer', '/usr/local/bin/composer', '/opt/cpanel/composer/bin/composer'];
    $ran = false;

    foreach ($composerPaths as $composerBin) {
        $cmd = 'cd '.escapeshellarg($basePath).' && '.escapeshellcmd($composerBin).' dump-autoload 2>&1';
        $output = @shell_exec($cmd);

        if ($output !== null) {
            echo "Ran via '{$composerBin}':\n{$output}\n";
            $ran = true;
            break;
        }
    }

    if (! $ran) {
        echo "Could not find a working composer binary automatically.\n";
    }
}

section('Done');

echo "\nDelete this file now via cPanel File Manager (public/deploy-check-KB5Djfj2W17qFzAQvTiKpFr45Vzu0UvI.php).\n";
