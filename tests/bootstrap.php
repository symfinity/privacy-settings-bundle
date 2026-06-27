<?php

declare(strict_types=1);

/**
 * PHPUnit bootstrap — single Composer autoload only.
 *
 * Inside the symfinity monorepo, never combine /app/vendor with
 * packages/privacy-settings-bundle/vendor — that fatals on Psr\Container redeclare.
 */

$packageRoot = dirname(__DIR__);
$monorepoRoot = dirname($packageRoot, 2);
$packageAutoload = $packageRoot.'/vendor/autoload.php';
$monorepoAutoload = $monorepoRoot.'/vendor/autoload.php';
$isMonorepo = is_file($monorepoRoot.'/mono.json') && is_file($monorepoAutoload);
$packageVendorDir = realpath($packageRoot.'/vendor') ?: $packageRoot.'/vendor';

$packageAutoloadActive = false;
foreach (spl_autoload_functions() ?: [] as $loader) {
    if (!\is_array($loader) || !$loader[0] instanceof \Composer\Autoload\ClassLoader) {
        continue;
    }

    $ref = new \ReflectionClass($loader[0]);
    $classFile = $ref->getFileName();
    if (!\is_string($classFile)) {
        continue;
    }

    $vendorDir = realpath(dirname($classFile, 2)) ?: '';

    if ($vendorDir === $packageVendorDir) {
        $packageAutoloadActive = true;
        break;
    }
}

if ($isMonorepo && is_file($packageAutoload) && $packageAutoloadActive) {
    fwrite(STDERR, <<<'TXT'
Unsafe test bootstrap: symfinity monorepo vendor AND packages/privacy-settings-bundle/vendor are both present,
and vendor/bin/phpunit already loaded the package autoload. PHPUnit will fatal on Psr\Container.

Use instead:
  docker compose --env-file .env.docker run --rm -T -w /app php vendor/bin/phpunit packages/privacy-settings-bundle/tests/
  docker compose --env-file .env.docker run --rm -T -w /app/packages/privacy-settings-bundle php vendor/bin/phpunit
  rm -rf packages/privacy-settings-bundle/vendor

TXT);

    exit(1);
}

if (!class_exists(\Composer\Autoload\ClassLoader::class, false)) {
    if ($isMonorepo) {
        require $monorepoAutoload;
    } elseif (is_file($packageAutoload)) {
        require $packageAutoload;
    } else {
        fwrite(STDERR, "Composer autoload not found — run composer install in the package or monorepo.\n");
        exit(1);
    }
}
