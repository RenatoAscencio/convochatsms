# VSCode Configuration

Esta carpeta contiene la configuración compartida del workspace para Visual Studio Code.

## Archivos Incluidos

### `extensions.json`
Lista de extensiones recomendadas para el desarrollo del paquete:
- **PHP**: Intelephense, PHPStan, PHP CS Fixer
- **Laravel**: Extra Intellisense, Blade Snippets, Goto View
- **Testing**: PHPUnit, PHP CodeSniffer
- **Productividad**: GitLens, Git Graph, Error Lens, TODO Tree

### `settings.json`
Configuración del workspace:
- Formateo automático con PHP CS Fixer (PSR-12) al guardar
- PHPStan nivel 8 habilitado
- Configuración de rulers a 120 caracteres
- Integración con PHPUnit

### `tasks.json`
Tareas automatizadas (accesibles con `Cmd+Shift+B`):
- Run PHPUnit Tests
- Run PHPUnit with Coverage
- Run PHPStan Analysis
- Run PHP CS Fixer (Check/Fix)
- Composer Install/Update
- Full Test Suite

### `launch.json`
Configuraciones de debug:
- Listen for XDebug
- Debug Current PHP Script
- Debug PHPUnit Test

### `snippets.code-snippets`
Snippets personalizados para el proyecto:
- `test` - Método test PHPUnit completo
- `convosms` - Envío SMS ConvoChat
- `convowa` - Envío WhatsApp ConvoChat
- `tryconvo` - Try-catch para ConvoChat
- `lcontroller` - Método controlador Laravel
- `doc` - PHPDoc block

## Instalación de Extensiones

Al abrir el proyecto por primera vez, VSCode te sugerirá instalar las extensiones recomendadas.

También puedes instalarlas manualmente desde:
- Command Palette (`Cmd+Shift+P`)
- Buscar: "Extensions: Show Recommended Extensions"

## Uso de Tareas

Para ejecutar tareas:
1. Presiona `Cmd+Shift+B` (macOS) o `Ctrl+Shift+B` (Windows/Linux)
2. Selecciona la tarea que deseas ejecutar
3. La tarea "Run PHPUnit Tests" es la tarea por defecto

## Personalización

Si deseas personalizar la configuración localmente sin afectar al repositorio:
1. Crea un archivo `.vscode/settings.local.json` (ignorado por git)
2. Agrega tu configuración personal

## Beneficios

Esta configuración compartida asegura que:
- ✅ Todos los desarrolladores usen las mismas herramientas
- ✅ El código se formatee automáticamente con PSR-12
- ✅ PHPStan analice el código en tiempo real (nivel 8)
- ✅ Los tests sean fáciles de ejecutar desde el IDE
- ✅ Los snippets aceleren el desarrollo

## Mantenimiento

Esta configuración es parte del proyecto y debe mantenerse sincronizada con:
- **CLAUDE.md**: Documentación de herramientas y configuración
- **README.md**: Guía de instalación y desarrollo
- **composer.json**: Scripts y herramientas del proyecto
