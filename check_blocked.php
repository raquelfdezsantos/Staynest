<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$reservations = \App\Models\Reservation::whereIn('status', ['pending', 'paid'])->get();

echo "=== RESERVAS ACTIVAS ===\n";
$totalNights = 0;
foreach ($reservations as $r) {
    $nights = $r->check_in->diffInDays($r->check_out);
    $totalNights += $nights;
    echo "Reserva {$r->code}: {$r->check_in->format('Y-m-d')} → {$r->check_out->format('Y-m-d')} ({$nights} noches)\n";
}
echo "\nTotal noches a bloquear: {$totalNights}\n";

$blocked = \App\Models\RateCalendar::where('is_available', false)->count();
echo "Noches bloqueadas en RateCalendar: {$blocked}\n\n";

if ($blocked < $totalNights) {
    echo "ERROR: Hay menos noches bloqueadas de las esperadas!\n";
} else {
    echo "OK: Bloqueo correcto\n";
}
