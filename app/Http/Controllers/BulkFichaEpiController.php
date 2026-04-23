<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\EpiEntrega;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BulkFichaEpiController extends Controller
{
    /**
     * Riscos numbered exactly as in the official PDF form (1-23).
     */
    private const RISCOS_NUMERADOS = [
        'Entalamento' => 1,
        'Esmagamento' => 2,
        'Choques, pancadas e compressões' => 3,
        'Queda em altura' => 4,
        'Queda ao mesmo nível' => 5,
        'Queda de objectos' => 6,
        'Projecção de partículas' => 7,
        'Projeção de partículas' => 7,
        'Projecção de partículas, objectos' => 7,
        'Cortes e perfurações' => 8,
        'Contacto directo com a electricidade' => 9,
        'Riscos elétricos' => 9,
        'Ruído' => 10,
        'Vibrações' => 11,
        'Frio' => 12,
        'Riscos térmicos' => 12,
        'Queimadura' => 13,
        'Radiações ionizantes' => 14,
        'Radiações não ionizantes' => 15,
        'Poeiras' => 16,
        'Gases e vapores' => 17,
        'Produtos químicos' => 17,
        'Fumos' => 18,
        'Imersões' => 19,
        'Salpicos e projecções' => 20,
        'Atropelamento' => 21,
        'Substâncias Cancerígenas / Amianto' => 22,
        'Riscos biológicos' => 22,
        'Substâncias Irritantes / corrosivas' => 23,
        'Riscos mecânicos' => 3,
    ];

    public function __invoke(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Encontrar todos os IDs de colaboradores que tiveram entregas nesse mês/ano
        $colaboradorIds = EpiEntrega::whereMonth('fecha_entrega', $month)
            ->whereYear('fecha_entrega', $year)
            ->distinct()
            ->pluck('colaborador_id');

        $colaboradoresData = [];

        foreach ($colaboradorIds as $id) {
            $colaborador = Colaborador::find($id);
            if (! $colaborador) {
                continue;
            }

            $entregas = EpiEntrega::with(['epiItem', 'entregadoPor'])
                ->where('colaborador_id', $id)
                ->whereMonth('fecha_entrega', $month)
                ->whereYear('fecha_entrega', $year)
                ->orderBy('fecha_entrega')
                ->get();

            if ($entregas->isNotEmpty()) {
                $colaboradoresData[] = [
                    'colaborador' => $colaborador,
                    'entregas' => $entregas,
                ];
            }
        }

        // Ordenar colaboradores por nome
        usort($colaboradoresData, function ($a, $b) {
            return strcasecmp($a['colaborador']->nombre, $b['colaborador']->nombre);
        });

        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        return view('epis.bulk-ficha-print', [
            'colaboradoresData' => $colaboradoresData,
            'riscosMap' => self::RISCOS_NUMERADOS,
            'monthName' => $monthName,
            'month' => $month,
            'year' => $year,
        ]);
    }
}
