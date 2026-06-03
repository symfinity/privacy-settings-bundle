# Quickstart: symfinity/privacy-settings-bundle

## Install

```bash
composer require symfinity/privacy-settings-bundle
```

With Flex, the recipe registers the bundle and copies default category config.

## Canonical markup

```twig
<script type="text/plain" data-privacy-category="analytics"></script>
```

## Forbidden (strict policy)

- `data-cookiecategory`
- `data-cc`

See [strict-attribute-contract](contracts/strict-attribute-contract.md).

## Dogfood

`symfinity/ux-blocks-demo` — route `/privacy-settings`.

## Tests

```bash
cd src/symfinity
./sbin/php packages/privacy-settings-bundle/vendor/bin/phpunit -c packages/privacy-settings-bundle/phpunit.xml.dist
```
