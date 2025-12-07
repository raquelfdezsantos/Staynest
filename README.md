# Staynest · Plataforma de Alquiler Vacacional (VUT)

Aplicación web para la gestión de viviendas de uso turístico, desarrollada con **Laravel 12**, **MySQL**, **TailwindCSS** y **Stripe (modo test)**.  
Proyecto Final de Grado — 2º DAW (septiembre–diciembre 2025).

---

## Descripción del proyecto

Staynest permite a propietarios gestionar sus alojamientos turísticos sin depender de intermediarios (Airbnb, Booking…).  
Incluye gestión completa de propiedades, calendarios y reservas, pagos con Stripe (modo test) y facturación automática.

Los clientes pueden consultar disponibilidad, reservar de forma segura y acceder a sus facturas.  
El sistema envía emails transaccionales y expira reservas pendientes mediante un scheduler.

---

## Funcionalidades principales

- **Gestión de propiedades:** CRUD completo, galería PhotoSwipe, calendario dinámico, geolocalización Google Maps
- **Sistema de reservas:** validaciones inteligentes, expiración automática (5 min), recordatorios por email
- **Pagos y facturación:** integración con Stripe (modo test), facturas PDF, reembolsos automáticos
- **Notificaciones:** emails automáticos para reservas, pagos, cancelaciones y recordatorios
- **Panel admin:** dashboard, gestión de propiedades/calendarios/fotos/reservas, perfil personalizable
- **Panel cliente:** historial de reservas, descarga de facturas, perfil editable
- **Interfaz pública:** pestañas interactivas, galería de fotos, formulario de contacto, diseño responsive

---

## Tecnologías

**Backend:** PHP 8.2 · Laravel 12 · Breeze · Blade · Eloquent · Stripe SDK · DomPDF  
**Frontend:** TailwindCSS 3 · CSS personalizado · JavaScript · Vite · PhotoSwipe  
**Base de datos:** MySQL 8.x  
**Servicios:** Stripe (modo test) · Ethereal · Google Maps API  
**Testing:** Pest  
**DevOps:** Git · GitHub · Docker 

---

## Instalación Local

```bash
# Clonar repositorio
git clone https://github.com/raquelfdezsantos/Staynest.git
cd Staynest

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Editar .env con credenciales de MySQL y Stripe
# DB_DATABASE, DB_USERNAME, DB_PASSWORD
# STRIPE_KEY, STRIPE_SECRET

# Migrar y sembrar base de datos
php artisan migrate:fresh --seed
php artisan storage:link

# Iniciar desarrollo
npm run dev
php artisan serve
php artisan schedule:work  # En otra terminal
```

Disponible en `http://localhost:8000`

---

## Despliegue con Docker

```bash
docker-compose up -d
docker-compose exec app php artisan migrate:fresh --seed
```

---

## Usuarios de Demostración

**Administradores:**
- **Raquel** (Admin Principal): `raquel@staynest.com` / `password`  
  _Propiedades: Apartamento Nordeste (Gijón)_
- **Ana** (Admin Secundaria): `ana@staynest.com` / `password`  
  _Propiedades: Estudio Llanes + Chalet Rías Bajas (eliminado, restaurable)_

**Clientes:**
- **Laura**: `laura@example.com` / `password` _(reserva pendiente)_
- **Carlos**: `carlos@example.com` / `password` _(reserva pagada)_
- **Isabel**: `isabel@example.com` / `password` _(reserva cancelada)_

---

## Documentación

- [Sistema de Expiración de Reservas](docs/SISTEMA_EXPIRACION_RESERVAS.md)

---

## Estado

**Versión:** v0.9.0  
**Estado:** Listo para defensa de TFG  
**Entrega:** 12 de diciembre de 2025

---

## Autor

**Raquel Fernández Santos**  
2º DAW · Curso 2025  
Tutor: **Mario Álvarez Fernández**  
