<?php

namespace App\Http\Controllers;

use App\Exports\ColaboradoresExport;
use App\Exports\EstatisticasColaboradoresExport;
use App\Exports\EstatisticasVeiculosExport;
use App\Exports\PepsExport;
use App\Exports\VeiculosExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __invoke(Request $request, string $tipo): BinaryFileResponse
    {
        return match ($tipo) {
            'colaboradores' => Excel::download(new ColaboradoresExport, 'colaboradores_'.now()->format('Ymd').'.xlsx'),
            'veiculos' => Excel::download(new VeiculosExport, 'veiculos_'.now()->format('Ymd').'.xlsx'),
            'peps' => Excel::download(new PepsExport, 'peps_'.now()->format('Ymd').'.xlsx'),
            default => abort(404),
        };
    }

    public function estatisticasVeiculos(Request $request): BinaryFileResponse
    {
        $request->validate([
            'mes' => 'required|integer|between:1,12',
            'ano' => 'required|integer|min:2020',
        ]);

        $export = new EstatisticasVeiculosExport(
            (int) $request->mes,
            (int) $request->ano,
        );
        $filename = 'estatisticas_veiculos'
                    .'_'.str_pad($request->mes, 2, '0', STR_PAD_LEFT)
                    .'_'.$request->ano.'.xlsx';

        return Excel::download($export, $filename);
    }

    public function estatisticasColaboradores(Request $request): BinaryFileResponse
    {
        $request->validate([
            'mes' => 'required|integer|between:1,12',
            'ano' => 'required|integer|min:2020',
        ]);

        $export = new EstatisticasColaboradoresExport(
            (int) $request->mes,
            (int) $request->ano,
        );
        $filename = 'estatisticas_colaboradores'
                    .'_'.str_pad($request->mes, 2, '0', STR_PAD_LEFT)
                    .'_'.$request->ano.'.xlsx';

        return Excel::download($export, $filename);
    }
}
