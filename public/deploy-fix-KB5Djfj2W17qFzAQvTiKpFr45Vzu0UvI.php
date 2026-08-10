<?php

/**
 * Temporary, token-protected one-time fix.
 *
 * The host's PHP-FPM/web process has no `composer` binary on its PATH, so
 * `composer dump-autoload` has never actually run since packages/Webkul/
 * ShopExtension was added -- vendor/composer/autoload_psr4.php never got
 * the new namespace entry, so the class (and its routes) can't be found.
 *
 * This patches that one entry directly into autoload_psr4.php, the exact
 * line `composer dump-autoload` would have added, with no shell/Composer
 * involved. Then it clears Laravel's caches via the Artisan kernel.
 *
 * DELETE THIS FILE (via cPanel File Manager) once you're done running it.
 */

header('Content-Type: text/plain; charset=utf-8');

$basePath = dirname(__DIR__);

function section(string $title): void
{
    echo "\n=== {$title} ===\n";
}

section('1. Patching vendor/composer/autoload_psr4.php');

$autoloadPsr4Path = $basePath.'/vendor/composer/autoload_psr4.php';

if (! file_exists($autoloadPsr4Path)) {
    echo "ERROR: {$autoloadPsr4Path} not found. Cannot continue.\n";
    exit;
}

$contents = file_get_contents($autoloadPsr4Path);

if (str_contains($contents, "'Webkul\\\\ShopExtension\\\\'")) {
    echo "Already patched -- entry exists.\n";
} else {
    $needle = "return array(\n";
    $pos = strpos($contents, $needle);

    if ($pos === false) {
        echo "ERROR: could not find the expected 'return array(' marker in this file.\n";
        echo "The file format may differ from what this script expects -- manual edit needed.\n";
        exit;
    }

    $insertAt = $pos + strlen($needle);
    $line = "    'Webkul\\\\ShopExtension\\\\' => array(\$baseDir . '/packages/Webkul/ShopExtension/src'),\n";

    $patched = substr($contents, 0, $insertAt).$line.substr($contents, $insertAt);

    $backupPath = $autoloadPsr4Path.'.bak-'.date('YmdHis');
    copy($autoloadPsr4Path, $backupPath);
    echo "Backed up original to: ".basename($backupPath)."\n";

    if (file_put_contents($autoloadPsr4Path, $patched) === false) {
        echo "ERROR: could not write to {$autoloadPsr4Path} -- check file permissions.\n";
        exit;
    }

    echo "Patched successfully -- added Webkul\\ShopExtension\\ entry.\n";
}

section('2. Checking for an optimized/static autoloader that might bypass the file above');

$autoloadStaticPath = $basePath.'/vendor/composer/autoload_static.php';

if (file_exists($autoloadStaticPath)) {
    $staticContents = file_get_contents($autoloadStaticPath);

    if (str_contains($staticContents, 'ShopExtension')) {
        echo "autoload_static.php already mentions ShopExtension -- fine.\n";
    } else {
        echo "WARNING: autoload_static.php exists and does NOT mention ShopExtension.\n";
        echo "This file can take priority over autoload_psr4.php when Composer's\n";
        echo "'optimized autoloader' mode was baked in at the last full composer install.\n";
        echo "If step 4 below still shows NO after this fix, this is why -- see the\n";
        echo "note printed at the end of this report.\n";
    }
} else {
    echo "No autoload_static.php found -- not using the static/optimized autoloader, good.\n";
}

section('3. Clearing Laravel caches (no shell needed)');

try {
    require $basePath.'/vendor/autoload.php';

    $app = require $basePath.'/bootstrap/app.php';

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $exitCode = $kernel->call('optimize:clear');
    echo "optimize:clear exit code: {$exitCode}\n";
    echo $kernel->output();
} catch (\Throwable $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
}

section('4. Re-checking (fresh PHP process, freshly-patched autoload file)');

echo "NOTE: this section reuses the same PHP process/autoloader instance loaded\n";
echo "above in section 3, so it may not reflect the patch. Reload the homepage\n";
echo "directly afterwards for the real, fresh-process test.\n\n";

$classExists = class_exists(\Webkul\ShopExtension\Providers\ShopExtensionServiceProvider::class);
echo 'Webkul\\ShopExtension\\Providers\\ShopExtensionServiceProvider class_exists(): '.($classExists ? "YES\n" : "NO\n");

section('Done -- now reload https://laravel.demo.softorio.com/ and check the homepage');

echo "\nIf it's still broken and section 2 above printed a WARNING about\n";
echo "autoload_static.php, tell me and I'll patch that file too.\n";
echo "\nDelete both deploy-check-*.php and deploy-fix-*.php via cPanel File Manager when done.\n";
