# Memoria del Proyecto Staynest

> Versión: v0.9.x (demo)  
> Última actualización: 24/11/2025

## Índice
1. Manual del Usuario
   1.1 Objetivo del manual  
   1.2 Requisitos previos  
   1.3 Conceptos clave (Roles, Estados)  
   1.4 Zona pública (Home, Entorno, Ubicación, Reservar)  
   1.5 Autenticación (Registro / Login / Recuperar contraseña)  
   1.6 Área privada Huésped  
   1.7 Área privada Propietario  
   1.8 Correos y notificaciones (sandbox)  
   1.9 Accesibilidad y buenas prácticas  
   1.10 Solución de problemas  
   1.11 Glosario  
   1.12 Conclusión usuario  
2. Manual del Programador
   2.1 Objetivo  
   2.2 Stack y versiones  
   2.3 Estructura del proyecto  
   2.4 Modelos y relaciones  
   2.5 Flujo Reserva → Pago → Factura  
   2.6 Mailables, Jobs y Queue  
   2.7 Variables de entorno  
   2.8 Instalación y puesta en marcha  
   2.9 Seeders y datos demo  
   2.10 Tests  
   2.11 Estilos y Frontend  
   2.12 Seguridad  
   2.13 Stripe (modo test)  
   2.14 Deploy (sin Docker / con Docker)  
   2.15 Mantenimiento  
   2.16 Extensión futura  
   2.17 Convenciones (commits / tags)  
   2.18 Plantillas (Issue / Retrospectiva)  
3. Metodologías Utilizadas
   3.1 Enfoque híbrido incremental + ágil  
   3.2 Ciclo semanal  
   3.3 Gestión de backlog y versionado  
   3.4 Criterios de priorización  
   3.5 Calidad y rendimiento  
   3.6 Feedback y retrospectiva  
   3.7 Herramientas  
   3.8 Próximas mejoras proceso  
4. Conclusión Final

---
## 1. Manual del Usuario
### 1.1 Objetivo del manual
Guiar a cualquier persona (huésped o propietario) en el uso de Staynest de forma clara, sencilla y segura, sin necesidad de conocimientos técnicos.

### 1.2 Requisitos previos
- Navegador actualizado (Chrome, Firefox, Edge, Safari).  
- Conexión a Internet estable.  
- Cuenta de usuario:  
  - Huésped: puede crearla desde la web.  
  - Propietario: credenciales administrativas asignadas.  
- Entornos posibles: local de desarrollo / demo desplegada.

### 1.3 Conceptos clave (Roles, Estados)
**Roles:**  
- Huésped: crea reservas, descarga facturas, gestiona su perfil.  
- Propietario: administra propiedad, precios, fotos, facturación, estadísticas.

**Estados de reserva:** `pending` (pendiente de pago) → `paid` (pagada) → `cancelled` (cancelada) → (reembolsos automáticos si aplica).  
**Factura:** Documento PDF asociado a una reserva pagada.  
**Pago Stripe test:** Simulación sin impacto real.

### 1.4 Zona pública
**Home:** Imagen principal, descripción breve, licencia y registro oficial, menú fijo. Scroll ajusta estilo del encabezado. Galería ampliable.  
👉 [Captura Home]

**Entorno:** Información turística de la zona (Gijón), recomendaciones y fotos.  
👉 [Captura Entorno]

**Dónde estamos / Contacto:** Mapa (Google Maps) + formulario con validaciones y mensajes claros de error/éxito.  
👉 [Captura Mapa + Formulario]

**Reservar:** Selección fecha entrada / salida / huéspedes. El sistema valida disponibilidad, estancia mínima y capacidad. Si pasa validaciones: crea reserva `pending`. El usuario puede autenticarse para pagar.  
👉 [Captura Formulario Reserva]

### 1.5 Autenticación (Registro / Login / Recuperación)
- Registro huésped: correo + contraseña + datos básicos (edad verificada por fecha nacimiento).  
- Login: correo + contraseña; opción “Recuérdame”.  
- Recuperar contraseña: envía correo (sandbox Mailtrap).  
👉 [Captura Login / Registro]

### 1.6 Área privada Huésped
Menú “Cliente”:  
- Mis reservas: listado con estados, posibilidad de ver factura.  
- Mis facturas: descarga PDF.  
- Perfil: datos personales, avatar, opción eliminar cuenta.  
- Cerrar sesión.  
👉 [Capturas vistas huésped]

