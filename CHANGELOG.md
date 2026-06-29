# Changelog

All notable changes to **symfinity/privacy-settings-bundle** are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.2] - 2026-06-29

Patch release after [v0.2.1](https://github.com/symfinity/privacy-settings-bundle/releases/tag/v0.2.1). Split mirror maintenance — no consent API, enforcement helpers, or configuration changes.

### Removed

- **`docs.yaml`** — internal handbook compile manifest (split mirror ships `docs/` directly)

### Changed

- **Split mirror CI** — Composer package cache and `GITHUB_TOKEN` authentication so GitHub Actions reliably resolves `symfinity/*` dependencies across the PHP × Symfony matrix
- **README** — Packagist downloads badge alongside the release badge

### Added

- Composer **`funding`** metadata for [GitHub Sponsors](https://github.com/sponsors/serotoninja)
- **`.github/FUNDING.yml`** — GitHub Sponsors link on the split mirror repository

### Notes

- No Twig function names, `symfinity_privacy_settings` keys, or Flex recipe changes since v0.2.1
- `composer update symfinity/privacy-settings-bundle` is sufficient; no template or YAML migrations

## [0.2.1] - 2026-06-29

### Fixed

- **PrivacyMediaEmbed Load** — clicking **Load** now hides the facade and shows the iframe at the correct size (absolute facade/frame layout, `privacy-media-embed--loaded` state, eager iframe fetch on unlock)
- **`aspectRatio` prop** — values such as `16/9` and `4/3` normalize to spaced CSS ratios (`16 / 9`, `4 / 3`) so `aspect-ratio` applies reliably in all browsers

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
