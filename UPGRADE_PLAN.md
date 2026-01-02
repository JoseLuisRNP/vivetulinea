# Plan de Actualización de Dependencias Frontend

## Objetivo
Actualizar todas las dependencias del frontend de forma segura y controlada, minimizando el riesgo de romper la aplicación.

## Estrategia
Actualización por fases, comenzando con las más seguras y avanzando hacia las que requieren más atención.

---

## FASE 1: Actualizaciones Seguras (Parches y Menores)
**Riesgo: Bajo** | **Tiempo estimado: 15 minutos**

### Dependencias a actualizar:
```json
{
  "@tailwindcss/forms": "^0.5.10",
  "autoprefixer": "^10.4.23",
  "eslint-config-prettier": "^10.1.8",
  "eslint-plugin-prettier": "^5.5.4",
  "postcss": "^8.5.6",
  "prettier": "^3.7.4",
  "typescript": "^5.9.3",
  "vue-tsc": "^2.2.12",
  "vite-plugin-vue-devtools": "^7.7.9",
  "@unovis/ts": "^1.6.2",
  "@unovis/vue": "^1.6.2"
}
```

### Pasos:
1. Crear branch: `git checkout -b chore/update-frontend-deps-phase1`
2. Actualizar package.json con las nuevas versiones
3. Ejecutar `npm install`
4. Verificar que no hay conflictos: `npm list --depth=0`
5. Ejecutar linter: `npm run lint:check`
6. Ejecutar type check: `npm run build` (o `vue-tsc --noEmit`)
7. Probar la aplicación en desarrollo: `npm run dev`
8. Si todo funciona: commit y merge

---

## FASE 2: Actualizaciones Moderadas (Mayores dentro de misma versión principal)
**Riesgo: Medio** | **Tiempo estimado: 30 minutos**

### Dependencias a actualizar:
```json
{
  "vue": "^3.5.25",
  "eslint-plugin-vue": "^10.6.2",
  "@vueuse/core": "^14.1.0",
  "@sentry/vue": "^10.30.0"
}
```

### Consideraciones especiales:
- **Vue 3.5.x**: Incluye nuevas características pero mantiene compatibilidad con Vue 3.x
- **@vueuse/core**: Revisar breaking changes en la documentación
- **@sentry/vue**: Revisar guía de migración de v7 a v10
- **eslint-plugin-vue**: Revisar cambios en reglas de ESLint

### Pasos:
1. Crear branch: `git checkout -b chore/update-frontend-deps-phase2`
2. Revisar changelogs de cada paquete para breaking changes
3. Actualizar package.json
4. Ejecutar `npm install`
5. Revisar y actualizar código si hay breaking changes:
   - Verificar uso de @vueuse/core
   - Verificar configuración de Sentry
   - Verificar reglas de ESLint
6. Ejecutar linter y type check
7. Probar funcionalidades críticas de la aplicación
8. Si todo funciona: commit y merge

---

## FASE 3: Actualizar Herramientas de Desarrollo (ESLint y TypeScript)
**Riesgo: Medio-Alto** | **Tiempo estimado: 45 minutos**

### Dependencias a actualizar:
```json
{
  "eslint": "^9.39.2",
  "@typescript-eslint/eslint-plugin": "^8.49.0",
  "@typescript-eslint/parser": "^8.49.0",
  "@vue/eslint-config-typescript": "^14.6.0"
}
```

### Consideraciones especiales:
- **ESLint 9**: Cambios significativos en configuración (flat config)
- **@typescript-eslint 8**: Compatible con ESLint 9
- Puede requerir actualizar `.eslintrc.js` a formato flat config

### Pasos:
1. Crear branch: `git checkout -b chore/update-frontend-deps-phase3`
2. **IMPORTANTE**: Hacer backup de `.eslintrc.js`
3. Revisar guía de migración de ESLint 8 a 9
4. Actualizar package.json
5. Ejecutar `npm install`
6. Migrar configuración de ESLint:
   - Convertir `.eslintrc.js` a `eslint.config.js` (flat config)
   - Actualizar reglas según nuevas versiones
7. Ejecutar `npm run lint:check` y corregir errores
8. Verificar que el IDE sigue funcionando con ESLint
9. Si todo funciona: commit y merge

---

## FASE 4: Actualizar Vite y Plugins Relacionados
**Riesgo: Alto** | **Tiempo estimado: 60 minutos**

### Dependencias a actualizar:
```json
{
  "vite": "^7.2.7",
  "@vitejs/plugin-vue": "^6.0.3",
  "laravel-vite-plugin": "^2.0.1",
  "vite-plugin-pwa": "^1.2.0"
}
```

### Consideraciones especiales:
- **Vite 7**: Cambios significativos desde v4
- **laravel-vite-plugin 2.0**: Puede requerir cambios en `vite.config.js`
- **vite-plugin-pwa 1.2**: Cambios en configuración
- Revisar `vite.config.js` y actualizar según sea necesario

