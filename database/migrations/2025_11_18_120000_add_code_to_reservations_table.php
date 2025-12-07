<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;
use App\Models\Reservation;

// Se convierte esta migración en sólo BACKFILL de códigos (la columna ya existe por migración previa)
return new class extends Migration {
    public function up(): void
    {
        Reservation::whereNull('code')->orWhere('code', '')->chunkById(200, function ($chunk) {
            foreach ($chunk as $reservation) {
                $reservation->code = $this->generateCode();
                $reservation->save();
            }
        });
    }

    public function down(): void
    {
        
    }

    private function generateCode(): string
    {
        $prefix = 'SN-' . now()->format('Y') . '-';
        for ($i = 0; $i < 5; $i++) {
            $segment = Str::upper(Str::random(6));
            $code = $prefix . $segment;
            if (!Reservation::where('code', $code)->exists()) {
                return $code;
            }
        }
        return $prefix . Str::upper(Str::random(12));
    }
};
