# Verification

## Package tests

From the package directory:

```bash
composer test
composer phpstan
```

From the Symfinity product monorepo:

```bash
cd src/symfinity
./bin/php vendor/bin/phpunit packages/privacy-settings-bundle/tests/
./bin/php vendor/bin/phpstan analyse -c packages/privacy-settings-bundle/phpstan.neon.dist
```

## Config smoke

```bash
php bin/console debug:config symfinity_privacy_settings
```

Expect configured categories to match your YAML.

## ConsentBanner render smoke

Run the integration render test:

```bash
./bin/php vendor/bin/phpunit packages/privacy-settings-bundle/tests/Integration/ConsentBannerRenderTest.php
```

Or embed `ConsentBanner` in a Twig template and confirm `data-ui-role` markers for form atoms appear in HTML.

## Clean-app Flex smoke (optional)

In a fresh Symfony app with the symfinity/recipes endpoint:

```bash
composer require symfinity/privacy-settings-bundle
php bin/console debug:config symfinity_privacy_settings
```

## Recipe validation (maintainers)

```bash
cd src/symfinity
./bin/php vendor/bin/mono recipes:validate --tier=v1
```

## See also

- [Troubleshooting](troubleshooting.md)
- [CHANGELOG](../CHANGELOG.md)