### 1.7 Área privada Propietario
- Dashboard: estadísticas básicas (reservas activas, ingresos).  
- Gestionar propiedad: editar datos, servicios, baja/restaurar.  
- Calendario (en evolución): precios, min_stay (reglas).  
- Fotos: subir, ordenar, eliminar; portada.  
- Facturas emitidas: acceso a PDF.  
- Perfil + Logout.  
👉 [Captura Dashboard]

### 1.8 Correos y notificaciones (sandbox)
Se generan Mailables (Stripe test y eventos reserva) y se envían a Mailtrap. No salen a destinatarios reales. Principales: confirmación reserva, cancelación, recibo de pago, reembolso y avisos al administrador.

### 1.9 Accesibilidad y buenas prácticas
- Altura estándar controles: 36px (botones).  
- Contraste neutro con color acento.  
- Etiquetas vinculadas a inputs (`for`/`id`).  
- Focus visible.  
- Textos en estilo oración (no mayúsculas forzadas).  
- Mensajes de error claros y específicos.

### 1.10 Solución de problemas
| Problema | Causa probable | Solución |
|----------|----------------|----------|
| No puedo iniciar sesión | Credenciales incorrectas | Usar “¿Has olvidado tu contraseña?” |
| No se envía formulario | Campos vacíos / inválidos | Revisar formato y completar |
| No carga el mapa | API Key ausente | Configurar `GOOGLE_MAPS_API_KEY` en `.env` |
| No llega el correo | Entorno demo | Ver Mailtrap inbox |
| Error de fechas | Solape / min_stay | Seleccionar otro rango |
| Error 403 | Falta permisos | Iniciar sesión con rol adecuado |
| Reserva no se confirma | Falta pago | Completar proceso Stripe test |
| Factura ausente | Worker no activo | Iniciar `queue:work` |
| Correo duplicado | Dos workers simultáneos | Detener uno |
| Error 419 | Sesión expirada | Refrescar y reenviar |
| Imagen no sube | Formato no permitido | Usar JPG/PNG válido |

### 1.11 Glosario
- Reserva pendiente: creada, sin pago confirmado.  
- PaymentIntent: intento de pago Stripe test.  
- Factura: PDF generado tras pago exitoso.  
- Baja de propiedad: estado inactivo (soft delete).  
- Worker: proceso que ejecuta jobs en cola.

### 1.12 Conclusión usuario
La navegación clara, validaciones y mensajes consistentes permiten gestionar reservas y facturas sin conocimientos técnicos.

---
## 2. Manual del Programador
### 2.1 Objetivo
Instalar, configurar, extender y desplegar Staynest en entornos locales y containerizados.

### 2.2 Stack y versiones
- PHP 8.2+ / Laravel 12.x  
- Composer 2.x  
- Node.js 20+ / npm / Vite  
- MySQL 8.x  
- Pest para tests  
- TailwindCSS (utilidades + CSS variables locales)  
- Alpine.js (interactividad ligera)  
- Stripe (modo test) / Mailtrap / Google Maps (opcional)

### 2.3 Estructura del proyecto (carpetas clave)
```
app/Http/Controllers      # Lógica de rutas
app/Models                # Modelos Eloquent
app/Mail                  # Mailables transaccionales
app/Policies              # Autorización
resources/views           # Vistas Blade y componentes
database/migrations       # Migraciones
database/seeders          # Seeders (Initial / Demo)
routes/                   # Archivos web.php, auth.php
public/                   # Assets compilados, storage symlink
tests/                    # Tests Pest (Feature / Unit)
```

### 2.4 Modelos y relaciones
- User: posee reservas, facturas indirectas.  
- Property: `hasMany` Reservations / Photos.  
- Reservation: `belongsTo` User / Property; `hasOne` Invoice; `hasMany` Payments.  
- Payment: `belongsTo` Reservation.  
- Invoice: `belongsTo` Reservation.  
- Photo: `belongsTo` Property.  
- RateCalendar (si está en evolución): reglas de precios / min_stay.

