# Troubleshooting

## Forbidden attribute name in template

**Cause:** Markup uses `data-cookiecategory` or `data-cc`. Those names are not part of this bundle — only `data-privacy-category` is valid.

**Fix:** Replace with `data-privacy-category`. Search templates and partials for the forbidden strings (often copy-pasted from other consent tools). Run `StrictAttributeValidator` or bundle tests to locate offenders. See [strict attribute policy](strict-attribute-policy.md).

## Unknown preference category in capture payload

**Cause:** Capture payload includes a category id not present in `symfinity_privacy_settings.categories`.

**Fix:** Align capture code and config. Run `debug:config symfinity_privacy_settings` and verify ids.

## Required category disabled

**Cause:** Capture attempted to set a `default_state: required` category to `false`.

**Fix:** Always submit required categories as enabled. ConsentBanner renders them as disabled checkboxes to prevent this in UI-driven flows.

## Consent choices not persisted between requests

**Cause:** Custom `PreferenceStoreInterface` wiring replaced the default cookie store, `storage.cookie.lifetime_days` is too low, `storage.cookie.policy_version` changed without re-consent, or the browser blocks the consent cookie.

**Fix:** Default wiring uses `CookiePreferenceStore` (signed cookie `symfinity_privacy_consent`, configurable lifetime ≥ **180 days**). Bump `storage.cookie.policy_version` when categories change. After Accept all, confirm the consent cookie in DevTools → Application → Cookies. For audit trails or logged-in users, implement `PreferenceStoreInterface` with your database — see [Usage](usage.md).

## Accept all succeeds but the banner returns on reload

**Cause:** Submit and restore used different subject keys, or an old cookie was signed with a previous `policy_version`.

**Fix:** Use `{{ component('ConsentBanner') }}` without `subjectKey`, or an explicit stable key (for example a user id). Clear cookies and retest. Bump `storage.cookie.policy_version` deliberately when category config changes.

## Session driver only (legacy)

**Cause:** `storage.driver: session` keeps consent in the PHP session (`PHPSESSID`), which expires when the browser session ends (~session cookie) and is invisible as a dedicated consent cookie.

**Fix:** Prefer default `storage.driver: cookie` for anonymous consent. Use `session` only when you intentionally scope consent to the PHP session.

## ConsentBanner unstyled or missing form layout

**Cause:** `symfinity/ux-blocks-form` CSS not loaded, or AssetMapper cannot resolve the bundle asset path.

**Fix:** Ensure `symfinity/ux-blocks-form` Flex recipe ran (form-tier CSS). `ConsentBanner` emits the glue stylesheet link automatically; verify `debug:asset-map privacy-settings-bundle/styles/privacy-settings-consent.css` resolves. Optional `symfinity/ui-kernel` improves themed spacing and colours.

## Category toggle has no effect on analytics or third-party scripts

**Cause:** The bundle stores consent only. Rendering `ConsentBanner` does not load, unload, or block tag managers, analytics SDKs, or `data-privacy-category` scripts unless the consumer app wires enforcement.

**Fix:** Gate integrations with `PreferenceRestoreService`, listen for `ConsentDecisionEvent`, or implement a client runtime for marked assets. See [Applying consent choices](usage.md#applying-consent-choices).

## Getting help

[GitHub Issues](https://github.com/symfinity/privacy-settings-bundle/issues)
