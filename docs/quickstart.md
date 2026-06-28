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

## Symfony integration

Flex copies `config/routes/symfinity_privacy_settings.yaml` (POST `/_privacy/consent/{subjectKey}`). Manual installs: copy that file from the package into `config/routes/` or import the bundle file:

```yaml
# config/routes/symfinity_privacy_settings.yaml
symfinity_privacy_settings:
    resource: '@PrivacySettingsBundle/config/routes.yaml'
```

`ConsentSubmitController` reads the posted `privacy[…]` fields, calls `PreferenceCaptureService`, and redirects back to the referer.

### React to consent changes

Listen for `ConsentDecisionEvent` when a category toggles (banner save or programmatic capture):

```php
<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfinity\PrivacySettingsBundle\Event\ConsentDecisionEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class AnalyticsConsentListener
{
    public function __invoke(ConsentDecisionEvent $event): void
    {
        if ('analytics' !== $event->categoryId || !$event->newState) {
            return;
        }

        // Enable analytics scripts, tag managers, etc.
    }
}
```

### Read choices in a controller

Inject `PreferenceRestoreService` when you need the effective state for a visitor key:

```php
use Symfinity\PrivacySettingsBundle\Consent\PreferenceRestoreService;
use Symfinity\PrivacySettingsBundle\Symfony\CategoryModelNormalizer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

public function __construct(
    private readonly PreferenceRestoreService $restoreService,
    private readonly CategoryModelNormalizer $normalizer,
    #[Autowire(param: 'symfinity.privacy_settings.categories')]
    private readonly array $rawCategories,
) {}

public function dashboard(string $subjectKey): Response
{
    $categories = $this->normalizer->normalize($this->rawCategories);
    $choices = $this->restoreService->effectiveChoices($subjectKey, $categories);

    // $choices['analytics'] === true|false
}
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
