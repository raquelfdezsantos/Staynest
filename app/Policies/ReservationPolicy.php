<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy para reservas.
 */
class ReservationPolicy
{
    /**
     * Determina si puede ver el listado de reservas.
     *
     * @param \App\Models\User $user Usuario autenticado
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determina si puede ver una reserva específica.
     *
     * @param \App\Models\User $user Usuario autenticado
     * @param \App\Models\Reservation $r Reserva a consultar
     * @return bool
     */
    public function view(User $user, Reservation $r): bool
    {
        return $user->id === $r->user_id || $user->role === 'admin';
    }

    /**
     * Determina si puede crear una reserva.
     *
     * @param \App\Models\User $user Usuario autenticado
     * @return bool
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determina si puede actualizar una reserva.
     *
     * @param \App\Models\User $user Usuario autenticado
     * @param \App\Models\Reservation $r Reserva a actualizar
     * @return bool
     */
    public function update(User $user, Reservation $r): bool
    {
        return $user->id === $r->user_id || $user->role === 'admin';
    }

    /**
     * Determina si puede eliminar una reserva.
     *
     * @param \App\Models\User $user Usuario autenticado
     * @param \App\Models\Reservation $reservation Reserva a eliminar
     * @return bool
     */
    public function delete(User $user, Reservation $reservation): bool
    {
        return false;
    }

    /**
     * Determina si puede cancelar una reserva.
     *
     * @param \App\Models\User $user Usuario autenticado
     * @param \App\Models\Reservation $r Reserva a cancelar
     * @return bool
     */
    public function cancel(User $user, Reservation $r): bool
    {
        return $user->id === $r->user_id || $user->role === 'admin';
    }

    /**
     * Determina si puede pagar una reserva.
     *
     * @param \App\Models\User $user Usuario autenticado
     * @param \App\Models\Reservation $r Reserva a pagar
     * @return bool
     */
    public function pay(User $user, Reservation $r): bool
    {
        return $user->id === $r->user_id || $user->role === 'admin';
    }
    
    /**
     * Determina si puede restaurar una reserva eliminada.
     *
     * @param \App\Models\User $user Usuario autenticado
     * @param \App\Models\Reservation $reservation Reserva a restaurar
     * @return bool
     */
    public function restore(User $user, Reservation $reservation): bool
    {
        return false;
    }

    /**
     * Determina si puede eliminar permanentemente una reserva.
     *
     * @param \App\Models\User $user Usuario autenticado
     * @param \App\Models\Reservation $reservation Reserva a eliminar permanentemente
     * @return bool
     */
    public function forceDelete(User $user, Reservation $reservation): bool
    {
        return false;
    }
}
