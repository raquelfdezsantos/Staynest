# Sistema de Expiración de Reservas

## Descripción

Este sistema cancela automáticamente las reservas pendientes de pago que no se completen dentro de las 24 horas siguientes a su creación.

## Funcionamiento

### 1. Creación de Reserva
Cuando se crea una reserva:
- Se establece `status = 'pending'`
- Se establece `expires_at = now() + 24 horas`
- Se envía email de confirmación con aviso de las 24 horas

### 2. Recordatorio (1 hora antes)
El comando `reservations:send-expiration-reminders` (ejecutado cada hora):
- Busca reservas con estado `pending`
- Que expiren en los próximos 50-70 minutos
- Envía email de recordatorio al usuario

### 3. Expiración Automática
El comando `reservations:expire-pending` (ejecutado cada hora):
- Busca reservas con estado `pending`
- Cuyo `expires_at` ya haya pasado
- Cambia el estado a `cancelled`
- Libera las fechas del calendario
- Envía email notificando la expiración

### 4. Pago Completado
Cuando el usuario paga:
- Se establece `status = 'paid'`
- Se elimina `expires_at` (se pone a `null`)
- La reserva ya no expirará

## Comandos Artisan

### Expirar reservas manualmente
```bash
php artisan reservations:expire-pending
```

### Enviar recordatorios manualmente
```bash
php artisan reservations:send-expiration-reminders
```

### Ver lista de comandos programados
```bash
php artisan schedule:list
```

### Ejecutar el scheduler manualmente (desarrollo)
```bash
php artisan schedule:work
```

## Configuración en Producción

### Opción 1: Cron Job (Linux/macOS)
Agregar a crontab (`crontab -e`):
```
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

### Opción 2: Task Scheduler (Windows)
1. Abrir "Programador de tareas"
2. Crear tarea básica
3. Disparador: Cada minuto
4. Acción: Iniciar programa
   - Programa: `C:\ruta\a\php.exe`
   - Argumentos: `artisan schedule:run`
   - Directorio: `C:\ruta\a\tu\proyecto`

### Opción 3: Servicio Systemd (Linux)
Crear `/etc/systemd/system/staynest-scheduler.service`:
```ini
[Unit]
Description=Staynest Laravel Scheduler
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/staynest
ExecStart=/usr/bin/php artisan schedule:work
Restart=always

[Install]
WantedBy=multi-user.target
```

Luego:
```bash
sudo systemctl enable staynest-scheduler
sudo systemctl start staynest-scheduler
```

## Emails Enviados

### 1. ReservationConfirmedMail
- **Cuándo**: Al crear la reserva
- **Contenido**: Datos de la reserva + aviso de 24 horas para pagar

### 2. ReservationExpiringReminderMail
- **Cuándo**: 1 hora antes de expirar
- **Contenido**: Recordatorio urgente con botón de pagar

### 3. ReservationExpiredMail
- **Cuándo**: Cuando se cancela por expiración
- **Contenido**: Notificación de cancelación + opción de crear nueva reserva

## Base de Datos

### Migración
- Archivo: `2025_12_02_091602_add_expires_at_to_reservations_table.php`
- Campo agregado: `expires_at` (timestamp nullable)

### Modelo
- Campo agregado a `$fillable`: `'expires_at'`
- Cast agregado: `'expires_at' => 'datetime'`

## Notas Importantes

1. **Desarrollo**: Ejecutar `php artisan schedule:work` para probar el scheduler
2. **Producción**: Configurar cron/task scheduler como se indica arriba
3. **Logs**: Los comandos registran en `storage/logs/laravel.log`
4. **Testing**: Los comandos se pueden ejecutar manualmente para probar

## Archivos Modificados/Creados

### Nuevos archivos:
- `app/Mail/ReservationExpiringReminderMail.php`
- `app/Mail/ReservationExpiredMail.php`
- `app/Console/Commands/ExpirePendingReservations.php`
- `app/Console/Commands/SendExpirationReminders.php`
- `resources/views/emails/reservation_expiring_reminder.blade.php`
- `resources/views/emails/reservation_expired.blade.php`
- `database/migrations/2025_12_02_091602_add_expires_at_to_reservations_table.php`

### Modificados:
- `app/Models/Reservation.php` (fillable + cast)
- `app/Http/Controllers/ReservationController.php` (agregar expires_at al crear)
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` (agregar expires_at al crear)
- `app/Http/Controllers/Auth/ClientRegisterController.php` (agregar expires_at al crear)
- `app/Http/Controllers/PaymentController.php` (limpiar expires_at al pagar)
- `app/Http/Controllers/StripeController.php` (limpiar expires_at al pagar)
- `resources/views/emails/reservation_confirmed.blade.php` (agregar aviso de 24h)
- `routes/console.php` (programar comandos)
