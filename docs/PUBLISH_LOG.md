# Publish Log — v4.0.0

**Date:** 2026-01-31
**Operator:** Claude Opus 4.5 (automated)

## 1. Local Verification

```
$ git status              # clean (cache files in .gitignore)
$ git pull --rebase       # up to date
$ composer quality        # tests: 70 pass, PHPStan: 0 errors, CS Fixer: 0 violations
$ composer audit          # 0 vulnerabilities
```

**Result:** PASS

## 2. Tag Creation

```
$ git tag -a v4.0.0 -m "v4.0.0"
$ git show v4.0.0 --stat  # verified: points to commit 3b4afde
```

**Result:** Tag created

## 3. Push

```
$ git push origin main    # 1b17c67..3b4afde  main -> main (16 commits)
$ git push origin v4.0.0  # [new tag] v4.0.0 -> v4.0.0
```

**Result:** Pushed

## 4. GitHub Release

```
$ gh release create v4.0.0 --title "v4.0.0" --latest --notes "..."
```

**URL:** https://github.com/RenatoAscencio/convochatsms/releases/tag/v4.0.0

**Result:** Created, marked as Latest

## 5. Packagist

**URL:** https://packagist.org/packages/convochatsms/laravel-sms-whatsapp-gateway

**Status:** Auto-updated. v4.0.0 detected and listed as latest stable release within seconds of tag push. Webhook is active.

**Install command:**
```bash
composer require convochatsms/laravel-sms-whatsapp-gateway:^4.0
```

**Result:** OK

## 6. Consumer Verification

Created temp project in `/tmp/convochat-consumer-test`:

```
$ composer init --require="laravel/framework:^11.0" --require="convochatsms/laravel-sms-whatsapp-gateway:^4.0"
$ composer install
```

**Locked version:** `convochatsms/laravel-sms-whatsapp-gateway (v4.0.0)`

**Class autoload test (10/10 classes):**
- ConvoChatServiceProvider: OK
- ConvoChatManager: OK
- ConvoChat (Facade): OK
- ConvoChatSmsService: OK
- ConvoChatWhatsAppService: OK
- ConvoChatContactsService: OK
- ConvoChatOtpService: OK
- ConvoChatUssdService: OK
- BaseConvoChatService: OK
- SendBulkSmsJob: OK

**HTTP mock test:** Constructor accepts custom Client + config. Call chain works through sendSmsWithCredits -> sendSms -> makeRequest. Fails at `config()` helper as expected (no Laravel app booted in raw PHP script). Full integration verified via ConsumerSmokeTest.php in test suite (Orchestra Testbench).

**Result:** PASS

## Summary

| Step | Status |
|------|--------|
| Local quality gate | PASS |
| Tag v4.0.0 | Created |
| Push (main + tag) | Done |
| GitHub Release | https://github.com/RenatoAscencio/convochatsms/releases/tag/v4.0.0 |
| Packagist | v4.0.0 live, auto-updated |
| Consumer install | v4.0.0 installs, all classes load |
