# Troubleshooting

## Forbidden legacy attribute in template

**Cause:** Markup uses `data-cookiecategory` or `data-cc`.

**Fix:** Replace with canonical `data-privacy-category`. Run `StrictAttributeValidator` or bundle tests to locate offending templates. See [strict-attribute-migration](strict-attribute-migration.md).

## Unknown preference category in capture payload

**Cause:** Capture payload includes a category id not present in `symfinity_privacy_settings.categories`.

**Fix:** Align capture code and config. Run `debug:config symfinity_privacy_settings` and verify ids.

## Required category disabled

**Cause:** Capture attempted to set a `default_state: required` category to `false`.

**Fix:** Always submit required categories as enabled. ConsentBanner renders them as disabled checkboxes to prevent this in UI-driven flows.

## Consent choices not persisted between requests

**Cause:** Default `InMemoryPreferenceStore` is process-local.

**Fix:** Implement `PreferenceStoreInterface` with your database or cache — see [Usage](usage.md).

## ConsentBanner unstyled or missing form layout

**Cause:** `symfinity/ux-blocks-form` CSS not loaded, or glue CSS missing.

**Fix:** Ensure ux-blocks-form AssetMapper paths are active and import `privacy-settings-bundle/styles/privacy-settings-consent.css`. Optional `symfinity/ui-kernel` improves themed spacing and colours.

## Getting help

[GitHub Issues](https://github.com/symfinity/privacy-settings-bundle/issues)
