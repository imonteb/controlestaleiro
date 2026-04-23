<?php

namespace App\Policies;

use App\Models\EpiEntrega;
use App\Models\User;

/**
 * Entregas de EPIs.
 * Admins e utilizadores epi gerem as entregas.
 * Apenas admins podem eliminar registos.
 */
class EpiEntregaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEpi() || $user->isAdmin();
    }

    public function view(User $user, EpiEntrega $epiEntrega): bool
    {
        return $user->isEpi() || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isEpi() || $user->isAdmin();
    }

    public function update(User $user, EpiEntrega $epiEntrega): bool
    {
        return $user->isEpi() || $user->isAdmin();
    }

    public function delete(User $user, EpiEntrega $epiEntrega): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, EpiEntrega $epiEntrega): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, EpiEntrega $epiEntrega): bool
    {
        return $user->isSuperAdmin();
    }
}
