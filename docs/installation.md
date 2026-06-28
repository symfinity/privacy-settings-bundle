# Installation

## Requirements

- PHP 8.2 or higher
- Symfony 7.4 or 8.x
- `symfinity/ux-blocks-form` ^0.1 (pulled transitively by this bundle)

Optional: `symfinity/ui-kernel` for themed consent UI in kernel-powered apps.

## Composer

```bash
composer require symfinity/privacy-settings-bundle
```

## Symfony Flex

Add the [symfinity/recipes](https://github.com/symfinity/recipes) Flex endpoint to your project's `composer.json` if it is not already configured.

The `0.1` recipe:

1. Registers `PrivacySettingsBundle` in `config/bundles.php`
2. Copies `config/packages/symfinity_privacy_settings.yaml` from the package
3. Copies `config/routes/symfinity_privacy_settings.yaml` (consent submit POST route)
4. Seeds default `required` and `analytics` categories

Ensure `symfinity/ux-blocks-form` is installed (Composer resolves it automatically). Run the ux-blocks-form recipe first if your project does not yet use form-tier components.

## Manual installation

When Flex is unavailable:

1. Register `Symfinity\PrivacySettingsBundle\PrivacySettingsBundle` in `config/bundles.php`
2. Copy `vendor/symfinity/privacy-settings-bundle/config/packages/symfinity_privacy_settings.yaml` to `config/packages/`
3. Copy `vendor/symfinity/privacy-settings-bundle/config/routes/symfinity_privacy_settings.yaml` to `config/routes/`
4. Require `symfinity/ux-blocks-form` and enable AssetMapper paths from that package

## Verify installation

```bash
php bin/console debug:config symfinity_privacy_settings
```

You should see the configured category list.

## Next steps

- [Quickstart](quickstart.md)
- [Configuration](configuration.md)
