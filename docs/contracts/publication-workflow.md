# Contract: privacy preference publication workflow

**Package:** `symfinity/privacy-settings-bundle`

## Goal

Define deterministic behavior from visitor action to persisted preference state and runtime decision evaluation.

## Workflow

1. Visitor opens privacy settings surface.
2. Bundle presents configured categories and current effective state.
3. Visitor confirms choices.
4. Bundle validates payload against current category model.
5. Bundle publishes normalized preference state to persistence port.
6. Bundle emits consent decision events for audit/verification.
7. Subsequent requests restore state and enforce category-bound decisions.

## Required invariants

- Required categories cannot be disabled by visitor action.
- Unknown categories in incoming payload are rejected.
- Unknown categories in restored stored state are treated with safe non-activation behavior.
- Every accepted change emits a deterministic decision event.

## Failure behavior

| Failure | Expected behavior |
|--------|-------------------|
| Invalid payload shape | Reject write, keep previous state, emit validation error |
| Forbidden legacy attribute declaration detected | Reject declaration path and emit strict attribute error |
| Persistence adapter unavailable | Fail safely, keep runtime non-activating for optional categories |
| Divergence between stored state and current config | Ignore unknown categories, emit divergence event, continue safely |

## Out of scope

- UI styling/theming strategy
- Storage engine implementation detail
- Multi-tenant policy orchestration beyond category model semantics
