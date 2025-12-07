<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$reservations = \App\Models\Reservation::all();

echo "=== TODAS LAS RESERVAS ===\n";
foreach ($reservations as $r) {
    echo "Reserva {$r->code}: {$r->status} | {$r->check_in->format('Y-m-d')} → {$r->check_out->format('Y-m-d')} | Expira: " . ($r->expires_at ? $r->expires_at->format('Y-m-d H:i:s') : 'N/A') . "\n";
}
echo "\nTotal: " . $reservations->count() . " reservas\n";
