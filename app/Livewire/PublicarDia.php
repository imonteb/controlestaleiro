<?php

namespace App\Livewire;

use App\Models\DiaPublicado;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Publicar Dia na TV')]
class PublicarDia extends Component
{
    public ?string $diaActivoId = null;

    public string $mensajeOk = '';

    public function mount(): void
    {
        $ativo = DiaPublicado::ativoNaTv();
        $this->diaActivoId = $ativo ? (string) $ativo->id : null;
    }

    public function activar(int $id): void
    {
        $dia = DiaPublicado::findOrFail($id);
        $dia->ativarNaTv();
        $this->diaActivoId = (string) $id;
        $this->mensajeOk = 'O dia '.$dia->fecha->format('d/m/Y').' já está ativo na TV.';
    }

    public function desactivarTv(): void
    {
        DiaPublicado::query()->update(['activo_tv' => false]);
        $this->diaActivoId = null;
        $this->mensajeOk = 'Painel TV desativado. Não será mostrado nenhum dia.';
    }

    public function render()
    {
        // Contar colaboradores por data numa única query (evita N+1)
        $contagemPorData = DB::table('asignacion_colaborador')
            ->join('asignaciones', 'asignaciones.id', '=', 'asignacion_colaborador.asignacion_id')
            ->whereNotNull('asignaciones.pep_id')
            ->select('asignaciones.fecha', DB::raw('COUNT(asignacion_colaborador.colaborador_id) as total'))
            ->groupBy('asignaciones.fecha')
            ->pluck('total', 'fecha');

        $dias = DiaPublicado::with('confirmadoPor')
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function ($dia) use ($contagemPorData) {
                $dia->num_colaboradores = $contagemPorData[$dia->fecha->toDateString()] ?? 0;
                $dia->fecha_formato = Carbon::parse($dia->fecha)->locale('pt')->isoFormat('dddd, D [de] MMMM [de] YYYY');

                return $dia;
            });

        return view('livewire.publicar-dia', [
            'dias' => $dias,
            'urlTv' => route('tv'),
        ]);
    }
}
