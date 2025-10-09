# Release Command

Prepara una nueva versión del paquete para release:

1. Verifica que todo esté limpio (git status)
2. Ejecuta suite completa de tests (PHPUnit + PHPStan + CS Fixer)
3. Pregunta qué tipo de release: patch, minor, major
4. Actualiza CHANGELOG.md con los cambios desde última versión
5. Actualiza la versión en composer.json si es necesario
6. Crea git tag con la nueva versión
7. Muestra instrucciones para publicar en Packagist

Notas:
- Sigue Semantic Versioning (semver.org)
- Documenta todos los cambios importantes
- Verifica compatibilidad PHP/Laravel antes de release
