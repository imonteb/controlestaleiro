<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\EpiEntrega;
use Carbon\Carbon;

class FichaEpiController extends Controller
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
        'Projecção de partículas' => 7,   // alias
        'Projeção de partículas' => 7,   // alias (modern spelling)
        'Projecção de partículas, objectos' => 7,
        'Cortes e perfurações' => 8,
        'Contacto directo com a electricidade' => 9,
        'Riscos elétricos' => 9,   // alias
        'Ruído' => 10,
        'Vibrações' => 11,
        'Frio' => 12,
        'Riscos térmicos' => 12,  // alias → Frio / Queimadura
        'Queimadura' => 13,
        'Radiações ionizantes' => 14,
        'Radiações não ionizantes' => 15,
        'Poeiras' => 16,
        'Gases e vapores' => 17,
        'Produtos químicos' => 17,  // alias
        'Fumos' => 18,
        'Imersões' => 19,
        'Salpicos e projecções' => 20,
        'Atropelamento' => 21,
        'Substâncias Cancerígenas / Amianto' => 22,
        'Riscos biológicos' => 22,  // alias
        'Substâncias Irritantes / corrosivas' => 23,
        'Riscos mecânicos' => 3,   // alias → Choques
    ];

    public function __invoke(Colaborador $colaborador)
    {
        $month = request('month');
        $year = request('year');

        // If a specific month/year is requested, show only that month
        if ($month && $year) {
            $entregas = EpiEntrega::with(['epiItem', 'entregadoPor'])
                ->where('colaborador_id', $colaborador->id)
                ->whereMonth('fecha_entrega', $month)
                ->whereYear('fecha_entrega', $year)
                ->orderBy('fecha_entrega')
                ->get();

            $mesLabel = Carbon::createFromDate($year, $month, 1)->locale('pt')->isoFormat('MMMM YYYY');

            return view('epis.ficha-print', [
                'colaborador' => $colaborador,
                'entregas' => $entregas,
                'riscosMap' => self::RISCOS_NUMERADOS,
                'mesLabel' => $mesLabel,
                'month' => $month,
                'year' => $year,
            ]);
        }

        // No filter: get distinct month/year combinations
        $meses = EpiEntrega::where('colaborador_id', $colaborador->id)
            ->selectRaw('MONTH(fecha_entrega) as mes, YEAR(fecha_entrega) as ano, COUNT(*) as total')
            ->whereNotNull('fecha_entrega')
            ->groupByRaw('YEAR(fecha_entrega), MONTH(fecha_entrega)')
            ->orderByRaw('YEAR(fecha_entrega) DESC, MONTH(fecha_entrega) DESC')
            ->get()
            ->map(fn ($r) => [
                'mes' => $r->mes,
                'ano' => $r->ano,
                'total' => $r->total,
                'label' => Carbon::createFromDate($r->ano, $r->mes, 1)->locale('pt')->isoFormat('MMMM YYYY'),
                'url' => route('epis.ficha', ['colaborador' => $colaborador->id, 'month' => $r->mes, 'year' => $r->ano]),
            ]);

        // If only one month exists, redirect directly to it
        if ($meses->count() === 1) {
            return redirect()->route('epis.ficha', [
                'colaborador' => $colaborador->id,
                'month' => $meses->first()['mes'],
                'year' => $meses->first()['ano'],
            ]);
        }

        return view('epis.ficha-print-picker', [
            'colaborador' => $colaborador,
            'meses' => $meses,
        ]);
    }
}
