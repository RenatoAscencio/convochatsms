# ConvoChat Laravel Package - Audit Report

**Date:** 2026-01-29
**Auditor:** Automated (Claude Code)
**Scope:** Full codebase audit - Architecture, Code Quality, Performance, Security, CI/CD, DX

---

## 1. Executive Summary

- **41 tests pass, 0 failures** — baseline is green. PHPStan level 8 clean. CS Fixer clean.
- **Critical: 3 CVEs** in dev dependencies (PHPUnit high, symfony/http-foundation high, symfony/process medium).
- **Major architectural debt:** `makeRequest`, `validateConfiguration`, and `validateRequiredParams` are copy-pasted identically across all 5 service classes (~60 lines x 5 = ~300 duplicated lines).
- **Dead code:** `ConvoChatCache` and `RateLimiter` classes exist but are **never used** by any service or referenced anywhere in runtime code.
- **Missing test coverage:** OTP, USSD, and Contacts services have **zero tests** (3 of 5 services untested).
- **Semantic HTTP issue:** All delete/destructive endpoints use `GET` method — this is an upstream API design issue but worth documenting.
- **Job re-dispatch loop risk:** `SendBulkSmsJob` catches exceptions per-recipient and re-dispatches itself, potentially creating infinite job spawning.
- **PHPStan ignores** ~9 error patterns instead of fixing the underlying type issues (anonymous class typing, generics).
- **CI uses `actions/cache@v3`** (v4 available), and PHPStan runs without `--memory-limit` (fails locally at 128M).
- **Top 5 priorities:** (1) Extract base service class, (2) Add tests for untested services, (3) Fix dependency CVEs, (4) Fix job re-dispatch logic, (5) Clean PHPStan suppressions.

---

## 2. System Map

```
┌─────────────────────────────────────────────────────────┐
│                    Laravel Application                   │
├─────────────────────────────────────────────────────────┤
│  ConvoChat Facade ──► ConvoChatServiceProvider          │
│       │                  (singletons registry)          │
│       ├── sms()      ──► ConvoChatSmsService            │
│       ├── whatsapp() ──► ConvoChatWhatsAppService       │
│       ├── contacts() ──► ConvoChatContactsService       │
│       ├── otp()      ──► ConvoChatOtpService            │
│       └── ussd()     ──► ConvoChatUssdService           │
│                                                         │
│  Each service:                                          │
│    - Constructs Guzzle Client                           │
│    - Reads config (api_key, base_url, timeout)          │
│    - Validates configuration                            │
│    - makeRequest() → Guzzle HTTP → ConvoChat API        │
│    - Logs success/error                                 │
│                                                         │
│  Support (UNUSED):                                      │
│    ConvoChatCache ──► Laravel Cache facade               │
│    RateLimiter    ──► Laravel Cache facade                │
│                                                         │
│  Queue:                                                 │
│    SendBulkSmsJob ──► ConvoChatSmsService               │
│                                                         │
│  Console:                                               │
│    TestConvoChatCommand (artisan convochat:test)         │
├─────────────────────────────────────────────────────────┤
│  External: ConvoChat API (https://sms.convo.chat/api)   │
└─────────────────────────────────────────────────────────┘
```

---

## 3. Audit by Domain

### 3.1 Architecture & Modularity

| Finding | Severity | File(s) |
|---------|----------|---------|
| **5 services duplicate identical code** (~60 lines each): `makeRequest()`, `validateConfiguration()`, `validateRequiredParams()`, constructor logic, and 4 identical properties | High | All `src/Services/*.php` |
| Anonymous class in ServiceProvider for facade accessor has no typed properties (causes PHPStan suppressions) | Medium | `src/ConvoChatServiceProvider.php:40-72` |
| `ConvoChatCache` and `RateLimiter` are defined but never integrated into any service | Medium | `src/Cache/`, `src/Security/` |
| No interface/contract for services — makes testing with fakes harder | Low | `src/Services/` |
| Config file lacks sections for OTP, USSD, Contacts services | Low | `config/convochat.php` |

