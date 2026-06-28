# Overview

`symfinity/privacy-settings-bundle` provides a strict privacy category model, consent capture and restore services, template attribute validation, and a form-tier **ConsentBanner** UI composed from `symfinity/ux-blocks-form`.

## Capabilities

| Area | Summary |
|------|---------|
| Configuration | Declarative `symfinity_privacy_settings.categories` model (default four categories) |
| Consent | Capture, restore, store port, and `ConsentDecisionEvent` — **adopters wire enforcement** |
| Markup | Canonical `data-privacy-category` only — alternate names rejected |
| Events | `ConsentDecisionEvent` on preference changes |
| Consent UI | `ConsentBanner` Twig component + glue CSS |
| Enforcement (v0.2) | `privacy_consent()`, `PrivacyMediaEmbed`, opt-in script unblocker — [Enforcement](enforcement.md) |

## Handbook

- [Installation](installation.md)
- [Quickstart](quickstart.md)
- [Configuration](configuration.md)
- [Usage](usage.md)
- **[Enforcement — block integrations in Twig](enforcement.md)**
- [Verification](verification.md)
- [Upgrade](upgrade.md)
- [Troubleshooting](troubleshooting.md)
- [Reference](reference.md)
- [Strict attribute policy](strict-attribute-policy.md)
- [Contracts](contracts/strict-attribute-contract.md)
