<?php

namespace App\Console\Commands;

use App\Models\EpiItem;
use Illuminate\Console\Command;

class EnrichEpiDatabase extends Command
{
    protected $signature = 'epi:enrich-database';

    protected $description = 'Enriches the EPI database based on heuristic keywords parsing of the names';

    public function handle()
    {
        $items = EpiItem::all();
        $this->info("Enriching {$items->count()} EPI items...");

        $enrichedCount = 0;

        foreach ($items as $item) {
            $name = strtolower($item->nombre);

            // Default assumes
            $tipo = 'individual';
            $unidade = 'unidade';
            $requiereTalla = false;
            $tallasDisponibles = null;
            $camposPersonalizados = [
                ['nombre' => 'Marca', 'tipo' => 'text', 'requerido' => false],
                ['nombre' => 'Lote', 'tipo' => 'text', 'requerido' => false],
            ];
            $riscos = [];
            $descripcion = '';

            // Safety Rules (Industrial Standards)

            // FOOTWEAR
            if (str_contains($name, 'bota') || str_contains($name, 'calcado') || str_contains($name, 'sapato')) {
                $unidade = 'par';
                $requiereTalla = true;
                $tallasDisponibles = ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47'];
                $riscos = ['Esmagamento', 'Queda ao mesmo nível', 'Cortes e perfurações'];
                $descripcion = 'Calçado de segurança com protecção mecânica contra esmagamento e perfuração.';
                if (str_contains($name, 'pvc') || str_contains($name, 'borracha')) {
                    $riscos[] = 'Imersões';
                    $riscos[] = 'Salpicos e projecções';
                }
            }
            // GLOVES
            elseif (str_contains($name, 'luva') || str_contains($name, 'luvas')) {
                $unidade = 'par';
                $requiereTalla = true;
                $tallasDisponibles = ['7', '8', '9', '10', '11'];
                if (str_contains($name, 'quimic') || str_contains($name, 'nitrilo') || str_contains($name, 'pvc')) {
                    $riscos = ['Salpicos e projecções', 'Substâncias Irritantes / corrosivas'];
                    $descripcion = 'Luvas de proteção química impermeáveis.';
                } elseif (str_contains($name, 'dielectric') || str_contains($name, 'isolant')) {
                    $riscos = ['Contacto directo com a electricidade'];
                    $descripcion = 'Luvas dielétricas para trabalhos em tensão elétrica.';
                } elseif (str_contains($name, 'sold') || str_contains($name, 'termic')) {
                    $riscos = ['Queimadura', 'Entalamento'];
                    $descripcion = 'Luvas de soldador com resistência térmica.';
                } else {
                    $riscos = ['Cortes e perfurações', 'Entalamento'];
                    $descripcion = 'Luvas de proteção mecânica e anti-corte.';
                }
            }
            // HEAD
            elseif (str_contains($name, 'capacete') || str_contains($name, 'toca')) {
                $riscos = ['Choques, pancadas e compressões', 'Queda de objectos'];
                $descripcion = 'Capacete de proteção craniana contra impacto.';
            }
            // EYES & FACE
            elseif (str_contains($name, 'oculo') || str_contains($name, 'óculo') || str_contains($name, 'viseira')) {
                $riscos = ['Projecção de partículas, objectos'];
                $descripcion = 'Proteção ocular contra projecções sólidas e faíscas.';
                if (str_contains($name, 'solda')) {
                    $riscos[] = 'Queimadura';
                    $riscos[] = 'Radiações não ionizantes';
                }
            }
            // RESPIRATORY
            elseif (str_contains($name, 'mascar') || str_contains($name, 'máscar') || str_contains($name, 'filtro')) {
                if (str_contains($name, 'filtro')) {
                    $descripcion = 'Filtro respiratório de substituição.';
                } else {
                    $descripcion = 'Proteção das vias respiratórias.';
                }
                if (str_contains($name, 'gas') || str_contains($name, 'gás') || str_contains($name, 'vapor')) {
                    $riscos = ['Gases e vapores'];
                } elseif (str_contains($name, 'solda')) {
                    $riscos = ['Fumos', 'Poeiras'];
                } else {
                    $riscos = ['Poeiras'];
                }
            }
            // HEARING
            elseif (str_contains($name, 'tamp') || str_contains($name, 'abafador') || str_contains($name, 'auricular')) {
                $riscos = ['Ruído'];
                $descripcion = 'Proteção auditiva para atenuação acústica.';
                if (str_contains($name, 'tamp')) {
                    $unidade = 'par';
                }
            }
            // FALLS
            elseif (str_contains($name, 'arnes') || str_contains($name, 'arnês') || str_contains($name, 'linha') || str_contains($name, 'mosqueta') || str_contains($name, 'cinta') || str_contains($name, 'cordal')) {
                $riscos = ['Queda em altura'];
                $descripcion = 'Equipamento e acessórios para sistemas anti-queda em altura.';
            }
            // BODY (HIGH VIS & CLOTHING)
            elseif (str_contains($name, 'fato') || str_contains($name, 'casaco') || str_contains($name, 'calca') || str_contains($name, 'calça') || str_contains($name, 'colete') || str_contains($name, 'blusao') || str_contains($name, 't-shirt')) {
                $requiereTalla = true;
                $tallasDisponibles = ['S', 'M', 'L', 'XL', 'XXL', '3XL'];

                // HIGH VIS / REFLECTIVE (Extremely important distinction)
                if (str_contains($name, 'alta visi') || str_contains($name, 'reflec') || str_contains($name, 'colete')) {
                    $riscos[] = 'Atropelamento';
                    $descripcion = 'Vestuário de alta visibilidade classe 2/3 para garantir o destacamento visual.';
                }

                // THERMAL/WEATHER
                if (str_contains($name, 'frio') || str_contains($name, 'inverno') || str_contains($name, 'termic') || str_contains($name, 'imperme')) {
                    $riscos[] = 'Frio';
                    $descripcion .= ' Possui barreira térmica ou impermeável.';
                }

                // CHEMICAL / TYVEK
                if (str_contains($name, 'tyvek') || str_contains($name, 'quimic') || str_contains($name, 'descatav') || str_contains($name, 'descartáv')) {
                    $riscos = ['Poeiras', 'Salpicos e projecções', 'Substâncias Cancerígenas / Amianto'];
                    $descripcion = 'Fato de proteção integral contra particulas nocivas e salpicos químicos.';
                }

                // WELDING
                if (str_contains($name, 'soldador') || str_contains($name, 'ignifugo') || str_contains($name, 'flama')) {
                    $riscos = ['Queimadura', 'Projecção de partículas, objectos'];
                    $descripcion = 'Vestuário resistente à chama para soldadura e risco de fogo.';
                }

                // General clothing fallback if completely empty
                if (empty($riscos)) {
                    $descripcion = 'Vestuário geral de trabalho e proteção da higiene.';
                    $riscos = ['Salpicos e projecções', 'Cortes e perfurações'];
                }
            }
            // EPC / COLLECTIVE
            elseif (str_contains($name, 'sinal') || str_contains($name, 'cone') || str_contains($name, 'barreira') || str_contains($name, 'veda') || str_contains($name, 'fita') || str_contains($name, 'extintor') || str_contains($name, 'placa')) {
                $tipo = 'coletivo';
                $camposPersonalizados = []; // Sem campos personalizados
                $riscos = ['Atropelamento', 'Queda ao mesmo nível'];
                $descripcion = 'Equipamentos de Proteção Coletiva (EPC) e Sinalização Temporária.';
                if (str_contains($name, 'extintor')) {
                    $riscos = ['Queimadura'];
                    $descripcion = 'Aparelho de prevenção e extinção de incêndios.';
                }
            }

            // Only update fields if they were completely blank or standard
            if (! $item->tipo || $item->tipo === 'individual' && $tipo === 'coletivo') {
                $item->tipo = $tipo;
            }
            if (! $item->unidade) {
                $item->unidade = $unidade;
            }
            $item->requiere_talla = $requiereTalla;
            if ($requiereTalla && empty($item->tallas_disponibles)) {
                $item->tallas_disponibles = $tallasDisponibles;
            }
            if (empty($item->campos_personalizados) && $tipo === 'individual') {
                $item->campos_personalizados = $camposPersonalizados;
            }
            // Always fix risks as long as we mapped them
            if (! empty($riscos)) {
                $item->riscos = $riscos;
            }
            if (! empty($descripcion)) {
                $item->descripcion = $descripcion;
            }

            $item->saveQuietly();
            $enrichedCount++;
        }

        $this->info("Database successfully heuristically enriched. Processed {$enrichedCount} items.");
    }
}
