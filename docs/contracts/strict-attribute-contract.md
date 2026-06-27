# Contract: strict privacy attribute policy

**Package:** `symfinity/privacy-settings-bundle`

## Canonical rule

Privacy runtime declarations must use canonical strict attributes defined by this bundle. Attribute aliasing is not supported.

## Forbidden legacy aliases

The following attributes are explicitly forbidden and must be rejected:

- `data-cookiecategory`
- `data-cc`

## Validation outcomes

| Case | Outcome |
|------|---------|
| Canonical attribute declaration | Accepted and evaluated |
| Unknown privacy attribute | Rejected with deterministic validation error |
| `data-cookiecategory` or `data-cc` present | Rejected as forbidden legacy alias |
| Mixed canonical + forbidden legacy on same declaration | Rejected |

## Error contract

- Errors must be deterministic and machine-readable.
- Error payloads/messages must include:
  - offending attribute name
  - declaration context reference (when available)
  - stable error code identifier

## Compliance notes

- Runtime behavior must not silently accept, normalize, or map forbidden aliases.
- QA checks must include negative coverage proving rejection of forbidden aliases.
