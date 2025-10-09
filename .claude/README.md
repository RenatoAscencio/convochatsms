# Claude Code Configuration

Esta carpeta contiene la configuración de Claude Code para el proyecto ConvoChat Laravel SMS & WhatsApp Gateway.

## Estructura

```
.claude/
├── agents/                    # Agentes especializados
│   ├── laravel-package-expert.md
│   ├── php-testing-expert.md
│   └── api-integration-specialist.md
├── commands/                  # Comandos slash personalizados
│   ├── test.md
│   ├── fix.md
│   ├── release.md
│   ├── docs.md
│   └── new-feature.md
├── settings.local.json        # Configuración personal (ignorado por git)
└── README.md                  # Este archivo
```

## Agentes Especializados

Los agentes proporcionan expertise profunda en áreas específicas del desarrollo.

### 🟠 `laravel-package-expert`
**Experto en Paquetes Laravel**
- Desarrollo de features para el paquete
- Service providers, facades, configuration
- Publicación en Packagist
- Testing con Orchestra Testbench

### 🟢 `php-testing-expert`
**Experto en Testing PHP/PHPUnit**
- Creación de tests unitarios e integración
- Mocking de HTTP clients y servicios
- Code coverage y TDD
- Debugging de tests fallidos

### 🔵 `api-integration-specialist`
**Especialista en Integración de APIs**
- Integración de endpoints REST
- Error handling y retry logic
- Timeout management y rate limiting
- Security best practices

Los agentes son invocados automáticamente por Claude Code cuando detecta tareas relacionadas con su especialización.

## Comandos Slash

Los comandos personalizan workflows comunes del proyecto.

### `/test` - Suite Completa de Tests
Ejecuta PHPUnit, PHPStan y PHP CS Fixer en una sola operación.

### `/fix` - Corrección Automática
Ejecuta PHP CS Fixer para arreglar formato y sugiere soluciones para PHPStan.

### `/release` - Preparar Nueva Versión
Workflow completo: verificar cambios, tests, actualizar CHANGELOG, crear git tag.

### `/docs` - Actualizar Documentación
Revisa código reciente y actualiza README, CHANGELOG y CLAUDE.md.

### `/new-feature` - Workflow Nueva Feature
Guía completa: planificación, implementación, tests, documentación, verificación.

## Configuración Personal

El archivo `settings.local.json` contiene permisos personalizados y no se sube a git.

Para modificar permisos locales:
1. Edita `.claude/settings.local.json`
2. Agrega rutas y comandos que no requieren confirmación
3. Los cambios solo afectan tu entorno local

## Uso

### Comandos Slash
En Claude Code, simplemente escribe `/test`, `/fix`, etc.

### Agentes
Los agentes se invocan automáticamente. También puedes referenciarlos explícitamente:
```
"Usa el laravel-package-expert para diseñar esta feature"
```

## Documentación Completa

Ver [CLAUDE.md](../CLAUDE.md) para:
- Descripción completa de cada agente
- Ejemplos de uso de comandos
- Guías de desarrollo
- Estándares del proyecto

## Mantenimiento

Al agregar nuevos agentes o comandos:
1. Crear archivo `.md` en carpeta correspondiente
2. Seguir formato estándar (ver archivos existentes)
3. Documentar en CLAUDE.md
4. Actualizar este README si es necesario

## Referencias

- **Documentación Claude Code**: <https://docs.claude.com/claude-code>
- **CLAUDE.md**: Guía completa para asistentes IA
- **README.md**: Documentación del paquete
