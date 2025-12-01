<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Invoice.
 *
 * Representa la factura generada tras el pago simulado de una reserva.
 * Incluye número de factura, importe total, fecha de emisión y relación con la reserva.
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
     * Resumen de los atributos que pueden asignarse de forma masiva.
     * 
     * @var array <int, string>
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
     * Conversión automática de atributos a tipos nativos.
     * 
     * @var array <int, string>
     */
    protected $casts = [
        'issued_at' => 'datetime',
        'amount'    => 'decimal:2',
        'details'   => 'array',
    ];

    /**
     * Relación: una factura pertenece a una reserva.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Reservation, Invoice>
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Genera un número de factura único.
     * 
     * @param string $prefix Prefijo del número de factura (INV, RECT, REFUND)
     * @return string Número de factura único
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
