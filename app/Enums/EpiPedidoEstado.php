<?php

namespace App\Enums;

enum EpiPedidoEstado: string
{
    case Pendente = 'pendente';
    case SemStock = 'sem_stock';
    case Aprovado = 'aprovado';
    case Rejeitado = 'rejeitado';
    case Entregue = 'entregue';

    public function label(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::SemStock => 'Sem Stock',
            self::Aprovado => 'Aprovado',
            self::Rejeitado => 'Rejeitado',
            self::Entregue => 'Entregue',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pendente => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
            self::SemStock => 'bg-orange-500/10 text-orange-500 border-orange-500/20',
            self::Aprovado => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
            self::Rejeitado => 'bg-red-500/10 text-red-400 border-red-500/20',
            self::Entregue => 'bg-green-500/10 text-green-500 border-green-500/20',
        };
    }
}
