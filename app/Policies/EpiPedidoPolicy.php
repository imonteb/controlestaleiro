<?php

namespace App\Policies;

use App\Models\EpiPedido;
use App\Models\User;

/**
 * Pedidos de EPIs.
 * Admins e utilizadores epi gerem pedidos (aprovar, rejeitar, entregar).
 * Apenas admins podem eliminar pedidos.
 */
class EpiPedidoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEpi() || $user->isAdmin();
    }

    public function view(User $user, EpiPedido $epiPedido): bool
    {
        return $user->isEpi() || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isEpi() || $user->isAdmin();
    }

    public function update(User $user, EpiPedido $epiPedido): bool
    {
        return $user->isEpi() || $user->isAdmin();
    }

    public function delete(User $user, EpiPedido $epiPedido): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, EpiPedido $epiPedido): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, EpiPedido $epiPedido): bool
    {
        return $user->isSuperAdmin();
    }
}
