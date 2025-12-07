<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo PropertyEnvironment.
 *
 * Representa la información del entorno de una propiedad turística.
 * Incluye descripción, fotos y detalles sobre naturaleza, cultura, actividades y servicios.
 *
 * @property int $id
 * @property int $property_id
 * @property string|null $title
 * @property string|null $subtitle
 * @property string|null $hero_photo
 * @property string|null $summary
 * @property string|null $nature_description
 * @property string|null $nature_photo
 * @property string|null $culture_description
 * @property string|null $culture_photo
 * @property string|null $activities_description
 * @property string|null $activities_photo
 * @property string|null $services_description
 * @property string|null $services_photo
 */
class PropertyEnvironment extends Model
{
    use HasFactory;

    /**
     * Campos asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'property_id',
        'title',
        'subtitle',
        'hero_photo',
        'summary',
        'nature_description',
        'nature_photo',
        'culture_description',
        'culture_photo',
        'activities_description',
        'activities_photo',
        'services_description',
        'services_photo',
    ];

    /**
     * Relación: un entorno pertenece a una propiedad.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Property, PropertyEnvironment>
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
