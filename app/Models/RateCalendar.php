<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo RateCalendar.
 *
 * Representa el calendario de tarifas y disponibilidad de una propiedad.
 * Define el precio por noche, la estancia mínima y el estado de reserva de cada fecha.
 *
 * @property int $id
 * @property int $property_id
 * @property \Illuminate\Support\Carbon $date
 * @property float $price
 * @property bool $is_available
 * @property int $min_stay
 * @property string|null $blocked_by
 */
class RateCalendar extends Model
{
    use HasFactory;

    // Claves naturales: (property_id, date)
    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'date',
        'price',
        'is_available',
        'min_stay',
        'blocked_by',
    ];

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'min_stay' => 'integer',
    ];
}
