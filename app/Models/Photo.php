<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa una foto de propiedad.
 *
 * @property int $id
 * @property int $property_id
 * @property string $url
 * @property bool $is_cover
 * @property int $sort_order
 */
class Photo extends Model
{
    /**
     * Atributos asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'property_id',
        'url',
        'is_cover',
        'sort_order',
    ];

    /**
     * Conversión automática de atributos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_cover' => 'boolean',
    ];

    /**
     * Relación con la propiedad.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Property, \App\Models\Photo>
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
