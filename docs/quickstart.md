# Quickstart

## Install

```bash
composer require symfinity/privacy-settings-bundle
```

With Flex, the recipe registers the bundle and copies default category config.

## Configure categories

```yaml
# config/packages/symfinity_privacy_settings.yaml
symfinity_privacy_settings:
    categories:
        - id: required
          label: Required
          default_state: required
        - id: analytics
          label: Analytics
          default_state: disabled
          description: Optional usage analytics
```

## Capture and restore (PHP)

```php
use Symfinity\PrivacySettingsBundle\Consent\PreferenceCaptureService;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceRestoreService;
use Symfinity\PrivacySettingsBundle\Symfony\CategoryModelNormalizer;

/** @var CategoryModelNormalizer $normalizer */
$categories = $normalizer->normalize($this->getParameter('symfinity.privacy_settings.categories'));

$restore = $this->restoreService->effectiveChoices('visitor-123', $categories);
// ['required' => true, 'analytics' => false]

$capture->capture('visitor-123', $categories, ['required' => true, 'analytics' => true]);
```

## Embed ConsentBanner (Twig)

Requires `symfinity/ux-blocks-form`. Suggest `symfinity/ui-kernel` when your layout already exposes `[data-theme]` tokens.

```twig
{# templates/base.html.twig #}
{% block body %}
    {{ component('ConsentBanner', { subjectKey: app.session.id }) }}
    {% block content %}{% endblock %}
{% endblock %}
```

Import glue CSS in your layout (AssetMapper):

```twig
<link rel="stylesheet" href="{{ asset('privacy-settings-bundle/styles/privacy-settings-consent.css') }}">
```

## Mark blocked assets

```twig
<script type="text/plain" data-privacy-category="analytics" src="/assets/analytics.js"></script>
```

See [strict-attribute-contract](contracts/strict-attribute-contract.md) for forbidden aliases.

## Headless use

Skip rendering `ConsentBanner` and call capture/restore services directly — see [Usage](usage.md).

## Maintainer tests (monorepo)

```bash
cd src/symfinity
./bin/php vendor/bin/phpunit packages/privacy-settings-bundle/tests/
```
