# Configuration

## Triple alignment

| Piece | Value |
|-------|-------|
| `Configuration.php` root | `symfinity_privacy_settings` |
| Consumer file | `config/packages/symfinity_privacy_settings.yaml` |
| YAML document root | `symfinity_privacy_settings:` |

## Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `categories` | list | `[]` | Privacy categories exposed to capture, restore, and UI |
| `categories[].id` | string | required | Stable kebab-case identifier |
| `categories[].label` | string | required | Human-readable label for UI |
| `categories[].default_state` | enum | `disabled` | One of `required`, `enabled`, `disabled` |
| `categories[].description` | string | `''` | Optional helper text in ConsentBanner |

## `default_state` semantics

| Value | Effective default | UI behaviour |
|-------|-------------------|--------------|
| `required` | Always `true` | Cannot be disabled; rendered as locked checkbox |
| `enabled` | `true` until visitor opts out | Switch defaults on |
| `disabled` | `false` until visitor opts in | Switch defaults off |

## Container parameter

Normalized raw config is exposed as:

```text
symfinity.privacy_settings.categories
```

Inject via `#[Autowire(param: 'symfinity.privacy_settings.categories')]` or `%symfinity.privacy_settings.categories%`.

## Environment variables

No dedicated env vars ship with this bundle. You may reference `%env(...)%` placeholders inside YAML values when your deployment needs them.

## Consent UI fallbacks

When `symfinity/ui-kernel` is absent, glue CSS uses documented `--ui-*` fallbacks. See [Usage](usage.md).

## See also

[Usage](usage.md)
