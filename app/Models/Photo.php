<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Photo.
 *
 * Gestiona las fotografías asociadas a una propiedad.
 * Cada foto pertenece a una única propiedad y contiene su ruta de almacenamiento.
 *
 * @property int $id
 * @property int $property_id
 * @property string $url
 * @property bool $is_cover
 * @property int $sort_order
 */
class Photo extends Model
{
    protected $fillable = [
        'property_id',
        'url',
        'is_cover',
        'sort_order',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    /**
     * Relación: una foto pertenece a una propiedad.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Property, Photo>
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
