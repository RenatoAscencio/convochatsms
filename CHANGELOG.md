# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2024-09-17

### ✨ Added

#### 🔧 Análisis de código y calidad
- **PHPStan level 8**: Análisis estático completo con cero errores
- **PHP CS Fixer**: Formateo automático de código con estándares PSR-12
- **GitHub Actions CI/CD**: Tests automáticos en múltiples versiones PHP (8.0-8.4) y Laravel (8-11)
- **Codecov integration**: Reportes de cobertura de código

#### ⚡ Mejoras de rendimiento y arquitectura
- **Inyección de dependencias mejorada**: HTTP client configurable y testeable
- **Constantes organizadas**: Eliminación de strings mágicos en servicios
- **Validación robusta de configuración**: Validación automática de API keys, URLs y timeouts
- **Logging estructurado**: Logs con contexto completo y API keys redactados
- **Cache layer**: Sistema de caché inteligente para respuestas de API
- **Queue integration**: Jobs para envíos masivos asincrónicos
- **Rate limiting**: Protección contra spam y abuso

#### 🧪 Testing y debugging
- **41 tests completos**: Cobertura de funcionalidad, configuración y errores
- **Tests de timeout**: Verificación de configuraciones personalizadas de timeout
- **Tests de constantes**: Validación de todos los valores constantes
- **Tests de configuración**: Validación de parámetros requeridos

#### 📚 Documentación ampliada
- **README comprehensivo**: Ejemplos avanzados, troubleshooting y mejores prácticas
- **Guías de seguridad**: Patrones de protección de API keys y validación
- **Ejemplos de performance**: Optimizaciones para envíos masivos
- **Guías de debugging**: Herramientas y técnicas de diagnóstico

### 🔄 Changed

- **Arquitectura de servicios**: Constructores mejorados con inyección de dependencias
- **Manejo de errores**: Sistema más robusto con logging detallado
- **Tests**: Migración a PHPUnit 12 con método names en lugar de @test annotations
- **Timeout configuration**: Ahora configurable por servicio y globalmente

### 🐛 Fixed

- **Tests de error handling**: Corrección de tests que esperaban arrays en lugar de excepciones
- **Composer dependencies**: Actualización a versiones más recientes y compatibles
- **PHPStan issues**: Resolución de todos los problemas de análisis estático
- **Code formatting**: Aplicación consistente de estándares de código

### 🛡️ Security

- **API key protection**: Redacción automática en logs
- **Input validation**: Validación estricta de números de teléfono y URLs
- **Rate limiting**: Protección contra abuso de API

### 📈 Performance

- **Connection reuse**: Reutilización de conexiones HTTP
- **Smart caching**: Caché con TTL optimizado por tipo de datos
- **Batch processing**: Soporte para envíos masivos eficientes
- **Memory optimization**: Uso reducido de memoria en operaciones bulk

---

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