### 2.5 Flujo Reserva → Pago → Factura (texto)
1. Selección fechas → validación disponibilidad y reglas.  
2. Creación `Reservation` estado `pending`.  
3. Inicio pago (PaymentIntent test).  
4. Confirmación Stripe → Job registra `Payment` y genera `Invoice`.  
5. Reserva pasa a `paid`.  
6. Envío de correos (Mailtrap).  
7. Factura disponible para descarga.

### 2.6 Mailables, Jobs y Queue
**Mailables principales:** ReservationConfirmedMail, ReservationCancelledMail, PaymentReceiptMail, PaymentRefundIssuedMail, AdminReservationUpdatedMail, etc.  
**Queue:** Correos y procesos post-pago.  
Arranque local: `php artisan queue:work --queue=default --tries=3`  
En Docker (ejemplo): `docker compose exec app php artisan queue:work --daemon`

### 2.7 Variables de entorno
| Variable | Función |
|----------|---------|
| APP_ENV / APP_KEY | Contexto y cifrado interno |
| DB_HOST / DB_DATABASE / DB_USERNAME / DB_PASSWORD | Conexión MySQL |
| MAIL_MAILER / MAIL_HOST / MAIL_PORT / MAIL_USERNAME / MAIL_PASSWORD | Mailtrap sandbox |
| QUEUE_CONNECTION | Driver de cola (database/sync) |
| STRIPE_SECRET / STRIPE_PUBLIC_KEY | Claves Stripe test |
| STRIPE_WEBHOOK_SECRET | Validación firma de eventos |
| GOOGLE_MAPS_API_KEY | Mapa ubicación |
| FILESYSTEM_DISK | Almacenamiento (public) |

### 2.8 Instalación y puesta en marcha
```
git clone <repo-url>
cd staynest
composer install
npm install
cp .env.example .env
php artisan key:generate
# Configurar .env (DB, MAIL, STRIPE, MAPS)
php artisan migrate
php artisan db:seed --class=InitialDataSeeder
# Opcional:
php artisan db:seed --class=DemoDataSeeder
npm run dev  # o build
php artisan serve
```

### 2.9 Seeders y datos demo
- `InitialDataSeeder`: mínimos para arrancar.  
- `DemoDataSeeder`: casos completos para defensa (reservas, facturas, fotos).  
Ejecutar solo uno según necesidad.

### 2.10 Tests (Pest)
```
php artisan test
# o
./vendor/bin/pest
```
Cubre: autenticación, reglas de reserva (min_stay, solapes), flujo pago/factura, roles/autorización. Añadir nuevos tests en `tests/Feature` o `tests/Unit` manteniendo claridad.

### 2.11 Estilos y Frontend
- CSS variables para colores y tamaños.  
- Altura consistente botones: 36px.  
- Blade components para layouts y formularios.  
- Tailwind base + clases utilitarias propias + estilos inline específicos cuando se requiere precisión.  
- Accesibilidad: labels, focus, contraste, mensajes.

### 2.12 Seguridad
- CSRF nativo Laravel.  
- Hash de contraseñas Bcrypt.  
- Policies / Middleware por rol.  
- Validaciones en FormRequest (si se han implementado) o en controladores.  
- Subida de imágenes: restringir a formatos seguros (JPG/PNG).  
- Webhooks Stripe con verificación de firma.  
- Sanitización y límites de longitud en campos de texto.

### 2.13 Stripe (modo test)
- Usar tarjeta 4242 4242 4242 4242.  
- Ver eventos en panel Stripe test.  
- PaymentIntent nunca cobra dinero real.  
- Facturas señaladas como demo; podrían evolucionar a facturación fiscal real.  
- Webhook simula confirmación y dispara jobs.

### 2.14 Deploy
**Sin Docker:**  
1. Subir código.  
2. Configurar `.env` producción.  
3. `composer install --no-dev`  
4. `php artisan migrate --force`  
5. Cacheos: `php artisan config:cache`, `route:cache`, `view:cache`.  
6. `npm run build`  
7. Arrancar worker cola.

**Con Docker (defensa demo):**  
```
docker compose build
docker compose up -d
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --class=DemoDataSeeder
docker compose exec app npm run build
docker compose exec app php artisan queue:work --daemon
```
Ventajas: reproducible, evita diferencias de entorno, estabiliza defensa.

