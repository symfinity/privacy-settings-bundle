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
| `ConsentDecisionEvent` | Emitted when effective choices change |
| `ConsentDecisionEventPublisher` | Compares previous vs new effective state |
| `CategoryModelNormalizer` | Config array to domain list |
| `StrictAttributeValidator` | Canonical attribute enforcement |
| `MarkupDeclarationScanner` | Template scanning helper |
| `ConsentBanner` | Twig component for consent UI |
| `ConsentSubmitController` | POST handler for banner form |

## Error codes

| Code | Meaning |
|------|---------|
| `PRIVACY_INVALID_CATEGORY_ID` | Category id fails slug pattern |
| `PRIVACY_EMPTY_CATEGORY_LABEL` | Missing label for a category |
| `PRIVACY_DUPLICATE_CATEGORY_ID` | Duplicate id in config |
| `PRIVACY_UNKNOWN_CATEGORY_STATE` | Invalid `default_state` value |
| `PRIVACY_FORBIDDEN_ATTRIBUTE` | Legacy alias attribute present |
| `PRIVACY_UNSUPPORTED_ATTRIBUTE` | Unknown privacy attribute |
| `PRIVACY_UNKNOWN_PREFERENCE_CATEGORY` | Capture payload references unknown id |
| `PRIVACY_REQUIRED_CATEGORY_DISABLED` | Required category submitted as disabled |

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
