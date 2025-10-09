# New Feature Command

Workflow completo para agregar una nueva feature:

1. **Planificación:**
   - Pregunta qué feature se quiere agregar
   - Discute el diseño y arquitectura
   - Identifica qué archivos se modificarán

2. **Implementación:**
   - Crea/modifica servicios necesarios
   - Mantiene compatibilidad con Laravel 8.x, 9.x, 10.x
   - Sigue PSR-12 y estándares del proyecto
   - Usa typed properties (PHP 8.0+)

3. **Testing:**
   - Crea tests unitarios para la nueva feature
   - Crea tests de integración si es necesario
   - Verifica que PHPStan nivel 8 no tiene errores
   - Ejecuta suite completa de tests

4. **Documentación:**
   - Actualiza README.md con ejemplos de uso
   - Actualiza CLAUDE.md si afecta arquitectura
   - Agrega entrada en CHANGELOG.md
   - Documenta métodos con PHPDoc

5. **Finalización:**
   - Verifica que todo funciona correctamente
   - Sugiere crear commit descriptivo
   - Ofrece crear branch si no existe
