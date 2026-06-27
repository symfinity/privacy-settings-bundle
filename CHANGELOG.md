# Changelog

All notable changes to **symfinity/privacy-settings-bundle** are documented here.

## [0.1.0] - 2026-06-27

### Added

- Symfony bundle with strict `data-privacy-category` validation
- Consent capture/restore services and in-memory store port
- `ConsentDecisionEvent` publishing on effective choice changes
- Flex `0.1` recipe (bundle + default config)
- Form-tier `ConsentBanner` Twig component and glue CSS
- Handbook spine, verification doc, split mirror CI, PHPStan gate

### Policy

- No legacy alias compatibility (`data-cookiecategory`, `data-cc`)
