# Verification

**Integration profile:** P2 — embed (ConsentBanner + auto glue CSS).

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

Or embed `<twig:ConsentBanner />` (or `{{ component('ConsentBanner') }}`) in a Twig template and confirm `data-ui-role` markers for form atoms appear in HTML.

## Clean-app smoke

On a fresh Symfony 7.4+ project:

```bash
composer require symfinity/privacy-settings-bundle
php bin/console debug:config symfinity_privacy_settings
```

Embed `<twig:ConsentBanner />` in your layout — expect `data-privacy-consent`, form atom `data-ui-role` markers, and `id="ux-blocks-core-css"` / `id="ux-blocks-form-css"` (auto-injected glue CSS). Demo route `/_privacy/demo` is **dev/test only** (`demo.route_enabled: false` in prod).

Org maintainer dogfood (recipe assert):

```bash
make dogfood-new PACKAGE=symfinity/privacy-settings-bundle SLUG=privacy-settings-cx-smoke VERSION='7.4.*' FORCE=1
make dogfood-serve SLUG=privacy-settings-cx-smoke
```

## Clean-app Flex smoke (maintainers)

Integration profile **P2** (embed): layout embed + auto-injected ux-blocks CSS; demo route dev/test only.

From org checkout (Flex endpoint + recipe assert; **dev** env — demo route enabled via `when@dev`):

```bash
make dogfood-new PACKAGE=symfinity/privacy-settings-bundle SLUG=privacy-settings-cx-smoke VERSION='7.4.*' FORCE=1
# expect stdout: recipe: applied (symfinity/privacy-settings-bundle)
make dogfood-serve SLUG=privacy-settings-cx-smoke
curl -sI http://127.0.0.1:<port>/_privacy/demo | grep '200'   # dev/test only
```

In a fresh Symfony app with only the symfinity/recipes endpoint (no path repos), **production** smoke uses layout embed + render test — demo route **MUST NOT** respond in `APP_ENV=prod`:

```bash
composer require symfinity/privacy-settings-bundle
php bin/console debug:config symfinity_privacy_settings
php bin/console debug:router symfinity_privacy_settings_demo 2>&1 | grep -q 'does not exist'  # prod: no demo route
./bin/php vendor/bin/phpunit packages/privacy-settings-bundle/tests/Integration/ConsentBannerRenderTest.php
```

Dev maintainer check (optional):

```bash
curl -sI http://127.0.0.1:8000/_privacy/demo | grep '200'
```

Confirm default categories are present without hand-editing YAML. In dev, the demo page contains `data-privacy-consent` and `id="ux-blocks-core-css"`.

## Recipe validation (maintainers)

```bash
cd src/symfinity
./bin/php vendor/bin/mono recipes:validate --tier=v1
```

## See also

- [Troubleshooting](troubleshooting.md)
- [CHANGELOG](../CHANGELOG.md)
