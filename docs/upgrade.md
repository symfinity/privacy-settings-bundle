# Upgrade and migration

## Initial release — 0.1.0

First public release with:

- Strict `data-privacy-category` policy (no alternate attribute names)
- Consent capture/restore services and in-memory store
- Flex `0.1` recipe
- Form-tier `ConsentBanner` UI

### Breaking policy

Alternate attribute names (`data-cookiecategory`, `data-cc`) were never supported. Use `data-privacy-category` from the start — see [strict attribute policy](strict-attribute-policy.md).

### From unreleased dev snapshots

If you consumed `dev-main` before `0.1.0`, pin `^0.1` and re-run Flex recipe merge for `symfinity_privacy_settings.yaml`.

## See also

[Configuration](configuration.md)
