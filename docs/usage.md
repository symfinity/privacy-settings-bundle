# Usage

## Services

| Service | Role |
|---------|------|
| `CategoryModelNormalizer` | Validates config arrays into `PrivacyCategory` value objects |
| `PreferenceCaptureService` | Persists visitor choices and publishes change events |
| `PreferenceRestoreService` | Resolves effective choices for a subject key |
| `PreferenceStoreInterface` | Storage port (default: in-memory) |
| `MarkupDeclarationScanner` | Finds privacy attributes in template markup |
| `StrictAttributeValidator` | Rejects forbidden legacy aliases |

## Consent UI

### ConsentBanner component

Renders a fixed banner with one row per configured category, composing ux-blocks-form `Form`, `Field`, `Switch`/`Checkbox`, `FormActions`, and core `Button`.

```twig
{{ component('ConsentBanner', {
    subjectKey: 'visitor-123',
    position: 'bottom'
}) }}
```

| Prop | Default | Description |
|------|---------|-------------|
| `subjectKey` | `visitor` | Key passed to capture/restore |
| `position` | `bottom` | `bottom` or `sheet` layout variant |

Submit posts to `/_privacy/consent/{subjectKey}` (bundle route) and calls `PreferenceCaptureService`.

### Kernel layout embed

When `symfinity/ui-kernel` is installed, place the banner inside your themed base layout so `[data-theme]` tokens apply:

```twig
<body data-theme="default">
    {{ component('ConsentBanner', { subjectKey: app.session.id }) }}
    {% block content %}{% endblock %}
</body>
```

Import form-tier CSS via `ux-blocks-form` and glue CSS from this package (see [Quickstart](quickstart.md)).

### Headless opt-out

Do not render `ConsentBanner`. Inject capture/restore services in controllers or API endpoints and persist via your own `PreferenceStoreInterface` implementation.

## Custom store

```yaml
# config/services.yaml
services:
    Symfinity\PrivacySettingsBundle\Consent\PreferenceStoreInterface:
        class: App\Privacy\DatabasePreferenceStore
```

## Events

Implement a listener for `Symfinity\PrivacySettingsBundle\Event\ConsentDecisionEvent` or use `ConsentDecisionEventPublisher` if you replace the default wiring.

## Template validation

Use `MarkupDeclarationScanner` and `TemplateAttributeContract` in CI or custom commands to fail builds when templates use forbidden attributes.

## Pitfalls

- Forbidden attributes (`data-cookiecategory`, `data-cc`) always fail validation — see [strict-attribute-migration](strict-attribute-migration.md)
- Unknown preference keys in capture payloads raise `PRIVACY_UNKNOWN_PREFERENCE_CATEGORY`
- Required categories cannot be disabled — `PRIVACY_REQUIRED_CATEGORY_DISABLED`
- Default in-memory store does not survive requests — replace for production

## See also

- [Configuration](configuration.md)
- [Troubleshooting](troubleshooting.md)
- [Reference](reference.md)
