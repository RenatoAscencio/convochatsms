# Test Suite Command

Ejecuta el suite completo de tests del paquete ConvoChat:

1. Ejecuta PHPUnit con todos los tests
2. Ejecuta PHPStan análisis estático (nivel 8)
3. Ejecuta PHP CS Fixer para verificar formato PSR-12
4. Muestra un resumen de resultados

Si hay errores:
- Muestra los errores claramente
- Sugiere posibles soluciones
- Ofrece corregir automáticamente si es posible

Si todo está bien:
- Confirma que todos los tests pasan ✅
- Confirma que PHPStan nivel 8 no tiene errores ✅
- Confirma que el código cumple PSR-12 ✅
