# Upgrade and migration

## 0.2.1 (patch)

Bugfix release for **PrivacyMediaEmbed** — **Load** unlock and embed sizing. No config or recipe changes.

```bash
composer update symfinity/privacy-settings-bundle:^0.2.1
```

## 0.2.0 (from 0.1.x)

### What you get

- Twig enforcement: `privacy_consent()`, `privacy_effective_choices()`, and `PrivacyMediaEmbed`
- Optional client script unblocker via `enforcement.client_scripts`
- Signed cookie store as the default persistence driver
- ConsentBanner **Cookie settings** reopen after a stored decision
- Default category set expanded to four ids: `required`, `analytics`, `marketing`, `media`

### Upgrade steps

1. `composer update symfinity/privacy-settings-bundle:^0.2`
2. Re-merge or diff `config/packages/symfinity_privacy_settings.yaml` from the package default (or Flex recipe when updated):
   - Add `enforcement.client_scripts: false` unless you need declarative script tags
   - Confirm `storage.driver: cookie` and cookie options match your policy
   - Add `marketing` and `media` categories if you only had `required` + `analytics`
3. Gate integrations in Twig — see [Enforcement](enforcement.md). The banner alone does not block analytics or embeds.
4. When category ids or `default_state` values change, bump `storage.cookie.policy_version` so returning visitors re-consent.

### Optional: enable declarative scripts

```yaml
symfinity_privacy_settings:
    enforcement:
        client_scripts: true
```

Only enable when you use `<script type="text/plain" data-privacy-category="…">` in templates. Prefer `privacy_consent()` for new work.

## Initial release — 0.1.0

First public release with:

- Strict `data-privacy-category` policy (no alternate attribute names)
- Consent capture/restore services and preference store port
- Flex `0.1` recipe
- Form-tier `ConsentBanner` UI

### Policy

Alternate attribute names (`data-cookiecategory`, `data-cc`) were never supported. Use `data-privacy-category` from the start — see [strict attribute policy](strict-attribute-policy.md).

### From unreleased dev snapshots

If you consumed `dev-main` before `0.1.0`, pin `^0.1` and re-run Flex recipe merge for `symfinity_privacy_settings.yaml`.

## See also

- [Configuration](configuration.md)
- [Enforcement](enforcement.md)
