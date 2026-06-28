# Enforcement (v0.2)

v0.2 adds **Twig helpers** that gate optional integrations after `ConsentBanner` captures choices. Capture and enforcement are separate steps: the banner records per-category booleans; **your templates** decide what to render.

This is **not** a certified CMP — legal copy, vendor lists, and jurisdiction-specific flows remain the adopter's responsibility.

### Default categories

The package YAML ships four category ids. Each optional group aligns with a common integration shape:

| Category id | Gate with | Examples |
|-------------|-----------|----------|
| `required` | — (always effective) | Session, CSRF, consent cookie |
| `analytics` | `privacy_consent('analytics')` | Matomo, Plausible, error trackers |
| `marketing` | `privacy_consent('marketing')` | Ad pixels, retargeting tags |
| `media` | `PrivacyMediaEmbed` or `privacy_consent('media')` | YouTube, Vimeo, Google Maps |

Customize ids in YAML only when you also update every Twig reference and bump `storage.cookie.policy_version`.

## Twig blocking methods (overview)

Use **one primary method per integration**. Combine methods only when they solve different problems (for example `privacy_consent()` for analytics scripts and `PrivacyMediaEmbed` for YouTube).

| Method | Best for | Runs | Config | Honours consent cookie |
|--------|----------|------|--------|------------------------|
| [`privacy_consent()`](#1-privacy_consent--server-side-if) | Scripts, pixels, manual iframes, whole sections | Server (Twig render) | Categories in YAML | Yes |
| [`privacy_effective_choices()`](#2-privacy_effective_choices--full-choice-map) | Multi-category UI, custom data attributes, debugging | Server (Twig render) | Categories in YAML | Yes |
| [`PrivacyMediaEmbed`](#3-privacymediaembed--media-iframes) | YouTube, Vimeo, Maps, generic HTTPS embeds | Server + optional client **Load** | `media` category in YAML | Yes (passthrough); **Load** is page-local only |
| [`data-privacy-category` scripts](#4-data-privacy-category--opt-in-client-unblocker) | Many existing `<script>` tags with category attributes | Client (opt-in Stimulus) | `enforcement.client_scripts: true` | Yes (after unblocker scans bootstrap) |

**Does not block anything by itself:** `ConsentBanner`, `data-privacy-category` without `privacy_consent()` or the opt-in unblocker, or PHP services called only from controllers.

### Decision guide

```text
Need to gate something in Twig?
├─ External or inline <script>     → privacy_consent()  (preferred)
│                                   or data-privacy-category + client_scripts (opt-in)
├─ YouTube / Vimeo / Maps iframe   → PrivacyMediaEmbed  (preferred)
│                                   or privacy_consent('media') + manual <iframe>
├─ Arbitrary HTTPS iframe          → PrivacyMediaEmbed provider=generic
│                                   or privacy_consent('media') + manual <iframe>
├─ Image pixel / tracking beacon   → privacy_consent()
├─ Entire template region          → privacy_consent() around the block
└─ Custom JS reading choice map    → privacy_effective_choices() → data attribute / inline JSON
```

---

## 1. `privacy_consent()` — server-side `{% if %}`

**Primary pattern.** Symfony renders allowed markup only when the effective choice for a category is `true`. Denied categories produce **no HTML** for that branch — no network request, no executable script in the response.

### API

```twig
{% if privacy_consent('category-id') %}
    {# allowed #}
{% endif %}

{% if privacy_consent('category-id', subjectKey) %}
    {# allowed for subjectKey #}
{% endif %}
```

| Parameter | Required | Default | Notes |
|-----------|----------|---------|-------|
| `categoryId` | yes | — | Must exist in `symfinity_privacy_settings.categories` |
| `subjectKey` | no | `visitor` | Same semantics as `ConsentBanner` |

Unknown category ids throw `LogicException` in `dev` and return `false` in `prod`.

### Block an external script

```twig
{% if privacy_consent('analytics') %}
    <script src="https://example.com/analytics.js" defer></script>
{% endif %}
```

The script tag is **absent from the HTML** when analytics is denied — browsers cannot fetch it.

### Block an inline script

```twig
{% if privacy_consent('analytics') %}
    <script>
        window.myAnalytics = { track: function () { /* … */ } };
    </script>
{% endif %}
```

Prefer external assets + `defer` when possible; inline scripts complicate CSP.

### Block a manual iframe (any category)

Use when you do not need the media facade / **Load** UX:

```twig
{% if privacy_consent('media') %}
    <iframe
        src="https://www.youtube-nocookie.com/embed/{{ videoId }}"
        title="{{ title }}"
        loading="lazy"
        allowfullscreen
    ></iframe>
{% else %}
    <p>This embed requires media consent.</p>
    <button type="button" onclick="document.dispatchEvent(new CustomEvent('privacy-settings:open-preferences', { bubbles: true, detail: { focusCategory: 'media' } }))">
        Cookie settings
    </button>
{% endif %}
```

For YouTube/Vimeo/Maps with facade + **Load**, use [`PrivacyMediaEmbed`](#3-privacymediaembed--media-iframes) instead.

### Block an image pixel or beacon

```twig
{% if privacy_consent('marketing') %}
    <img src="https://tracker.example/pixel.gif" alt="" width="1" height="1" hidden>
{% endif %}
```

### Block a whole section

```twig
{% if privacy_consent('marketing') %}
    <aside class="promo-strip">
        {# newsletter signup, partner logos, etc. #}
    </aside>
{% endif %}
```

### Logged-in subject

Use the same key you pass to `ConsentBanner`:

```twig
{% set subject = app.user ? app.user.userIdentifier : null %}

{% if privacy_consent('analytics', subject) %}
    {# account-scoped analytics #}
{% endif %}
```

Anonymous visitors: omit `subjectKey` (defaults to `visitor`).

### With `else` — show neutral placeholder

```twig
{% if privacy_consent('media') %}
    {{ include('partials/video_player.html.twig') }}
{% else %}
    <div class="embed-placeholder">
        <p>Video blocked until you allow media cookies.</p>
    </div>
{% endif %}
```

---

## 2. `privacy_effective_choices()` — full choice map

Returns `array<string, bool>` for a subject — the same map as `PreferenceRestoreService::effectiveChoices()`. Use when one template needs **multiple categories** without repeated restore calls, or when passing choices into markup for custom JavaScript.

### API

```twig
{% set choices = privacy_effective_choices() %}
{% set choices = privacy_effective_choices(app.user ? app.user.userIdentifier : null) %}
```

### Branch on several categories

```twig
{% set choices = privacy_effective_choices() %}

{% if choices.analytics %}
    <script src="/assets/analytics.js" defer></script>
{% endif %}

{% if choices.marketing %}
    <script src="/assets/marketing.js" defer></script>
{% endif %}
```

Equivalent to separate `privacy_consent()` calls; pick whichever reads clearer.

### Expose choices to client-side code (custom runtimes)

HttpOnly consent cookies are **not** readable from JavaScript. When you own a Stimulus controller or tag-manager bootstrap, inject server-rendered JSON:

```twig
{% set choices = privacy_effective_choices() %}
<div
    data-controller="app-analytics"
    data-app-analytics-choices-value="{{ choices|json_encode|e('html_attr') }}"
></div>
```

`ConsentBanner` also emits `<script type="application/json" id="privacy-settings-effective-choices">…</script>` for the bundle unblocker — you may read that element from custom JS, but prefer explicit `data-*` attributes for app code.

### Debug / support tooling

```twig
{# dev-only panel — remove in production templates #}
<pre>{{ privacy_effective_choices()|json_encode(constant('JSON_PRETTY_PRINT')) }}</pre>
```

---

## 3. `PrivacyMediaEmbed` — media iframes

Specialized component for third-party **iframe** embeds tied to the **`media`** category. Handles passthrough, facade, page-local **Load**, and **Cookie settings** reopen.

### Basic usage

```twig
<twig:PrivacyMediaEmbed
    provider="youtube"
    videoId="dQw4w9WgXcQ"
    title="Demo video"
/>
```

### Props

| Prop | Notes |
|------|-------|
| `provider` | `youtube`, `vimeo`, `google_maps`, or `generic` |
| `videoId` | YouTube/Vimeo id |
| `mapQuery` | Google Maps search query |
| `embedUrl` | Required for `generic` — absolute `https` only |
| `title` | Accessible iframe label (**required**) |
| `aspectRatio` | CSS aspect-ratio value; default `16 / 9` (`16/9` accepted) |
| `embedId` | Optional stable id for client unlock tracking |
| `subjectKey` | Optional; defaults to `visitor` |

### Provider examples

```twig
{# YouTube (privacy-enhanced host) #}
<twig:PrivacyMediaEmbed provider="youtube" videoId="dQw4w9WgXcQ" title="Demo" />

{# Vimeo #}
<twig:PrivacyMediaEmbed provider="vimeo" videoId="76979871" title="Demo" />

{# Google Maps #}
<twig:PrivacyMediaEmbed provider="google_maps" mapQuery="Berlin, Germany" title="Office location" aspectRatio="4/3" />

{# Generic HTTPS embed #}
<twig:PrivacyMediaEmbed
    provider="generic"
    embedUrl="https://example.com/widget/embed"
    title="Third-party widget"
/>
```

### Render behaviour

| `privacy_consent('media')` | Output |
|----------------------------|--------|
| `true` | iframe with real `src` immediately (**passthrough**) |
| `false` | Facade placeholder — **no** third-party request until visitor clicks **Load** |

**Load** unlocks **this embed only** for the **current page view**. Reload clears unlock state. **Load does not** update the consent cookie or enable media site-wide.

### Cookie settings reopen

The facade dispatches `privacy-settings:open-preferences` on `document` with `{ focusCategory: 'media' }`. `ConsentBanner` listens and opens manage-settings even when a stored decision exists.

### vs manual `privacy_consent('media')`

| | `privacy_consent()` + manual iframe | `PrivacyMediaEmbed` |
|---|-------------------------------------|---------------------|
| Passthrough when allowed | Yes | Yes |
| Facade + **Load** without consent | You build it | Built-in |
| **Cookie settings** link | You wire reopen event | Built-in |
| Provider URL validation | Your responsibility | `EmbedUrlResolver` |

---

## 4. `data-privacy-category` — opt-in client unblocker

Declarative markup for scripts that must stay in the template but start **inert**. Requires explicit opt-in — default is **off**.

### Enable

```yaml
# config/packages/symfinity_privacy_settings.yaml
symfinity_privacy_settings:
    enforcement:
        client_scripts: true
```

When `false`, scripts tagged below stay `type="text/plain"` forever.

### Block an external script (declarative)

```twig
<script type="text/plain" data-privacy-category="analytics" src="/assets/analytics.js"></script>
```

### Block an inline script (declarative)

```twig
<script type="text/plain" data-privacy-category="analytics">
    myAnalytics.init({ site: 'example' });
</script>
```

When the category becomes allowed, the unblocker clones the node to `type="text/javascript"` and executes it. When `ConsentBanner` is present with `client_scripts: true`, the unblocker controller attaches automatically.

### How choices reach JavaScript

The consent cookie is HttpOnly. The unblocker reads `#privacy-settings-effective-choices` JSON rendered by `ConsentBanner` (or `data-privacy-settings-bundle--script-unblocker-choices-value`).

After a banner save, **reload the page** or listen for `privacy-settings:consent-updated` on `document` to re-scan.

### When to prefer `privacy_consent()`

| Prefer `privacy_consent()` | Prefer `data-privacy-category` |
|----------------------------|--------------------------------|
| New templates | Migrating many existing script tags |
| Strict “no HTML when denied” | Tag manager dumps you cannot wrap in Twig `if` |
| Simpler mental model | Need scripts in DOM for non-JS crawlers (rare) |

**Warning:** opt-in only. Do not enable `client_scripts` unless you understand the client runtime.

---

## Markup validation (`data-privacy-category`)

The bundle validates **attribute names** in templates (see [strict attribute contract](contracts/strict-attribute-contract.md)):

| Attribute | Role |
|-----------|------|
| `data-privacy-category` | **Canonical** — declare which category owns an element |
| `data-cookiecategory`, `data-cc` | **Forbidden** — never supported; use `data-privacy-category` |

Validation does **not** block rendering. Pair declarations with [`privacy_consent()`](#1-privacy_consent--server-side-if) or the [client unblocker](#4-data-privacy-category--opt-in-client-unblocker).

`MarkupDeclarationScanner` helps CI find declarations; enforcement still requires one of the methods above.

---

## Subject key rules (all methods)

| Visitor | `subjectKey` | Notes |
|---------|--------------|-------|
| Anonymous | omit or `'visitor'` | Scoped by signed consent cookie |
| Logged-in | `app.user.userIdentifier` (or your stable id) | Must match `ConsentBanner` and capture route |

Do **not** pass `app.session.id` as `subjectKey`.

---

## Checklist

- [ ] Every category id used in Twig exists in `symfinity_privacy_settings.categories`
- [ ] Each optional integration uses exactly one blocking method
- [ ] `media` category configured when using `PrivacyMediaEmbed` or `privacy_consent('media')`
- [ ] `subjectKey` stable for anonymous and explicit for logged-in users
- [ ] `client_scripts` left `false` unless the unblocker is intentional
- [ ] Bump `storage.cookie.policy_version` when category ids or semantics change

---

## See also

- [Usage — Applying consent choices](usage.md#applying-consent-choices)
- [Configuration — enforcement.client_scripts](configuration.md)
- [Quickstart](quickstart.md)
- [Reference — Twig API](reference.md#twig-enforcement-api)
