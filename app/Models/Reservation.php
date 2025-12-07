<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa una reserva.
 *
 * @property \Illuminate\Support\Carbon $check_in
 * @property \Illuminate\Support\Carbon $check_out
 * @property int $user_id
 * @property int $property_id
 * @property int $guests
 * @property string $status
 * @property float $total_price
 *
 * @mixin \Eloquent
 */
class Reservation extends Model
{
    use HasFactory;
    /**
     * Atributos que pueden asignarse de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'property_id',
        'check_in',
        'check_out',
        'code',
        'guests',
        'adults',
        'children',
        'pets',
        'notes',
        'status',
        'total_price',
        'expires_at'
    ];

    /**
     * Conversión automática de atributos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in'  => 'date',
        'check_out' => 'date',
        'expires_at' => 'datetime',
    ];

    /**
     * Relación con la propiedad.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Property, \App\Models\Reservation>
     */
    public function property()
    {
        return $this->belongsTo(\App\Models\Property::class);
    }

    /**
     * Relación con el usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Reservation>
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Relación con la factura.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Invoice>
     */
    public function invoice()
    {
        return $this->hasOne(\App\Models\Invoice::class);
    }

    /**
     * Relación con las facturas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Invoice>
     */
    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class);
    }

    /**
     * Relación con los pagos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Payment>
     */
    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }


    /**
     * Calcula el total pagado neto.
     *
     * @return float Total pagado neto
     */
    public function paidAmount(): float
    {
        // Si la relación está cargada, usarla; si no, hacer query
        if ($this->relationLoaded('payments')) {
            $paid = $this->payments->where('status', 'succeeded')->sum('amount');
            $refunded = $this->payments->where('status', 'refunded')->sum('amount');
            return (float)($paid + $refunded); // refunds son negativos
        }
        
        $paid = (float) $this->payments()->where('status', 'succeeded')->sum('amount');
        $refunded = (float) $this->payments()->where('status', 'refunded')->sum('amount');
        return $paid + $refunded; // refunds son negativos
    }

    /**
     * Calcula el total reembolsado.
     *
     * @return float Monto total reembolsado
     */
    public function refundedAmount(): float
    {
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->where('status', 'refunded')->sum('amount');
        }
        
        return (float) $this->payments()
            ->where('status', 'refunded')
            ->sum('amount');
    }

    /**
     * Calcula el saldo pendiente de pago.
     *
     * @return float Monto pendiente de pago
     */
    public function balanceDue(): float
    {
        // Lo que falta por cobrar si total > pagado; 0 si no.
        return max(0, (float)$this->total_price - $this->paidAmount());
    }

    /**
     * Calcula el exceso pagado.
     *
     * @return float Monto pagado en exceso
     */
    public function overpaid(): float
    {
        // Exceso pagado (para detectar devoluciones); 0 si no.
        return max(0, $this->paidAmount() - (float)$this->total_price);
    }

    /**
     * Calcula el porcentaje de reembolso por cancelación.
     *
     * @return int Porcentaje de reembolso
     */
    public function cancellationRefundPercent(): int
    {
        if ($this->status !== 'paid') return 0;
        $days = now()->diffInDays($this->check_in, false);
        if ($days < 0) return 0; // check-in pasado
        $policy = config('reservations.cancellation_policy', []);
        foreach ($policy as $tier) {
            if ($days >= ($tier['min_days'] ?? 0)) {
                return (int) ($tier['percent'] ?? 0);
            }
        }
        return 0;
    }
}
