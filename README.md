<p align="center">
  <img src="public/images/logos/logo-dark.png" width="260" alt="Staynest Logo">
</p>



![Laravel](https://img.shields.io/badge/Laravel-12-f9322c?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-777bb4?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479a1?logo=mysql)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.4-06B6D4?logo=tailwindcss&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES2023-F7DF1E?logo=javascript&logoColor=black)
![Vite](https://img.shields.io/badge/Vite-5.0-646CFF?logo=vite&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Desktop-2496ed?logo=docker)
![Stripe](https://img.shields.io/badge/Stripe-Test_Mode-635bff?logo=stripe)

---

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
**Frontend:** TailwindCSS · CSS personalizado · JavaScript · Vite · PhotoSwipe  
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
# 1. Configurar variables de entorno
cp env.docker.example .env

# 2. Editar .env y añadir tus claves:
#    - STRIPE_KEY (tu clave pública de Stripe test)
#    - STRIPE_SECRET (tu clave secreta de Stripe test)
#    - GOOGLE_MAPS_API_KEY (tu clave de Google Maps)
#    Los emails se guardan automáticamente en logs

# 3. Construir y levantar contenedores siempre con:
docker compose up --build

#    Este comando ejecuta automáticamente:
#    - Migraciones
#    - Seeders con datos demo
#    - storage:link
#    - Corrección de permisos
#    - Scheduler para cancelar reservas pendientes

# Ver logs
docker compose logs -f app

# Detener contenedores
docker compose down

# Reiniciar desde cero
docker compose down -v
docker compose up --build
```

Disponible en `http://localhost:8000`

---

**Servicios incluidos:**
- **app**: Aplicación Laravel en puerto 8000
- **db**: MySQL 8.0 en puerto 3307
- **scheduler**: Cancelación automática de reservas cada minuto
- **node**: Vite dev server en puerto 5173 (desarrollo)

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

**Versión:** v1.0.0  
**Estado:** Listo para defensa de TFG  
**Entrega:** 12 de diciembre de 2025

---

## Autor

**Raquel Fernández Santos**  
2º DAW · Curso 2024-2025  
Tutor: **Mario Álvarez Fernández**  
