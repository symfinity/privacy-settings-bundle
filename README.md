<div align="center">

# Privacy Settings Bundle

### Strict privacy categories, consent capture, and form-tier consent UI for Symfony

[![PHP Version](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)](composer.json)
[![Symfony](https://img.shields.io/badge/Symfony-7.4+-343434?style=flat&logo=symfony&logoColor=white)](composer.json)
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

- **Canonical attribute** — `data-privacy-category` only; rejects alternate names (`data-cookiecategory`, `data-cc`)
- **Consent services** — capture, restore, signed cookie store (default), and store port with events
- **ConsentBanner UI** — composes `symfinity/ux-blocks-form` roles + glue CSS; **Cookie settings** reopen
- **Enforcement (v0.2)** — `privacy_consent()`, `PrivacyMediaEmbed`, opt-in script unblocker — [docs/enforcement.md](docs/enforcement.md)
- **Flex recipe** — bundle registration and default four categories on install

## Prerequisites

Add the [symfinity/recipes](https://github.com/symfinity/recipes) Flex endpoint before `composer require`.

## Installation

```bash
composer require symfinity/privacy-settings-bundle
```

See [Installation](docs/installation.md).

## Quick start

```twig
{{ component('ConsentBanner') }}
```

See [Quickstart](docs/quickstart.md) and [Enforcement](docs/enforcement.md).

## Documentation

- [Handbook index](docs/index.md)
- [Quickstart](docs/quickstart.md)
- [Configuration](docs/configuration.md)
- [Usage](docs/usage.md)
- [Enforcement](docs/enforcement.md)
- [Verification](docs/verification.md)
- [Upgrade](docs/upgrade.md)
- [CHANGELOG](CHANGELOG.md)

## Requirements

- PHP 8.2+
- Symfony 7.4 or 8.x
- `symfinity/ux-blocks-form` ^0.1

Suggest `symfinity/ui-kernel` for themed apps.

## Support

- [GitHub Issues](https://github.com/symfinity/privacy-settings-bundle/issues)
- [Security](.github/SECURITY.md)
- [Contributing](CONTRIBUTING.md)

## License

[MIT](LICENSE)
