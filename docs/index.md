# Overview

`symfinity/privacy-settings-bundle` provides a strict privacy category model, consent capture and restore services, template attribute validation, and a form-tier **ConsentBanner** UI composed from `symfinity/ux-blocks-form`.

## Capabilities

| Area | Summary |
|------|---------|
| Configuration | Declarative `symfinity_privacy_settings.categories` model |
| Consent | Capture, restore, and in-memory store port |
| Markup | Canonical `data-privacy-category` with forbidden legacy aliases |
| Events | `ConsentDecisionEvent` on preference changes |
| Consent UI | `ConsentBanner` Twig component + glue CSS |

## Handbook

- [Installation](installation.md)
- [Quickstart](quickstart.md)
- [Configuration](configuration.md)
- [Usage](usage.md)
- [Verification](verification.md)
- [Upgrade](upgrade.md)
- [Troubleshooting](troubleshooting.md)
- [Reference](reference.md)
- [Strict attribute migration](strict-attribute-migration.md)
- [Contracts](contracts/strict-attribute-contract.md)
