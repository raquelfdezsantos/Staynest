<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa un pago.
 *
 * @property int $reservation_id
 * @property float $amount
 * @property string $method
 * @property string $status
 * @property string $provider_ref
 */
class Payment extends Model
{
    use HasFactory;
    /**
     * Atributos asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reservation_id',
        'amount',
        'method',
        'status',
        'provider_ref',
    ];

    /**
     * Relación con la reserva.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Reservation, \App\Models\Payment>
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}