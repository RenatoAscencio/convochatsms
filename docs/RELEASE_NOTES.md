# Release Notes — v4.0.0

**Date:** 2026-01-31

## Highlights

This release focuses on security, code quality, and developer experience. No new API endpoints were added; the public Facade and service method signatures remain the same for standard usage.

### Security Fixes

- **API secret override** — A vulnerability allowed user-supplied parameters to overwrite the API key in requests. Fixed across all 5 services.
- **Infinite job loop** — `SendBulkSmsJob` could spawn unlimited retry jobs. Now relies on Laravel's built-in retry mechanism.
- **Dev dependency CVEs** — 3 vulnerabilities resolved.

### Architecture

- Extracted `BaseConvoChatService` abstract class, removing ~370 lines of duplicated code across SMS, WhatsApp, Contacts, OTP, and USSD services.
- Replaced anonymous class with typed `ConvoChatManager`.

### Quality

- PHPStan level 8 with only 1 justified suppression (down from 9).
- Console command now included in static analysis.
- Test suite expanded from 41 to 64 tests.
- Added consumer smoke test.

### Developer Experience

- `.env.example` with all configuration variables.
- Composer scripts: `test`, `analyse`, `format`, `quality`.
- Dependabot for automated dependency updates.
- Code coverage CI job.
- Documentation: `CONFIGURATION.md`, `TESTING.md`, `UPGRADE.md`.

## Breaking Changes

See [docs/UPGRADE.md](UPGRADE.md) for migration guide.

- Removed unused `ConvoChatCache` and `RateLimiter` classes
- Service constructors unified via `BaseConvoChatService`
- `validateRequiredParams` no longer rejects `0`/`"0"` values
