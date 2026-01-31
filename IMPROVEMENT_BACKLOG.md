# ConvoChat - Improvement Backlog

**Generated:** 2026-01-29

| ID | Title | Priority | Effort | Risk | Area | Reason | Acceptance Criteria |
|----|-------|----------|--------|------|------|--------|---------------------|
| B-01 | Fix `sendSms()` secret override vulnerability | P0 | S | B | Security | `array_merge(['secret'=>...], $params)` lets user params override API key | Secret key placed AFTER user params in merge, or filtered from $params |
| B-02 | Fix `SendBulkSmsJob` re-dispatch loop | P0 | S | M | Code Quality | Job catches per-recipient exceptions and re-dispatches new jobs, bypassing retry mechanism and risking infinite loop | Remove manual re-dispatch; rely on Laravel's built-in `$tries` and `$backoff` |
| B-03 | Update dev dependencies for CVEs | P0 | S | B | Security | 3 CVEs: PHPUnit (high), symfony/http-foundation (high), symfony/process (medium) | `composer audit` returns 0 vulnerabilities |
| B-04 | Extract `BaseConvoChatService` abstract class | P1 | M | M | Architecture | ~240 lines of identical code across 5 services (constructor, makeRequest, validateConfiguration, validateRequiredParams) | Single base class; all 5 services extend it; no duplicated methods; tests pass |
| B-05 | Add tests for OTP, USSD, Contacts services | P1 | M | B | Testing | 3 of 5 services have zero test coverage | Each service has unit tests covering: instantiation, required param validation, successful request, error handling |
| B-06 | Add composer.json scripts | P1 | S | B | DX | No `composer test`, `composer analyse`, `composer format` aliases | Scripts section added; all 3 commands work |
| B-07 | Fix PHPStan memory limit in CI | P1 | S | B | CI/CD | PHPStan crashes at 128M default memory | Add `--memory-limit=512M` to CI workflow PHPStan step |
| B-08 | Add Dependabot config | P1 | S | B | CI/CD | No automated dependency update monitoring; 3 CVEs went unnoticed | `.github/dependabot.yml` configured for composer |
| B-09 | Upgrade `actions/cache@v3` to `@v4` | P2 | S | B | CI/CD | v3 is outdated; v4 has better performance | CI workflow uses actions/cache@v4 |
| B-10 | Create domain-specific exceptions | P2 | M | M | Code Quality | All services throw generic `\Exception`; hard to catch specific errors | `ConvoChatApiException`, `ConvoChatValidationException`, `ConvoChatConnectionException` |
| B-11 | Reduce PHPStan error suppressions | P2 | M | B | Code Quality | 9 patterns suppressed + `reportUnmatchedIgnoredErrors: false` masks issues | Reduce to ≤3 necessary suppressions; enable `reportUnmatchedIgnoredErrors: true` |
| B-12 | Type anonymous class in ServiceProvider | P2 | S | M | Architecture | Anonymous class lacks typed properties; causes PHPStan suppressions | Extract to named class `ConvoChatManager` with proper typing |
| B-13 | Integrate `ConvoChatCache` into services | P2 | M | M | Performance | Cache class exists but is never used; read endpoints could benefit | Opt-in caching for GET endpoints via config flag; cache warming optional |
| B-14 | Integrate `RateLimiter` into services | P2 | S | M | Performance | RateLimiter class exists but is never used | Optional rate limiting on send methods via config |
| B-15 | Add `.env.example` | P2 | S | B | DX | No template for required env vars | File lists all CONVOCHAT_* variables with placeholder values |
| B-16 | Add code coverage to CI | P2 | S | B | CI/CD | No coverage reporting; hard to track regression | Coverage report generated; badge added to README |
| B-17 | Fix `ConvoChatCache::invalidateType()` flush fallback | P2 | S | M | Code Quality | Falls back to `Cache::flush()` which clears ALL application cache | Use tagged cache or iterate known keys instead |
| B-18 | Remove `RateLimiter::retriesLeft()` alias | P2 | S | B | Code Quality | Exact duplicate of `remaining()` | Remove alias method |
| B-19 | Add service interfaces/contracts | P2 | M | M | Architecture | No interfaces; harder to mock/fake in consumer apps | Interface per service; bind in ServiceProvider |
| B-20 | Include Console in PHPStan analysis | P2 | S | B | Code Quality | `src/Console` excluded from static analysis | Remove exclusion; fix any errors found |

### Priority Legend
- **P0**: Fix immediately (security, data loss risk)
- **P1**: Fix this sprint (high impact, moderate effort)
- **P2**: Plan for next sprint (improvements, hardening)

### Effort Legend
- **S**: Small (< 1 hour)
- **M**: Medium (1-4 hours)
- **L**: Large (> 4 hours)

### Risk Legend
- **B**: Bajo (low - unlikely to break anything)
- **M**: Medio (medium - needs testing)
- **A**: Alto (high - could break consumers)
