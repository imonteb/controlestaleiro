<?php

namespace App\Policies;

use App\Models\EpiItem;
use App\Models\User;

/**
 * Catálogo de EPIs.
 * Apenas admins gerem o catálogo (criar/editar/eliminar).
 * Utilizadores com role epi podem apenas consultar.
 */
class EpiItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEpi() || $user->isAdmin();
    }

    public function view(User $user, EpiItem $epiItem): bool
    {
        return $user->isEpi() || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, EpiItem $epiItem): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, EpiItem $epiItem): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, EpiItem $epiItem): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, EpiItem $epiItem): bool
    {
        return $user->isSuperAdmin();
    }
}
