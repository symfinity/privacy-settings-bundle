# Upgrade and migration

## Initial release — 0.1.0

First public release with:

- Strict `data-privacy-category` policy (no legacy alias support)
- Consent capture/restore services and in-memory store
- Flex `0.1` recipe
- Form-tier `ConsentBanner` UI

### Breaking policy

There is **no** migration path from legacy cookie attributes. Replace forbidden aliases before upgrading — see [strict-attribute-migration](strict-attribute-migration.md).

### From unreleased dev snapshots

If you consumed `dev-main` before `0.1.0`, pin `^0.1` and re-run Flex recipe merge for `symfinity_privacy_settings.yaml`.

## See also

[Configuration](configuration.md)
