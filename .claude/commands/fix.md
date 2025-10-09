# Fix Code Command

Corrige automáticamente problemas de código:

1. Ejecuta PHP CS Fixer para arreglar formato según PSR-12
2. Ejecuta PHPStan y muestra errores de análisis estático
3. Si hay errores de PHPStan, ofrece soluciones específicas
4. Ejecuta tests para verificar que no se rompió nada

Después de corregir:
- Muestra qué archivos se modificaron
- Confirma que los tests siguen pasando
- Sugiere hacer commit si todo está bien
