# Reference

## Classes and services

| Symbol | Role |
|--------|------|
| `PrivacySettingsBundle` | Symfony bundle entry |
| `Configuration` / `PrivacySettingsExtension` | `symfinity_privacy_settings` config tree |
| `PrivacyCategory` | Category value object |
| `PrivacySettingsException` | Domain validation errors |
| `PreferenceCaptureService` | Validates and stores choices |
| `PreferenceRestoreService` | Reads effective choices |
| `InMemoryPreferenceStore` | Default non-persistent store |
| `PreferenceStoreInterface` | Storage port |
| `ConsentDecisionEvent` | Emitted when a category's effective state changes — use to enable/disable integrations |
| `ConsentDecisionEventPublisher` | Compares previous vs new effective state |
| `CategoryModelNormalizer` | Config array to domain list |
| `StrictAttributeValidator` | Canonical attribute enforcement |
| `MarkupDeclarationScanner` | Template scanning helper |
| `ConsentBanner` | Twig component for consent UI |
| `ConsentSubmitController` | POST handler for banner form |
| `PrivacyConsentExtension` | Registers `privacy_consent()` and `privacy_effective_choices()` |
| `PrivacyMediaEmbed` | Twig component — media iframe passthrough or facade |
| `EmbedUrlResolver` | Provider URL builder for `PrivacyMediaEmbed` |

## Twig enforcement API

Registered by `PrivacyConsentExtension`. Full examples: [Enforcement](enforcement.md).

| Function / component | Returns / output | Blocks via |
|---------------------|------------------|------------|
| `privacy_consent(categoryId, subjectKey?)` | `bool` | Server omits `{% if %}` branch when `false` |
| `privacy_effective_choices(subjectKey?)` | `array<string, bool>` | Server branches on map; optional JSON for custom JS |
| `<twig:PrivacyMediaEmbed … />` | HTML (iframe or facade) | Server passthrough; client **Load** is page-local |
| `<script type="text/plain" data-privacy-category="…">` | Inert script in HTML | Client unblocker when `enforcement.client_scripts: true` |

## Error codes

| Code | Meaning |
|------|---------|
| `PRIVACY_INVALID_CATEGORY_ID` | Category id fails slug pattern |
| `PRIVACY_EMPTY_CATEGORY_LABEL` | Missing label for a category |
| `PRIVACY_DUPLICATE_CATEGORY_ID` | Duplicate id in config |
| `PRIVACY_UNKNOWN_CATEGORY_STATE` | Invalid `default_state` value |
| `PRIVACY_FORBIDDEN_ATTRIBUTE` | Forbidden attribute name (`data-cookiecategory`, `data-cc`) |
| `PRIVACY_UNSUPPORTED_ATTRIBUTE` | Unknown privacy attribute |
| `PRIVACY_UNKNOWN_PREFERENCE_CATEGORY` | Capture payload references unknown id |
| `PRIVACY_REQUIRED_CATEGORY_DISABLED` | Required category submitted as disabled |
| `PRIVACY_UNSUPPORTED_MEDIA_PROVIDER` | Invalid `PrivacyMediaEmbed` provider |
| `PRIVACY_MISSING_MEDIA_EMBED_FIELD` | Required embed field missing |
| `PRIVACY_INVALID_MEDIA_EMBED_URL` | Generic embed URL not absolute HTTPS |

## ConsentDecisionEvent fields

| Field | Type | Description |
|-------|------|-------------|
| `subjectKey` | string | Visitor or account key |
| `categoryId` | string | Changed category |
| `previousEffective` | bool | Prior effective state |
| `newEffective` | bool | New effective state |
| `source` | string | Capture source label (default `ui`) |

## Contracts

- [Strict attribute contract](contracts/strict-attribute-contract.md)
- [Publication workflow](contracts/publication-workflow.md)
