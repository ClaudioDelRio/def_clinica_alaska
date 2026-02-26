# 📋 Estado de Documentación RUT

## ℹ️ Contexto
La integración de RUT formaba parte de la versión universitaria del proyecto, asociada a registro/login y backend propio.

Desde la simplificación del sitio (26 de febrero de 2026), esa arquitectura fue retirada.

---

## ✅ Estado actual
En la versión vigente:
- No hay login o registro de usuarios en el frontend
- No existe API local para validar o persistir RUT
- No se utiliza base de datos propia para cuentas de clientes

Por lo tanto, **la validación de RUT ya no es una funcionalidad activa** del sitio público actual.

---

## 🗂️ Qué sí se mantiene
- Sitio informativo en `index.html` y `doctores.html`
- Agendamiento externo mediante NoahVet

URL de agendamiento:
- `https://panel.noahvetspa.cl/noahvet/index.php?slug=alaska`

---

## 🔄 Si se requiere RUT en el futuro
Si más adelante se vuelve a implementar backend propio, se recomienda crear una nueva documentación técnica de RUT alineada con:
1. arquitectura vigente,
2. endpoints reales,
3. validación frontend/backend,
4. almacenamiento y seguridad de datos.