**Recommendation:** Extract a `BaseConvoChatService` abstract class containing the shared constructor, properties, `makeRequest()`, `validateConfiguration()`, and `validateRequiredParams()`. This eliminates ~240 duplicated lines and centralizes HTTP behavior.

### 3.2 Code Quality

| Finding | Severity | Location |
|---------|----------|----------|
| `empty()` used for validation — fails for `0`, `"0"`, `false` which could be valid in some contexts | Low | All services `validateRequiredParams()` |
| Generic `\Exception` thrown instead of domain-specific exceptions | Medium | All `makeRequest()` methods |
| `sendSms()` merges user params with `secret` key — user can override `secret` via `$params` | Medium | `ConvoChatSmsService:56-59` |
| `SendBulkSmsJob` re-dispatches new jobs on failure, bypassing the built-in retry mechanism | High | `src/Jobs/SendBulkSmsJob.php:64-66` |
| `ConvoChatCache::invalidateType()` falls back to `Cache::flush()` which clears ALL cache | Medium | `src/Cache/ConvoChatCache.php:41` |
| PHPStan ignores 9 error patterns with `reportUnmatchedIgnoredErrors: false` — masks real issues | Medium | `phpstan.neon` |
| `retriesLeft()` in RateLimiter is just an alias of `remaining()` — unnecessary | Low | `src/Security/RateLimiter.php:44-47` |

### 3.3 Performance

| Finding | Severity | Notes |
|---------|----------|-------|
| Each service creates its own `Guzzle\Client` instance — no connection pooling | Low | Minor for a package; consumers can inject shared client |
| `SendBulkSmsJob` sends messages sequentially with `usleep()` — no batching | Medium | Could use the bulk SMS endpoint instead |
| `ConvoChatCache` is defined but never used — no caching of repeated API calls | Medium | Devices/accounts/rates could benefit from caching |
| No connection keep-alive or retry middleware on Guzzle client | Low | Single requests; acceptable for now |

### 3.4 Security

| Finding | Severity | Details |
|---------|----------|---------|
| **3 CVEs in dependencies** | High | PHPUnit (CVE-2026-24765, high), symfony/http-foundation (CVE-2025-64500, high), symfony/process (CVE-2026-24739, medium) |
| API key passed in JSON body (`secret` field) rather than auth header | Medium | Upstream API design; body params may be logged by proxies |
| `sendSms()` allows user to override `secret` via `$params` array merge | Medium | `array_merge(['secret' => $this->apiKey], $params)` — user params override secret |
| No `.env.example` file — developers might commit real keys | Low | Good practice to include template |
| OTP values not redacted in success logs (only redacted in error path) | Medium | `ConvoChatOtpService.php` — success log doesn't redact OTP |
| `ConvoChatCache::warmUp()` silently catches all exceptions | Low | Acceptable for warming but logs at warning level |

### 3.5 CI/CD

| Finding | Severity | Details |
|---------|----------|---------|
| `actions/cache@v3` used — v4 available with better performance | Low | `.github/workflows/tests.yml:64,95` |
| PHPStan runs without `--memory-limit` — fails locally on default 128M PHP config | Medium | Needs `--memory-limit=512M` or similar |
| No code coverage reporting in CI (coverage: none) | Medium | Could add Codecov/Coveralls |
| No Dependabot or Renovate configured for dependency updates | Medium | 3 CVEs went unnoticed |
| `composer.lock` not committed — builds may be non-reproducible | Low | Common choice for libraries, but worth noting |
| Console commands excluded from PHPStan (`excludePaths: src/Console`) | Low | `TestConvoChatCommand` not statically analyzed |

### 3.6 Observability

