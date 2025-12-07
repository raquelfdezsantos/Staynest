<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa una factura.
 *
 * @property int $id
 * @property int $reservation_id
 * @property string $number
 * @property string $pdf_path
 * @property \Illuminate\Support\Carbon $issued_at
 * @property float $amount
 */
class Invoice extends Model
{
    use HasFactory;
    /**
     * Atributos asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reservation_id',
        'number',
        'pdf_path',
        'issued_at',
        'amount',
        'details',
    ];

    /**
     * Conversión automática de atributos.
     *
     * @var array<int, string>
     */
    protected $casts = [
        'issued_at' => 'datetime',
        'amount'    => 'decimal:2',
        'details'   => 'array',
    ];

    /**
     * Relación con la reserva.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Reservation, \App\Models\Invoice>
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Genera número único de factura.
     *
     * @param string $prefix Prefijo
     * @return string Número único
     */
    public static function generateUniqueNumber(string $prefix = 'INV'): string
    {
        $year = now()->year;
        
        // Obtener el último número usado para este año y prefijo
        $lastInvoice = static::where('number', 'like', "{$prefix}-{$year}-%")
            ->orderByRaw('CAST(SUBSTRING(number, ' . (strlen($prefix) + strlen($year) + 3) . ') AS UNSIGNED) DESC')
            ->first();
        
        if ($lastInvoice) {
            // Extraer el número secuencial del último registro
            $lastNumber = (int) substr($lastInvoice->number, strlen($prefix) + strlen($year) + 2);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . '-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
