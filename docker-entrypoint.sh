#!/bin/bash
set -e

echo "Iniciando Staynest..."

# Esperar a que la base de datos esté lista
echo "Esperando a MySQL..."
until php artisan db:show 2>/dev/null; do
  sleep 2
done

echo "Base de datos conectada"

# Generar clave de aplicación si no existe
if [ ! -f .env ]; then
  echo "Creando archivo .env..."
  cp .env.example .env
  php artisan key:generate
fi

# Ejecutar migraciones y seeders
echo "Ejecutando migraciones y seeders..."
# Recrear DB completa con datos de demo en cada arranque
php artisan migrate:fresh --seed --force || true

# Asegurar permisos correctos
echo "Configurando permisos..."
chmod -R 777 storage bootstrap/cache

# Crear enlace simbólico de storage
echo "Creando enlace de storage..."
php artisan storage:link || true

# Limpiar caché
echo "Limpiando caché..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Staynest listo en http://localhost:8000"
echo ""
echo "Usuarios de demostración:"
echo "   Admin: raquel@staynest.com / password"
echo "   Cliente: laura@example.com / password"
echo ""

# Ejecutar el comando principal
exec "$@"