### 2.15 Mantenimiento
- Dependencias: `composer update` / `npm update` (controlado).  
- Logs: `storage/logs/laravel.log` (rotación manual si crece).  
- Backups: BD + `storage/app/public` (fotos, PDFs).  
- Limpieza periódica de archivos temporales si aplica.  
- Verificación de jobs y reintentos.

### 2.16 Extensión futura
- Multi-propiedad / marketplace.  
- Multi-idioma (i18n completa).  
- Pasarela de pago real (captura fondos).  
- Panel avanzado de precios dinámicos.  
- Integración mensajería interna huésped ↔ propietario.

### 2.17 Convenciones (commits / tags)
Commit claro en español, verbo presente: `fix: validación estancia mínima`.  
Tags semánticos: `vMAJOR.MINOR.PATCH` (ej: `v0.8.0` funcionalidad principal, `v0.8.1` corrección menor).  
Rama `main` por ser desarrollo monousuario; para equipo se recomiendan ramas feature.

### 2.18 Plantillas
**Issue (sugerido):**  
```
Título: <breve>
Descripción:
Pasos para reproducir:
Resultado esperado:
Logs relevantes:
Prioridad: alta/media/baja
```  
**Retrospectiva semanal:**  
- Objetivos cumplidos: …  
- Bloqueos: …  
- Ajustes: …  
- Riesgos próximos: …  
- Mejora de proceso: …

---
## 3. Metodologías Utilizadas
### 3.1 Enfoque híbrido incremental + ágil
Se construye por módulos funcionales estables (autenticación → reservas → panel admin) asegurando integraciones limpias. Cada módulo se revisa con mentalidad ágil en ciclos cortos (1 semana) permitiendo corregir y ajustar prioridades pronto.

### 3.2 Ciclo semanal
1. Planificación breve (selección de hitos).  
2. Desarrollo incremental.  
3. Revisión y smoke test.  
4. Ajuste backlog y documentación ligera.

### 3.3 Gestión de backlog y versionado
- Backlog implícito en issues / tareas internas.  
- Tags GitHub como checkpoints (`v0.7.0`, `v0.8.0`, `v0.9.x`).  
- SemVer adaptado a evolución incremental.

### 3.4 Criterios de priorización
- Valor directo para usuario final.  
- Riesgo técnico reducido.  
- Impacto en demo de defensa.  
- Facilidad de test.

### 3.5 Calidad y rendimiento
- Tests clave (reglas reserva, roles, flujo pago).  
- Paginación y `with()` para evitar N+1.  
- Minificado assets con Vite.  
- Cache config / rutas / vistas en producción.  
- Accesibilidad (contraste, labels, altura mínima).  
- Revisión visual tras cambios UI.

### 3.6 Feedback y retrospectiva
Micro-retrospectiva semanal siguiendo plantilla sugerida; ajuste rápido de prioridades y anotación de mejoras (ej: estandarizar altura botones, simplificar estilos checkbox).

### 3.7 Herramientas
- VS Code (extensiones básicas).  
- Git / GitHub (versionado + tags).  
- Pest (tests).  
- Mailtrap (sandbox correos).  
- Stripe test.  
- Flatpickr (fechas).  
- Docker (entorno reproducible).  
- Google Maps API (ubicación).  

### 3.8 Próximas mejoras proceso
- Automatizar CI (tests en push).  
- Añadir cobertura de código.  
- Integrar análisis estático (PHPStan más estricto).  
- Plantilla formal de issues + etiquetas (bug, feature, mejora).  
- Documentar decisiones arquitectónicas (ADR ligeros).

---
## 4. Conclusión Final
La documentación cubre uso funcional, despliegue técnico y metodología híbrida aplicada. Staynest mantiene una arquitectura limpia y extensible, con accesibilidad básica y seguridad razonable para entorno demo (Stripe test y Mailtrap). Base preparada para evolucionar a multi-propiedad y funcionalidades avanzadas sin rehacer el núcleo.

---
## Anexos Opcionales (añadir si se necesitan para la defensa)
- Diagrama simple flujo reserva:
```
[Usuario] -> (Formulario reserva) -> [Validación] -> [Reserva pending]
   -> (Stripe test pago) -> [Webhook / Job] -> [Payment + Invoice]
   -> [Reserva paid] -> (Correo confirmación)
```
- Comandos rápidos:
```
php artisan migrate:fresh --seed
php artisan queue:work
npm run build
```

Fin.
