# Verification

**Integration profile:** P2 — embed (ConsentBanner + auto glue CSS).

## Package tests

From the package directory (split mirror or path repo):

```bash
composer test
composer phpstan
```

Or with PHPUnit directly:

```bash
vendor/bin/phpunit
vendor/bin/phpstan analyse -c phpstan.neon.dist
```

## Config smoke

```bash
php bin/console debug:config symfinity_privacy_settings
```

Expect four default categories (`required`, `analytics`, `marketing`, `media`) unless you customized YAML.

## ConsentBanner render smoke

Run the integration render test:

```bash
vendor/bin/phpunit tests/Integration/ConsentBannerRenderTest.php
```

Or embed `<twig:ConsentBanner />` (or `{{ component('ConsentBanner') }}`) in a Twig template and confirm `data-ui-role` markers for form atoms appear in HTML.

## Clean-app smoke

On a fresh Symfony 7.4+ project:

```bash
composer require symfinity/privacy-settings-bundle
php bin/console debug:config symfinity_privacy_settings
```

Embed `<twig:ConsentBanner />` in your layout — expect `data-privacy-consent`, form atom `data-ui-role` markers, and `id="ux-blocks-core-css"` / `id="ux-blocks-form-css"` (auto-injected glue CSS). Demo route `/_privacy/demo` is **dev/test only** (`demo.route_enabled: false` in prod).

In a fresh Symfony app with only the symfinity/recipes Flex endpoint (no path repos), **production** smoke uses layout embed + render test — demo route **MUST NOT** respond in `APP_ENV=prod`:

```bash
composer require symfinity/privacy-settings-bundle
php bin/console debug:config symfinity_privacy_settings
php bin/console debug:router symfinity_privacy_settings_demo 2>&1 | grep -q 'does not exist'  # prod: no demo route
vendor/bin/phpunit tests/Integration/ConsentBannerRenderTest.php
```

Optional dev check (when `APP_ENV=dev` and demo route is enabled):

```bash
curl -sI http://127.0.0.1:8000/_privacy/demo | grep '200'
```

Confirm default categories are present without hand-editing YAML. In dev, the demo page contains `data-privacy-consent` and `id="ux-blocks-core-css"`.

## See also

- [Troubleshooting](troubleshooting.md)
- [CHANGELOG](../CHANGELOG.md)
