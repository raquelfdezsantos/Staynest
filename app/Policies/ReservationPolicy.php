<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy ReservationPolicy
 *
 * Define las reglas de autorización para acciones sobre reservas.
 */
class ReservationPolicy
{
    /**
     * Determina si el usuario puede ver el listado de reservas.
     *
     * @param User $user Usuario autenticado
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede ver una reserva específica.
     *
     * @param User $user Usuario autenticado
     * @param Reservation $r Reserva a consultar
     * @return bool
     */
    public function view(User $user, Reservation $r): bool
    {
        return $user->id === $r->user_id || $user->role === 'admin';
    }

    /**
     * Determina si el usuario puede crear una reserva.
     *
     * @param User $user Usuario autenticado
     * @return bool
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede actualizar una reserva.
     *
     * @param User $user Usuario autenticado
     * @param Reservation $r Reserva a actualizar
     * @return bool
     */
    public function update(User $user, Reservation $r): bool
    {
        return $user->id === $r->user_id || $user->role === 'admin';
    }

    /**
     * Determina si el usuario puede eliminar una reserva.
     *
     * @param User $user Usuario autenticado
     * @param Reservation $reservation Reserva a eliminar
     * @return bool
     */
    public function delete(User $user, Reservation $reservation): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede cancelar una reserva.
     *
     * @param User $user Usuario autenticado
     * @param Reservation $r Reserva a cancelar
     * @return bool
     */
    public function cancel(User $user, Reservation $r): bool
    {
        return $user->id === $r->user_id || $user->role === 'admin';
    }

    /**
     * Determina si el usuario puede pagar una reserva.
     *
     * @param User $user Usuario autenticado
     * @param Reservation $r Reserva a pagar
     * @return bool
     */
    public function pay(User $user, Reservation $r): bool
    {
        return $user->id === $r->user_id || $user->role === 'admin';
    }
    
    /**
     * Determina si el usuario puede restaurar una reserva eliminada.
     *
     * @param User $user Usuario autenticado
     * @param Reservation $reservation Reserva a restaurar
     * @return bool
     */
    public function restore(User $user, Reservation $reservation): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede eliminar permanentemente una reserva.
     *
     * @param User $user Usuario autenticado
     * @param Reservation $reservation Reserva a eliminar permanentemente
     * @return bool
     */
    public function forceDelete(User $user, Reservation $reservation): bool
    {
        return false;
    }
}