### Pasos:
1. Crear branch: `git checkout -b chore/update-frontend-deps-phase4`
2. Hacer backup de `vite.config.js`
3. Revisar changelogs:
   - Vite: https://github.com/vitejs/vite/blob/main/packages/vite/CHANGELOG.md
   - laravel-vite-plugin: https://github.com/laravel/vite-plugin/blob/main/CHANGELOG.md
4. Actualizar package.json
5. Ejecutar `npm install`
6. Actualizar `vite.config.js` según nuevas APIs
7. Verificar que `npm run dev` funciona correctamente
8. Verificar que `npm run build` genera los assets correctamente
9. Probar hot module replacement (HMR)
10. Si todo funciona: commit y merge

---

## FASE 5: Actualizar Tailwind CSS y DaisyUI
**Riesgo: Alto** | **Tiempo estimado: 90 minutos**

### Dependencias a actualizar:
```json
{
  "tailwindcss": "^4.1.18",
  "daisyui": "^5.5.14"
}
```

### Consideraciones especiales:
- **Tailwind CSS 4**: Cambios significativos en configuración y sintaxis
- **DaisyUI 5**: Compatible con Tailwind 4, pero puede requerir actualizaciones
- Revisar `tailwind.config.js` y actualizar según nueva sintaxis
- Verificar que todas las clases CSS siguen funcionando
- Puede requerir actualizar estilos personalizados

### Pasos:
1. Crear branch: `git checkout -b chore/update-frontend-deps-phase5`
2. Hacer backup de `tailwind.config.js` y archivos CSS relacionados
3. Revisar guía de migración de Tailwind CSS 3 a 4:
   - https://tailwindcss.com/docs/upgrade-guide
4. Revisar changelog de DaisyUI 5
5. Actualizar package.json
6. Ejecutar `npm install`
7. Migrar configuración de Tailwind:
   - Actualizar `tailwind.config.js` a nueva sintaxis
   - Verificar `postcss.config.js`
   - Actualizar imports en archivos CSS
8. Ejecutar `npm run build` y revisar warnings/errores
9. Revisar visualmente todas las páginas de la aplicación
10. Verificar que los componentes de DaisyUI funcionan correctamente
11. Si hay problemas, revisar documentación y ajustar
12. Si todo funciona: commit y merge

---

## Verificaciones Post-Actualización

Después de cada fase, verificar:

- [ ] `npm install` se ejecuta sin errores
- [ ] `npm run lint:check` pasa sin errores
- [ ] `npm run build` genera los assets correctamente
- [ ] `npm run dev` inicia sin errores
- [ ] La aplicación carga en el navegador
- [ ] Hot Module Replacement (HMR) funciona
- [ ] No hay errores en la consola del navegador
- [ ] Las funcionalidades principales de la app funcionan
- [ ] Los estilos se renderizan correctamente

---

## Notas Adicionales

### Dependencias con Consideraciones Especiales:

1. **v-calendar**: Tienes v3.1.2 instalado, pero npm outdated muestra v2.4.2 como latest. Verificar manualmente la versión correcta.

2. **vue-toastification**: Tienes v2.0.0-rc.5 (release candidate). El latest estable es v1.7.14. Decidir si mantener RC o volver a estable.

3. **@types/ziggy-js**: Tienes v1.8.0 instalado pero package.json especifica ^1.3.2. Actualizar a la versión instalada.

### Orden de Ejecución Recomendado:

1. ✅ FASE 1 (Seguras) - Hacer primero
2. ✅ FASE 2 (Moderadas) - Después de FASE 1
3. ⚠️ FASE 3 (ESLint) - Requiere atención especial
4. ⚠️ FASE 4 (Vite) - Requiere atención especial
5. ⚠️ FASE 5 (Tailwind) - Requiere más tiempo y pruebas

### Estrategia de Rollback:

Si alguna fase falla:
1. Revertir el commit: `git revert HEAD`
2. O volver al branch anterior: `git checkout main && git branch -D chore/update-frontend-deps-phaseX`
3. Restaurar package.json y package-lock.json desde git
4. Ejecutar `npm install` para restaurar dependencias

---

## Comandos Útiles

```bash
# Verificar dependencias desactualizadas
npm outdated

# Verificar dependencias instaladas
npm list --depth=0

# Limpiar cache y reinstalar
rm -rf node_modules package-lock.json
npm install

# Verificar vulnerabilidades
npm audit

# Verificar tipos TypeScript
npx vue-tsc --noEmit

# Ejecutar linter
npm run lint:check

# Build de producción
npm run build
```

---

## Timeline Estimado

- **FASE 1**: 15 minutos
- **FASE 2**: 30 minutos
- **FASE 3**: 45 minutos
- **FASE 4**: 60 minutos
- **FASE 5**: 90 minutos
- **Total**: ~4 horas (incluyendo pruebas y ajustes)

---

**Última actualización**: $(date)
**Creado por**: Plan de actualización de dependencias frontend





