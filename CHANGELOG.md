# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### 🔧 Fixed

- **PHPStan errors**: Reemplazado `logger()?->` con `Log` facade para evitar errores de nullsafe
- **GitHub Actions**: Eliminado Laravel 9.x que causaba conflictos de dependencias
- **Code Quality**: Todos los archivos ahora pasan PHPStan nivel 8

### 🗑️ Removed

- **Laravel 9.x support**: Laravel 9 removido por conflictos de dependencias (usar versión 1.x para Laravel 9)

---

## [2.0.0] - 2025-10-09

### ✨ Added

- **Laravel 12 support**: Agregado soporte completo para Laravel 12.x
- **Laravel 11 support**: Agregado soporte completo para Laravel 11.x
- **PHP 8.4 support**: Compatibilidad completa con PHP 8.4
- **Claude Code integration**:
  - Agentes especializados: `laravel-package-expert`, `php-testing-expert`, `api-integration-specialist`
  - Comandos slash personalizados: `/test`, `/fix`, `/release`, `/docs`, `/new-feature`
  - CLAUDE.md: Guía completa para asistentes IA
- **VSCode configuration**:
  - Configuración compartida del workspace
  - Extensiones recomendadas para desarrollo PHP/Laravel
  - Tasks automatizadas para tests y análisis
  - Snippets personalizados del proyecto
- **Compatibility matrix**: Tabla de compatibilidad detallada en documentación

### 🔄 Changed

- **Minimum PHP version**: Actualizado de 8.0 a 8.1
- **Dropped Laravel 8.x support**: Laravel 8 ya no es soportado (EOL)
- **Updated dependencies**:
  - `illuminate/support`: ^9.0|^10.0|^11.0|^12.0 (anteriormente ^8.0|^9.0|^10.0)
  - `phpunit/phpunit`: ^10.0|^11.0 (anteriormente ^9.5|^10.0|^11.0)
  - `orchestra/testbench`: ^7.0|^8.0|^9.0|^10.0 (añadidos v9 y v10)
  - `mockery/mockery`: ^1.6 (actualizado desde ^1.4)
- **GitHub Actions matrix**: Actualizada para incluir Laravel 11 y 12 con todas las combinaciones de PHP

### 📚 Documentation

- **README.md**: Actualizado con versiones PHP 8.1-8.4 y Laravel 9-12
- **CLAUDE.md**: Documentación completa de agentes y comandos Claude Code
- **Compatibility matrix**: Tabla detallada de versiones soportadas
- **.vscode/README.md**: Documentación de configuración VSCode
- **.claude/README.md**: Documentación de agentes y comandos

### 🧪 Testing

- **CI/CD expansion**: Tests en 12 combinaciones de PHP/Laravel (anteriormente 9)
- **Laravel 11.x testing**: PHP 8.2, 8.3, 8.4 con Testbench ^9.0
- **Laravel 12.x testing**: PHP 8.2, 8.3, 8.4 con Testbench ^10.0

### 🗑️ Removed

- **Laravel 8.x support**: Eliminado soporte para Laravel 8 (EOL)
- **PHP 8.0 support**: Eliminado soporte para PHP 8.0

---

## [1.1.1] - 2025-09-18

### 🔧 Fixed

- **Logger null-safe calls**: Agregado operador null-safe (?->) a llamadas logger() en SendBulkSmsJob y ConvoChatCache
- **PHPStan Level 8 compliance**: Eliminados todos los errores de análisis estático relacionados con logger()
- **GitHub Actions stability**: Estabilizada matriz de CI con combinaciones confiables de PHP/Laravel

### 🔄 Changed

- **Laravel 11 support**: Temporalmente removido soporte para Laravel 11 debido a incompatibilidades de dependencias
- **Supported Laravel versions**: Ahora soporta Laravel 8.x, 9.x, 10.x (matriz estable y probada)
- **CI/CD matrix**: Reducida a 9 combinaciones bien probadas para mayor confiabilidad

### 📚 Documentation

- **README badges**: Agregados badges de versiones PHP y Laravel soportadas
- **Test status badge**: Agregado badge dinámico del estado de GitHub Actions
- **Version accuracy**: Badges ahora reflejan las versiones realmente soportadas

### 🛡️ Quality Assurance

- **100% test success**: 41/41 tests pasando en todas las combinaciones soportadas
- **PHPStan Level 8**: Análisis estático sin errores
- **Stable CI pipeline**: GitHub Actions ejecutándose consistentemente sin fallos

---

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
