<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class LegalVarsService
{
    private const FILE = 'legal-vars.json';

    public static array $defaults = [
        'empresa_nome' => 'Construção e Manutenção Electromecânica S.A.',
        'empresa_nif' => '501 369 295',
        'empresa_morada' => 'Lagoas Park, Edifício 11, Piso 0, 2740-270 Porto Salvo',
        'empresa_email' => 'cme@cme.pt',
        'empresa_unidade' => 'C016',
        'app_nome' => 'ControlEstaleiro',
        'dpo_nome' => '',
        'dpo_email' => '',
        'hosting_entidade' => 'Hostinger',
        'hosting_pais' => 'Lituânia (União Europeia)',
    ];

    public static array $labels = [
        'empresa_nome' => 'Nome da Empresa',
        'empresa_nif' => 'NIF',
        'empresa_morada' => 'Morada',
        'empresa_email' => 'Email de Privacidade',
        'empresa_unidade' => 'Unidade / Centro de Custo',
        'app_nome' => 'Nome da Aplicação',
        'dpo_nome' => 'Nome do DPO (deixar vazio se não aplicável)',
        'dpo_email' => 'Email do DPO (deixar vazio se não aplicável)',
        'hosting_entidade' => 'Fornecedor de Alojamento',
        'hosting_pais' => 'País do Servidor',
    ];

    public static function all(): array
    {
        if (! Storage::exists(self::FILE)) {
            return self::$defaults;
        }

        $stored = json_decode(Storage::get(self::FILE), true) ?? [];

        return array_merge(self::$defaults, $stored);
    }

    public static function save(array $vars): void
    {
        $clean = array_intersect_key($vars, self::$defaults);
        Storage::put(self::FILE, json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public static function replace(string $content): string
    {
        $vars = self::all();
        foreach ($vars as $key => $value) {
            $content = str_replace('{'.$key.'}', (string) $value, $content);
        }

        return $content;
    }
}
