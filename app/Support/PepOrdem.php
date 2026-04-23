<?php

namespace App\Support;

/**
 * Ordem de exibição dos PEPs nos painéis (TV, PC, Mobile).
 * Os PEPs cujo ID não esteja nesta lista são adicionados ao final,
 * ordenados pelo seu ID.
 */
class PepOrdem
{
    const PREDEFINIDA = [1, 3, 7, 12, 2, 18, 20, 13, 6, 4, 8, 14, 21, 5, 9, 15, 10, 19];
}
