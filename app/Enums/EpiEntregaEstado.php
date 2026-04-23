<?php

namespace App\Enums;

enum EpiEntregaEstado: string
{
    case Entregue = 'entregue';
    case Devolvido = 'devolvido';
    case Danificado = 'danificado';
    case Perdido = 'perdido';

    public function label(): string
    {
        return match ($this) {
            self::Entregue => 'Entregue',
            self::Devolvido => 'Devolvido',
            self::Danificado => 'Danificado',
            self::Perdido => 'Perdido',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Entregue => 'bg-blue-50 text-blue-700 border-blue-100',
            self::Devolvido => 'bg-gray-50 text-gray-600 border-gray-200',
            self::Danificado => 'bg-orange-50 text-orange-700 border-orange-100',
            self::Perdido => 'bg-red-50 text-red-700 border-red-100',
        };
    }

    /** Estados que reduzem o stock disponível (saída do armazém). */
    public function contaComoSaida(): bool
    {
        return match ($this) {
            self::Entregue, self::Danificado, self::Perdido => true,
            self::Devolvido => false,
        };
    }

    /** @return list<self> */
    public static function comSaida(): array
    {
        return [self::Entregue, self::Danificado, self::Perdido];
    }

    /** @return list<string> */
    public static function valoresComSaida(): array
    {
        return array_map(fn (self $e) => $e->value, self::comSaida());
    }
}
