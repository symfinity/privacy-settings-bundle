# Strict attribute migration guide

This bundle intentionally supports only one markup contract:

- Canonical attribute: `data-privacy-category`

## Unsupported legacy aliases

The following aliases are not tolerated and are always rejected:

- `data-cookiecategory`
- `data-cc`

## Migration checklist

1. Replace all legacy attributes with `data-privacy-category`.
2. Run bundle unit/integration tests to verify forbidden aliases fail.
3. Validate host templates do not mix canonical and forbidden attributes.

## Non-goals

- No compatibility mode for legacy aliases
- No silent alias remapping
- No downgrade path that weakens strict validation
