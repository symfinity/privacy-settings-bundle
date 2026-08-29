# Strict attribute policy

This bundle supports **one** privacy declaration attribute:

- **Canonical:** `data-privacy-category`

There is no alias mode, normalization, or compatibility shim.

## Forbidden attribute names

These names are **explicitly rejected** — they were never part of this bundle's contract. They sometimes appear in third-party cookie-consent snippets or copy-pasted examples; do not use them in Symfinity templates:

- `data-cookiecategory`
- `data-cc`

If you wire `TemplateAttributeContract` or `StrictAttributeValidator` in CI and see `PRIVACY_FORBIDDEN_ATTRIBUTE`, search templates (and included partials) for those strings and replace them with `data-privacy-category`. The bundle does **not** scan templates at render time by default.

## Checklist

1. Use only `data-privacy-category` on elements that declare a privacy category.
2. Run bundle unit/integration tests or `MarkupDeclarationScanner` in CI to catch forbidden names.
3. Do not mix canonical and forbidden attributes on the same element.

## See also

- [Strict attribute contract](contracts/strict-attribute-contract.md)
- [Usage](usage.md)
- [Troubleshooting](troubleshooting.md)

## Non-goals

- No alias remapping or silent normalization
- No compatibility mode for alternate attribute names