| Finding | Severity | Details |
|---------|----------|---------|
| Logging is behind `config('convochat.log_requests')` flag — off by default | OK | Sensible default |
| No structured log channel — uses default Laravel log | Low | Could offer dedicated channel config |
| No metrics/timing of API calls | Low | Would help users monitor latency |
| Error logs include full request data (good) with redacted secrets (good) | OK | Properly implemented |

### 3.7 DX (Developer Experience)

| Finding | Severity | Details |
|---------|----------|---------|
| CLAUDE.md is comprehensive and well-maintained | OK | Excellent AI assistant documentation |
| ENDPOINTS.md documents all endpoints with examples | OK | Good reference |
| README.md and CHANGELOG.md present | OK | Standard |
| No `.env.example` for quick setup | Low | Would improve onboarding |
| `composer test` alias not defined in composer.json scripts | Medium | Must use `./vendor/bin/phpunit` directly |
| VSCode config is thorough | OK | Good for contributors |
| Test command (`convochat:test`) excluded from PHPStan | Low | Minor |

---

## 4. Backlog (see IMPROVEMENT_BACKLOG.md)

Summary table included in separate file.

---

## 5. Execution Plan

### Phase 0: Baseline & Measurements
- [x] PHPUnit: 41 tests, 108 assertions, 0.168s, 28MB — **PASS**
- [x] PHPStan level 8: 0 errors (with 512M memory) — **PASS**
- [x] PHP CS Fixer: 0 fixable files — **PASS**
- [x] Composer audit: **3 CVEs** (2 high, 1 medium)
- [x] LOC: 2,772 total PHP lines (excluding vendor)

### Phase 1: Quick Wins (Low Risk, High Impact)
1. Add `composer.json` scripts for `test`, `analyse`, `format`
2. Fix PHPStan memory limit in CI workflow
3. Upgrade `actions/cache@v3` to `@v4`
4. Add Dependabot configuration
5. Fix `sendSms()` secret override vulnerability

### Phase 2: Safe Refactors
1. Extract `BaseConvoChatService` abstract class
2. Create domain-specific exception classes
3. Fix `SendBulkSmsJob` re-dispatch logic
4. Add tests for OTP, USSD, Contacts services
5. Reduce PHPStan error suppressions

### Phase 3: Structural Changes
1. Integrate `ConvoChatCache` into services (opt-in)
2. Integrate `RateLimiter` into services (opt-in)
3. Add interfaces for all services
4. Type the anonymous class in ServiceProvider properly

### Phase 4: Hardening
1. Add `.env.example`
2. Configure code coverage in CI
3. Add changelog automation
4. Audit and document all PHPStan suppressions
5. Consider Laravel Notifications channel integration

---

## 6. Changes Applied

No code changes were applied in this audit. All findings are documented for review and prioritized implementation.

---

## 7. Risks & Rollback

| Risk | Mitigation |
|------|-----------|
| Extracting base class could break consumers who extend services | Mark as `@internal` or version bump to 2.0 |
| Dependency updates could introduce breaking changes | Pin to patch versions, test in CI matrix |
| Adding interfaces changes public API | Add as opt-in, don't require for v1.x |
| Cache integration could mask API errors | Make caching opt-in via config flag |

---

## 8. Recommendations & Definition of Done

### Definition of Done for each backlog item:
- [ ] Implementation complete
- [ ] Unit/feature tests pass
- [ ] PHPStan level 8 clean
- [ ] PHP CS Fixer clean
- [ ] CHANGELOG.md updated
- [ ] README.md updated (if public API changes)
- [ ] CI green across full matrix

### Final Recommendations:
1. **Address CVEs immediately** — update dev dependencies
2. **Extract base class** — this is the single highest-impact refactor, eliminating ~240 lines of duplication
3. **Fix the bulk job** — the re-dispatch pattern can create runaway jobs
4. **Add missing tests** — 3 of 5 services have zero test coverage
5. **Fix secret override** — `sendSms()` parameter merge order allows overriding the API key
