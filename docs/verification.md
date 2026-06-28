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

Expect four default categories (`required`, `analytics`, `marketing`, `media`) unless you customized YAML.

## ConsentBanner render smoke

Run the integration render test:

```bash
./bin/php vendor/bin/phpunit packages/privacy-settings-bundle/tests/Integration/ConsentBannerRenderTest.php
```

Or embed `ConsentBanner` in a Twig template and confirm `data-ui-role` markers for form atoms appear in HTML.

## Clean-app Flex smoke (MUST before public tag)

Org contract: **v0.1.0 consumer experience gate** — integration profile **P2** (embed). See symfinity monorepo `_org/contracts/v0.1.0-consumer-experience-gate.md`.

From org checkout (Flex endpoint + recipe assert):

```bash
make dogfood-new PACKAGE=symfinity/privacy-settings-bundle SLUG=privacy-settings-cx-smoke VERSION='7.4.*' FORCE=1
# expect stdout: recipe: applied (symfinity/privacy-settings-bundle)
make dogfood-serve SLUG=privacy-settings-cx-smoke
```

In a fresh Symfony app with only the symfinity/recipes endpoint (no path repos):

```bash
composer require symfinity/privacy-settings-bundle
php bin/console debug:config symfinity_privacy_settings
```

Confirm default categories are present without hand-editing YAML. For visible UI, follow [Quickstart](quickstart.md) embed steps — v0.1.0 **must** classify those as customization or automate embed per consumer gate **G5**.

## Recipe validation (maintainers)

```bash
cd src/symfinity
./bin/php vendor/bin/mono recipes:validate --tier=v1
```

## See also

- [Troubleshooting](troubleshooting.md)
- [CHANGELOG](../CHANGELOG.md)
