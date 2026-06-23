<div align="center">

# Privacy Settings Bundle

### Strict privacy category settings and consent decisions for Symfony

[![PHP Version](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)](composer.json)
[![Symfony](https://img.shields.io/badge/Symfony-6.4+-343434?style=flat&logo=symfony&logoColor=white)](composer.json)
<br/>
[![CI](https://github.com/symfinity/privacy-settings-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/symfinity/privacy-settings-bundle/actions/workflows/ci.yml)
<br/>
[![Release](https://img.shields.io/packagist/v/symfinity/privacy-settings-bundle.svg?style=flat&logo=packagist&logoColor=white)](https://packagist.org/packages/symfinity/privacy-settings-bundle)
[![Downloads](https://img.shields.io/packagist/dt/symfinity/privacy-settings-bundle.svg?style=flat&logo=packagist&logoColor=white)](https://packagist.org/packages/symfinity/privacy-settings-bundle)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat)](LICENSE)

</div>

> [!NOTE]
> **Read-only mirror.**
> See [CONTRIBUTING.md](CONTRIBUTING.md) for how to propose changes.

## Features

- **Canonical attribute** — `data-privacy-category` only
- **Forbidden aliases** — rejects `data-cookiecategory` and `data-cc`
- **Deterministic validation** — clear errors for invalid categories and config
- **Flex recipe** — bundle and default categories on install

## Prerequisites

Add the [symfinity/recipes](https://github.com/symfinity/recipes) Flex endpoint to your project's `composer.json` (see [recipes README](https://github.com/symfinity/recipes/blob/main/README.md)) — recipes are not in Symfony's official recipe repository yet.

## Installation

```bash
composer require symfinity/privacy-settings-bundle
```

See [Installation](docs/installation.md).

## Quick Start

```yaml
# config/packages/symfinity_privacy_settings.yaml
symfinity_privacy_settings:
  categories:
    - id: analytics
      label: Analytics
      default_state: disabled
```

```twig
<script data-privacy-category="analytics" src="/assets/analytics.js"></script>
```

See [Quick start](docs/quickstart.md) for the full walkthrough.

## Documentation

- **[Quick start](docs/quickstart.md)** — minimal setup path
- **[Installation](docs/installation.md)** — Flex, dependencies, verify
- **[Configuration](docs/configuration.md)** — bundle and app options
- **[Usage](docs/usage.md)** — day-to-day patterns
- **[Upgrade](docs/upgrade.md)** — version migrations

## Requirements

- PHP 8.1 or higher
- Symfony 6.4, 7.x, or 8.x

## Support

- [GitHub Issues](https://github.com/symfinity/privacy-settings-bundle/issues)
- [Security](.github/SECURITY.md)
- [Contributing](CONTRIBUTING.md)

## License

[MIT](LICENSE)
