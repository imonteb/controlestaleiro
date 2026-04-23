<?php

namespace Database\Seeders;

use App\Models\EmergencyContact;
use Illuminate\Database\Seeder;

class EmergencyContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'nome' => 'Bombeiros Municipais do Funchal (Sapadores)',
                'telefone' => '291 222 122',
                'descricao' => 'Emergência Médica e Incêndios (Funchal)',
                'ordem' => 1,
                'logo' => 'emergency_logos/sapadores_funchal.png',
                'activo' => true,
            ],
            [
                'nome' => 'Bombeiros Voluntários Madeirenses',
                'telefone' => '291 223 056',
                'descricao' => 'Emergência Médica e Incêndios',
                'ordem' => 2,
                'logo' => 'emergency_logos/bv_madeirenses.png',
                'activo' => true,
            ],
            [
                'nome' => 'Proteção Civil Madeira',
                'telefone' => '291 700 110',
                'descricao' => 'Serviço Regional de Proteção Civil',
                'ordem' => 3,
                'logo' => 'emergency_logos/prociv_madeira.png',
                'activo' => true,
            ],
            [
                'nome' => 'Centro de Informação Antivenenos (CIAV)',
                'telefone' => '800 250 250',
                'descricao' => 'INEM - Linha Venenos',
                'ordem' => 4,
                'logo' => 'emergency_logos/ciav.png',
                'activo' => true,
            ],
            [
                'nome' => 'Hospital Dr. Nélio Mendonça (SESARAM)',
                'telefone' => '291 705 600',
                'descricao' => 'Urgências Hospitalares',
                'ordem' => 5,
                'logo' => 'emergency_logos/sesaram.png',
                'activo' => true,
            ],
            [
                'nome' => 'GNR – Comando Regional Madeira',
                'telefone' => '291 214 460',
                'descricao' => 'Guarda Nacional Republicana',
                'ordem' => 6,
                'logo' => 'emergency_logos/gnr.png',
                'activo' => true,
            ],
            [
                'nome' => 'PSP – Comando Regional Funchal',
                'telefone' => '291 208 400',
                'descricao' => 'Polícia de Segurança Pública',
                'ordem' => 7,
                'logo' => 'emergency_logos/psp.png',
                'activo' => true,
            ],
            [
                'nome' => 'Cruz Vermelha – Madeira',
                'telefone' => '291 741 115',
                'descricao' => 'Assistência e Emergência',
                'ordem' => 8,
                'logo' => 'emergency_logos/cruz_vermelha.png',
                'activo' => true,
            ],
            [
                'nome' => 'EEM - Electricidade da Madeira',
                'telefone' => '291 211 300',
                'descricao' => 'Avarias / Urgência Elétrica',
                'ordem' => 9,
                'logo' => 'emergency_logos/eem.png',
                'activo' => true,
            ],
            [
                'nome' => 'ARM – Águas e Resíduos da Madeira',
                'telefone' => '291 201 020',
                'descricao' => 'Avarias / Redes de Água',
                'ordem' => 10,
                'logo' => 'emergency_logos/arm.png',
                'activo' => true,
            ],
            [
                'nome' => 'Gás Insular',
                'telefone' => '291 922 223',
                'descricao' => 'Urgência Gás',
                'ordem' => 11,
                'logo' => 'emergency_logos/gas_insular.png',
                'activo' => true,
            ],
            [
                'nome' => 'Direção Regional do Trabalho',
                'telefone' => '291 214 780',
                'descricao' => 'Apoio e Inspeção',
                'ordem' => 12,
                'logo' => 'emergency_logos/direcao_trabalho.png',
                'activo' => true,
            ],
            [
                'nome' => 'Câmara Municipal do Funchal',
                'telefone' => '291 211 000',
                'descricao' => 'Serviços Municipais',
                'ordem' => 13,
                'logo' => 'emergency_logos/cmf.png',
                'activo' => true,
            ],
            [
                'nome' => 'Socorristas: Duarte Luz / Dário Freitas',
                'telefone' => '291 765 047',
                'descricao' => 'Equipa Interna de Socorro',
                'ordem' => 14,
                'logo' => 'emergency_logos/socorrista.png',
                'activo' => true,
            ],
            [
                'nome' => 'Farmácia – Penteada',
                'telefone' => '291 631 591',
                'descricao' => 'Serviços Farmacêuticos Próximos',
                'ordem' => 15,
                'logo' => 'emergency_logos/farmacia.png',
                'activo' => true,
            ],
        ];

        foreach ($contacts as $contact) {
            EmergencyContact::updateOrCreate(
                ['nome' => $contact['nome']],
                $contact
            );
        }
    }
}
