<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa el calendario de tarifas.
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

    /**
     * Atributos asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'property_id',
        'date',
        'price',
        'is_available',
        'min_stay',
        'blocked_by',
    ];

    /**
     * Conversión automática de atributos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'min_stay' => 'integer',
    ];
}
