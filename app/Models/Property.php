<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo que representa una propiedad.
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $address
 * @property string $city
 * @property string $postal_code
 * @property string $province
 * @property int $capacity
 * @property string $tourism_license
 * @property string $rental_registration
 * @property float $latitude
 * @property float $longitude
 * @property array $services
 * @property string $owner_name
 * @property string $owner_tax_id
 */
class Property extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Campos asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'address',
        'city',
        'postal_code',
        'province',
        'capacity',
        'tourism_license',
        'rental_registration',
        'latitude',
        'longitude',
        'services',
        'owner_name',
        'owner_tax_id',
    ];

    /**
     * Relación con el usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Property>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con las fotos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Photo>
     */
    public function photos()
    {
        return $this->hasMany(\App\Models\Photo::class);
    }

    /**
     * Relación con el calendario de tarifas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RateCalendar>
     */
    public function rateCalendar()
    {
        return $this->hasMany(RateCalendar::class, 'property_id');
    }

    /**
     * Relación con el entorno.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\PropertyEnvironment>
     */
    public function environment()
    {
        return $this->hasOne(PropertyEnvironment::class);
    }

    /**
     * Conversión automática de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'services' => 'array',
        ];
    }
}
