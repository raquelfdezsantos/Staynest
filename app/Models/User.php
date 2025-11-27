<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo User.
 *
 * Representa a los usuarios del sistema, que pueden ser clientes o administradores.
 * Gestiona la autenticación, notificaciones y relaciones con sus reservas.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $phone
 * @property string|null $avatar_path
 * @property string|null $address
 * @property string|null $document_id
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $payment_method
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atributos que pueden asignarse masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar_path',
        'address',
        'document_id',
        'birth_date',
        'payment_method',
    ];

    /**
     * Atributos ocultos al serializar el modelo.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relación: un usuario admin puede tener varias propiedades.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Property>
     */
    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Conversión automática de atributos a tipos nativos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
