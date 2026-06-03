# symfinity/privacy-settings-bundle

Strict privacy category settings bundle for Symfony 6.4+ applications.

## Guarantees

- Canonical attribute only: `data-privacy-category`
- Forbidden aliases: `data-cookiecategory` and `data-cc`
- Deterministic validation errors for invalid category/config/attribute declarations

Normative contracts: [docs/contracts/](docs/contracts/) · [quickstart](docs/quickstart.md)

## Symfony 6.4 quickstart

```yaml
# config/packages/symfinity_privacy_settings.yaml
symfinity_privacy_settings:
  categories:
    - id: required
      label: Required
      default_state: required
      description: Required services and security primitives
    - id: analytics
      label: Analytics
      default_state: disabled
      description: Optional usage and performance analytics
    - id: marketing
      label: Marketing
      default_state: disabled
      description: Optional ad personalization
```

Render only canonical declarations:

```twig
<script data-privacy-category="analytics" src="/assets/analytics.js"></script>
```

Forbidden legacy declarations fail validation:

- `data-cookiecategory`
- `data-cc`
