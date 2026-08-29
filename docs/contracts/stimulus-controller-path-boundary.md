# Stimulus controller path boundary

## Problem

Symfony merges `prependExtensionConfig('stimulus', ['controller_paths' => [...]])` as a **replacement** of the
`controller_paths` list, not a merge. A bundle that prepends only its own directory **drops** the application
default (`%kernel.project_dir%/assets/controllers`) and silently unregisters every app Stimulus controller.

## MUST (privacy-settings-bundle)

| MUST | MUST NOT |
|------|----------|
| Register bundle controllers via `AppendStimulusControllerPathPass` in `PrivacySettingsBundle::build()` | `prependExtensionConfig('stimulus', …)` with `controller_paths` |
| Append the bundle path to `stimulus.asset_mapper.controllers_map_generator` argument `2` | Replace or narrow `controller_paths` to a single bundle directory |

## Consumer apps with custom controllers

If the app ships its own Stimulus controllers (homepage scroll story, product UI, etc.), keep an explicit app path in
`config/packages/stimulus.yaml`:

```yaml
stimulus:
    controller_paths:
        - '%kernel.project_dir%/assets/controllers'
        - '%kernel.project_dir%/vendor/symfinity/privacy-settings-bundle/assets/controllers'
```

The compiler pass still appends the bundle path when the vendor copy is used; listing both paths is defensive and
documents the contract for maintainers.

## Regression gates

- `tests/Integration/StimulusControllerPathsIntegrationTest.php` — app + bundle paths coexist after container compile
- `tests/Unit/DependencyInjection/Compiler/AppendStimulusControllerPathPassTest.php` — append-only semantics

Other Symfinity bundles with Stimulus controllers **SHOULD** copy this compiler-pass pattern instead of prepending
`controller_paths`.
