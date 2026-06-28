# Usage

## Services

| Service | Role |
|---------|------|
| `CategoryModelNormalizer` | Validates config arrays into `PrivacyCategory` value objects |
| `PreferenceCaptureService` | Persists visitor choices and publishes change events |
| `PreferenceRestoreService` | Resolves effective choices for a subject key |
| `PreferenceStoreInterface` | Storage port (default: signed cookie store) |
| `MarkupDeclarationScanner` | Finds privacy attributes in template markup |
| `StrictAttributeValidator` | Rejects forbidden attribute names (`data-cookiecategory`, `data-cc`) |

## Consent UI

### ConsentBanner component

Renders a modal consent surface (backdrop + two-step flow: quick accept/reject, then optional per-category settings) composing ux-blocks-form `Form`, `Field`, `Switch`/`Checkbox`, `FormActions`, and core `Button`.

```twig
{{ component('ConsentBanner', {
    subjectKey: 'visitor-123',
    position: 'bottom'
}) }}
```

| Prop | Default | Description |
|------|---------|-------------|
| `subjectKey` | `visitor` | Logical key for capture/restore; anonymous defaults use the signed consent cookie — do not pass `app.session.id` |
| `position` | `modal` | `modal` (backdrop dialog), `bottom`, or `sheet` layout variant |

Submit posts to `/_privacy/consent/{subjectKey}` (bundle route) and calls `PreferenceCaptureService`.

### Kernel layout embed

When `symfinity/ui-kernel` is installed, place the banner inside your themed base layout so `[data-theme]` tokens apply:

```twig
<body data-theme="default">
    {{ component('ConsentBanner') }}
    {% block content %}{% endblock %}
</body>
```

Omit `subjectKey` for anonymous visitors (`visitor` + consent cookie). Pass an explicit key for logged-in accounts (for example `app.user.userIdentifier`). Preferences live in the signed cookie `symfinity_privacy_consent` (default) until you replace `PreferenceStoreInterface`.

Import form-tier CSS via `ux-blocks-form` Flex recipe. `ConsentBanner` loads package glue CSS automatically — no host `importmap.php` entry required.

### Headless opt-out

Do not render `ConsentBanner`. Inject capture/restore services in controllers or API endpoints and persist via your own `PreferenceStoreInterface` implementation.

## Applying consent choices

`ConsentBanner` and `PreferenceCaptureService` **capture and persist** per-category booleans. **v0.2** ships enforcement helpers — see **[Enforcement](enforcement.md)** for `privacy_consent()`, `PrivacyMediaEmbed`, and the opt-in client script unblocker.

### What the bundle ships

| Ships (v0.2) | Does not ship |
|--------------|---------------|
| Category model + YAML config | Built-in Google Analytics / Matomo / GTM integration |
| `PreferenceCaptureService` / `PreferenceRestoreService` | Certified CMP / IAB TCF |
| Signed cookie store (default) or `PreferenceStoreInterface` port | Automatic vendor list management |
| `ConsentDecisionEvent` per changed category | Third-party SDK bundles |
| `privacy_consent()` Twig function | |
| `PrivacyMediaEmbed` component (facade + Load) | |
| Opt-in `enforcement.client_scripts` unblocker | |

### Default categories

The shipped YAML defines four ids. Map each to enforcement in your templates — see [Configuration](configuration.md) and [Enforcement](enforcement.md).

| Category | Default | ConsentBanner | Typical Twig gate |
|----------|---------|---------------|-------------------|
| `required` | always on | Locked checkbox | Not gated — essential stack |
| `analytics` | off until opt-in | Optional switch | `privacy_consent('analytics')` |
| `marketing` | off until opt-in | Optional switch | `privacy_consent('marketing')` |
| `media` | off until opt-in | Optional switch | `PrivacyMediaEmbed` or `privacy_consent('media')` |

### What happens when a visitor decides

Submit posts to `/_privacy/consent/{subjectKey}`. `ConsentSubmitController` builds a choice map and calls `PreferenceCaptureService`. Default storage writes a signed cookie (`symfinity_privacy_consent` unless configured otherwise).

Example category `analytics` with `default_state: disabled`:

| Visitor action | Effective `analytics` after save |
|----------------|--------------------------------|
| **Accept all** | `true` |
| **Reject optional** | `false` (`required` categories stay `true`) |
| **Manage settings** → Analytics on → **Save** | `true` |
| **Manage settings** → Analytics off → **Save** | `false` |

After a stored decision exists, `ConsentBanner` hides the quick panel but keeps a reopenable shell for **Cookie settings** via `privacy-settings:open-preferences`.

### Wire enforcement in your application

**Primary path (v0.2):** [Enforcement](enforcement.md) documents **every Twig blocking method** — comparison table, examples for scripts, iframes, pixels, and sections.

| Method | Twig entry point |
|--------|------------------|
| Server-side `{% if %}` | `privacy_consent('analytics')` |
| Full choice map | `privacy_effective_choices()` |
| Media iframes (facade + Load) | `<twig:PrivacyMediaEmbed … />` |
| Declarative scripts (opt-in) | `<script type="text/plain" data-privacy-category="…">` + `enforcement.client_scripts: true` |

Pick one method per integration. PHP-only paths (`ConsentDecisionEvent`, controller injection) complement Twig — see [Events](#events).

**React on change (`ConsentDecisionEvent`)** — enable or tear down client-side loaders when consent changes without a full reload:

```php
#[AsEventListener]
public function __invoke(ConsentDecisionEvent $event): void
{
    if ('analytics' !== $event->categoryId) {
        return;
    }

    if ($event->newState) {
        // Enable analytics (inject script, call tag manager consent API, etc.)
    } else {
        // Disable analytics (remove script, revoke consent API, etc.)
    }
}
```

### Checklist before go-live

- [ ] Every configured category id used in templates or listeners exists in `symfinity_privacy_settings.categories`
- [ ] Each optional integration uses one [Twig blocking method](enforcement.md) and/or `ConsentDecisionEvent`
- [ ] `subjectKey` is stable for anonymous (`visitor`) and explicit for logged-in users
- [ ] Bump `storage.cookie.policy_version` when category ids or semantics change

## Custom store

```yaml
# config/services.yaml
services:
    Symfinity\PrivacySettingsBundle\Consent\PreferenceStoreInterface:
        class: App\Privacy\DatabasePreferenceStore
```

## Events

Implement a listener for `Symfinity\PrivacySettingsBundle\Event\ConsentDecisionEvent` to react when a category toggles. See [Applying consent choices](#applying-consent-choices).

## Template validation

Use `MarkupDeclarationScanner` and `TemplateAttributeContract` in **your** CI or custom commands to fail builds when templates use forbidden attributes — neither runs automatically on page render.

## Pitfalls

- **Consent UI alone does not block or enable trackers** — use [Enforcement](enforcement.md) or wire restore/events in the consumer app
- Only `data-privacy-category` is valid; `data-cookiecategory` and `data-cc` fail when you call `StrictAttributeValidator` / `TemplateAttributeContract` — see [strict attribute policy](strict-attribute-policy.md)
- Unknown preference keys in capture payloads raise `PRIVACY_UNKNOWN_PREFERENCE_CATEGORY`
- Required categories cannot be disabled — `PRIVACY_REQUIRED_CATEGORY_DISABLED`
- Default cookie store persists consent for `storage.cookie.lifetime_days` (minimum **180**). Replace for production audit needs via `PreferenceStoreInterface`.

## See also

- [Enforcement](enforcement.md)
- [Configuration](configuration.md)
- [Troubleshooting](troubleshooting.md)
- [Reference](reference.md)
