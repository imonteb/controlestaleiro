<?php

namespace App\Livewire\Epis;

use App\Exports\DotacaoExport;
use App\Models\Colaborador;
use App\Models\EpiEntrega;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class Dotacao extends Component
{
    public string $search = '';

    public ?int $colaboradorId = null;

    public function selectColaborador(int $id): void
    {
        $this->colaboradorId = $id;
        $this->search = '';
    }

    public function clearColaborador(): void
    {
        $this->colaboradorId = null;
    }

    public function exportarExcel()
    {
        if (! $this->colaboradorId) {
            return;
        }

        $colaborador = Colaborador::findOrFail($this->colaboradorId);
        $nombre = $colaborador->nombre.'_'.$colaborador->apellido;

        return Excel::download(
            new DotacaoExport($this->colaboradorId),
            'dotacao_epi_'.str_replace(' ', '_', $nombre).'.xlsx'
        );
    }

    public function render()
    {
        // Search results (shown when no colaborador selected)
        $resultados = collect();
        if ($this->search && ! $this->colaboradorId) {
            $s = mb_strtolower($this->search);
            $resultados = Colaborador::where(function ($q) use ($s) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ["%{$s}%"])
                    ->orWhereRaw('LOWER(apellido) LIKE ?', ["%{$s}%"])
                    ->orWhereRaw('LOWER(numero_colaborador) LIKE ?', ["%{$s}%"]);
            })
                ->orderBy('apellido')
                ->orderBy('nombre')
                ->limit(10)
                ->get();
        }

        // Dotação data for selected colaborador
        $colaborador = null;
        $dotacao = collect();
        $totaisPendentes = 0;

        if ($this->colaboradorId) {
            $colaborador = Colaborador::find($this->colaboradorId);

            if ($colaborador) {
                $entregas = EpiEntrega::with('epiItem')
                    ->where('colaborador_id', $this->colaboradorId)
                    ->orderBy('fecha_entrega')
                    ->get();

                // Group by epiItem and aggregate
                $dotacao = $entregas
                    ->groupBy('epi_item_id')
                    ->map(function ($rows) {
                        $item = $rows->first()->epiItem;
                        $totalRecibido = $rows->sum('cantidad');
                        $totalDevuelto = $rows->whereNotNull('fecha_devolucion')->sum('cantidad');
                        $enPoder = $totalRecibido - $totalDevuelto;

                        return [
                            'epi' => $item,
                            'total_recibido' => $totalRecibido,
                            'total_devuelto' => $totalDevuelto,
                            'en_poder' => $enPoder,
                            'ultima_entrega' => $rows->max('fecha_entrega'),
                            'entregas' => $rows,
                        ];
                    })
                    ->sortByDesc('en_poder')
                    ->values();

                $totaisPendentes = $dotacao->sum('en_poder');
            }
        }

        return view('livewire.epis.dotacao', [
            'resultados' => $resultados,
            'colaborador' => $colaborador,
            'dotacao' => $dotacao,
            'totaisPendentes' => $totaisPendentes,
        ])->title('Dotação EPI');
    }
}
