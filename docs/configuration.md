# Configuration

## Triple alignment

| Piece | Value |
|-------|-------|
| `Configuration.php` root | `symfinity_privacy_settings` |
| Consumer file | `config/packages/symfinity_privacy_settings.yaml` |
| YAML document root | `symfinity_privacy_settings:` |

## Default category set

Flex and manual installs copy the package defaults — **four categories** that map to typical enforcement patterns in [Enforcement](enforcement.md):

| `id` | `default_state` | Role | Typical enforcement |
|------|-----------------|------|---------------------|
| `required` | `required` | Security, consent memory, core functionality | Always effective; not gated in Twig |
| `analytics` | `disabled` | Usage and performance measurement | `privacy_consent('analytics')`, analytics scripts |
| `marketing` | `disabled` | Ads, retargeting, campaign pixels | `privacy_consent('marketing')`, ad tags |
| `media` | `disabled` | Third-party players and embeds | `PrivacyMediaEmbed`, `privacy_consent('media')` |

Full default YAML (edit labels and descriptions for your policy; keep ids stable unless you migrate stored consent):

```yaml
symfinity_privacy_settings:
    enforcement:
        client_scripts: false
    storage:
        driver: cookie
        cookie:
            lifetime_days: 180
            policy_version: '1'
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

`description` appears in ConsentBanner per-category settings. `label` is the short title. Omit categories you do not need, or add custom ids — every id you reference from Twig or capture payloads must exist here.

## Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `categories` | list | `[]` | Privacy categories exposed to capture, restore, and UI |
| `storage.driver` | enum | `cookie` | `cookie` (signed consent cookie) or `session` (PHP session bag) |
| `storage.cookie.name` | string | `symfinity_privacy_consent` | First-party cookie name |
| `storage.cookie.lifetime_days` | int | `180` | Cookie lifetime in days (**minimum 180**) |
| `storage.cookie.policy_version` | string | `1` | Bump when categories change to invalidate old cookies |
| `storage.cookie.path` | string | `/` | Cookie path |
| `storage.cookie.domain` | string\|null | `null` | Optional cookie domain |
| `storage.cookie.secure` | enum | `auto` | `auto`, `true`, or `false` |
| `storage.cookie.samesite` | enum | `lax` | `lax`, `strict`, or `none` |
| `storage.cookie.http_only` | bool | `true` | HttpOnly flag (recommended) |
| `categories[].id` | string | required | Stable kebab-case identifier |
| `categories[].label` | string | required | Human-readable label for UI |
| `categories[].default_state` | enum | `disabled` | One of `required`, `enabled`, `disabled` |
| `categories[].description` | string | `''` | Optional helper text in ConsentBanner |
| `enforcement.client_scripts` | bool | `false` | Enables client unblocker for [declarative script tags](enforcement.md#4-data-privacy-category--opt-in-client-unblocker) only — does not affect `privacy_consent()` or `PrivacyMediaEmbed` |

## `default_state` semantics

| Value | Effective default | UI behaviour |
|-------|-------------------|--------------|
| `required` | Always `true` | Cannot be disabled; rendered as locked checkbox |
| `enabled` | `true` until visitor opts out | Switch defaults on |
| `disabled` | `false` until visitor opts in | Switch defaults off |

Changing effective choices updates storage and emits `ConsentDecisionEvent`. Gate integrations with [Enforcement](enforcement.md) helpers or your own listeners.

## Container parameter

```text
symfinity.privacy_settings.categories
```

Inject via `#[Autowire(param: 'symfinity.privacy_settings.categories')]` or `%symfinity.privacy_settings.categories%`.

## Environment variables

No dedicated env vars ship with this bundle. You may reference `%env(...)%` placeholders inside YAML values when your deployment needs them.

## Consent UI fallbacks

When `symfinity/ui-kernel` is absent, glue CSS uses documented `--ui-*` fallbacks. See [Usage](usage.md).

## See also

[Enforcement — Twig blocking methods](enforcement.md) · [Usage](usage.md)
