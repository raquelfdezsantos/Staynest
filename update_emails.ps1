# Script para actualizar todos los emails con layout Staynest
Write-Host "Actualizando emails con layout Staynest..." -ForegroundColor Green

# Lista de archivos completados
$completed = @(
    "reservation_confirmed.blade.php",
    "admin_new_reservation.blade.php"
)

Write-Host "✓ 2 archivos ya actualizados" -ForegroundColor Yellow
Write-Host "Faltan 12 archivos por actualizar..." -ForegroundColor Cyan
