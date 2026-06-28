# Quickstart

## Install

```bash
composer require symfinity/privacy-settings-bundle
```

With Flex, the recipe registers the bundle and copies default category config.

## Configure categories

The Flex recipe copies a **four-category** default (`required`, `analytics`, `marketing`, `media`). Adjust labels and descriptions for your policy; keep ids stable so stored consent and Twig gates stay aligned.

```yaml
# config/packages/symfinity_privacy_settings.yaml
symfinity_privacy_settings:
    categories:
        - id: required
          label: Required
          default_state: required
          description: Essential cookies and storage for security, remembering your consent choices, and core site functionality. Always active.
        - id: analytics
          label: Analytics
          default_state: disabled
          description: Optional measurement of how the site is used — page views, performance, and errors — to help improve the experience.
        - id: marketing
          label: Marketing
          default_state: disabled
          description: Optional cookies for ad campaign measurement and showing related content on other websites.
        - id: media
          label: Media
          default_state: disabled
          description: Optional third-party embeds such as video and audio players, maps, widgets, and social media content.
```

See [Configuration](configuration.md) for storage, enforcement, and `default_state` semantics.

## Symfony integration

Flex copies `config/routes/symfinity_privacy_settings.yaml` (POST `/_privacy/consent/{subjectKey}`). Manual installs: copy that file from the package into `config/routes/` or import the bundle file:

```yaml
# config/routes/symfinity_privacy_settings.yaml
symfinity_privacy_settings:
    resource: '@PrivacySettingsBundle/config/routes.yaml'
```

`ConsentSubmitController` reads the posted `privacy[…]` fields, calls `PreferenceCaptureService`, and redirects back to the referer.

The banner **records** choices. **v0.2** adds enforcement helpers — see [Enforcement](enforcement.md) for `privacy_consent()` and `PrivacyMediaEmbed`.

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
    {{ component('ConsentBanner') }}
    {% block content %}{% endblock %}
{% endblock %}
```

Omit `subjectKey` for anonymous visitors (`visitor` + signed consent cookie). Pass an explicit key when the subject is a logged-in account.

## Gate integrations (v0.2)

See **[Enforcement](enforcement.md)** for all Twig blocking methods (comparison table + examples).

```twig
{# 1. Server-side if — scripts, pixels, sections, manual iframes #}
{% if privacy_consent('analytics') %}
    <script src="/assets/analytics.js" defer></script>
{% endif %}

{# 2. Media iframe — uses media category; facade + Load when denied #}
<twig:PrivacyMediaEmbed provider="youtube" videoId="dQw4w9WgXcQ" title="Demo video" />

{# 3. Marketing pixel (same pattern as analytics) #}
{% if privacy_consent('marketing') %}
    <img src="https://example.com/pixel.gif" alt="" width="1" height="1" hidden>
{% endif %}

{# 4. Declarative script (requires enforcement.client_scripts: true) #}
<script type="text/plain" data-privacy-category="analytics" src="/assets/analytics.js"></script>
```

`ConsentBanner` loads `privacy-settings-bundle/styles/privacy-settings-consent.css` automatically via AssetMapper — no `importmap.php` or `app.js` import required.

## Mark blocked assets

Declare scripts or iframes that belong to a category. Prefer `privacy_consent()` for Twig snippets, or enable the opt-in unblocker — see [Enforcement](enforcement.md).

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
