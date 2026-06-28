# Changelog

All notable changes to **symfinity/privacy-settings-bundle** are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.0] - 2026-06-28

### Added

- **`privacy_consent()`** Twig function — server-side `{% if %}` gating for scripts, pixels, sections, and manual iframes
- **`privacy_effective_choices()`** Twig function — full per-category boolean map for custom UI or data attributes
- **`PrivacyMediaEmbed`** Twig component — YouTube, Vimeo, Google Maps, and generic HTTPS embeds with facade + page-local **Load** when the `media` category is denied
- **Opt-in client script unblocker** — `enforcement.client_scripts: true` activates Stimulus handling for `<script type="text/plain" data-privacy-category="…">` tags after consent
- **Signed cookie consent store** (default) — configurable cookie name, lifetime (minimum 180 days), and `policy_version` bump when category config changes
- **ConsentBanner reopen shell** — stored decisions hide the quick panel but keep **Cookie settings** via `privacy-settings:open-preferences`
- **Default four-category config** — `required`, `analytics`, `marketing`, and `media` with consumer-facing descriptions in package YAML
- **Handbook:** [Enforcement](docs/enforcement.md) — Twig blocking methods, decision guide, and examples; [strict attribute policy](docs/strict-attribute-policy.md)

### Changed

- Stimulus controllers live under the `privacy-settings-bundle/` AssetMapper namespace (`consent`, `media-embed`, `script-unblocker`)
- `symfony/asset` is required for consent glue CSS and component asset links

### Notes

- **ConsentBanner records choices only** — gate trackers and embeds in your templates with the enforcement helpers above (or your own listeners on `ConsentDecisionEvent`)
- **`client_scripts` stays off by default** — enable only when you rely on declarative `data-privacy-category` script tags
- Upgrading from **0.1.x**: merge updated `symfinity_privacy_settings.yaml` (four categories + optional `enforcement` block); see [upgrade.md](docs/upgrade.md)

## [0.1.0] - 2026-06-27

### Added

- Symfony bundle with strict `data-privacy-category` validation
- Consent capture/restore services and preference store port
- `ConsentDecisionEvent` publishing on effective choice changes
- Flex `0.1` recipe (bundle + default config)
- Form-tier `ConsentBanner` Twig component and glue CSS
- Handbook spine, verification doc, split mirror CI, PHPStan gate

### Policy

- Alternate attribute names rejected (`data-cookiecategory`, `data-cc` never supported)
