# Security Policy

## Supported Versions

| Version | Supported |
|---------|-----------|
| 0.1.x   | Yes       |

## Reporting a Vulnerability

If you discover a security vulnerability, **do not** open a public issue. Email **dev@symfinity.net** with:

- Type of vulnerability
- Full paths of source file(s) related to the issue
- The location of the affected code (tag, branch, commit, or URL)
- Step-by-step reproduction instructions
- Proof-of-concept or exploit code (if possible)
- Impact and plausible attack scenario

We aim to acknowledge within 48 hours and provide a detailed response within 7 days.

## Security best practices

1. Keep Symfony, ux-blocks siblings, and this bundle updated in your application
2. Protect consent submit routes with CSRF when exposing forms on authenticated pages
3. Use a persistent `PreferenceStoreInterface` implementation for production — the default in-memory store is for development only

## Security contact

**dev@symfinity.net**
