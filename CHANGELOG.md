# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-09-13

### Added

- Initial release of ConvoChat Laravel SMS Gateway
- SMS sending functionality with device and credit modes
- WhatsApp messaging support (text, media, documents)
- Laravel Service Provider with auto-discovery
- Facade for easy usage (`ConvoChat::sms()`, `ConvoChat::whatsapp()`)
- Configuration file with environment variable support
- Comprehensive error handling and logging
- WhatsApp account linking and management
- Phone number validation for WhatsApp
- Support for Laravel 8.x, 9.x, 10.x, 11.x
- PHP 8.0+ compatibility

### Features

#### SMS
- Send SMS via Android devices or credits
- SIM slot selection (1 or 2)
- Priority settings (high/normal)
- Gateway rate checking
- Device management
- Credit balance checking
- Subscription information

#### WhatsApp

- Text message sending
- Media messages (image, audio, video)
- Document sending (PDF, Excel, Word)
- Account linking with QR code
- Account relinking
- Phone number validation
- Multiple account support

#### Laravel Integration

- Service Provider with singleton bindings
- Publishable configuration
- Facade support
- Environment-based configuration
- Request logging (configurable)
- Comprehensive error handling

## [Unreleased]

### Planned

- Laravel Notifications channel
- Queue job support for bulk messaging
- Template system
- Webhook handling
- Artisan commands
- Analytics and metrics
- Multi-tenant support
