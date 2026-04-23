<?php

namespace App\Livewire\Colaboradores;

use App\Models\Colaborador;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Colaboradores')]
class Index extends Component
{
    public string $sortBy = 'nombre';

    public string $sortDir = 'asc';

    public string $filtro = 'activos';

    public string $pesquisa = '';

    public ?int $desativandoId = null;

    public string $motivoBaixa = '';

    public bool $confirmandoEliminar = false;

    public ?int $eliminandoId = null;

    public bool $eliminacaoBloqueada = false;

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function desativar(int $id): void
    {
        $this->desativandoId = $id;
        $this->motivoBaixa = '';
    }

    public function confirmarDesativar(): void
    {
        $this->validate(['motivoBaixa' => 'nullable|string|max:500']);

        Colaborador::find($this->desativandoId)?->update([
            'activo' => false,
            'motivo_baja' => $this->motivoBaixa ?: null,
        ]);

        $this->desativandoId = null;
        $this->motivoBaixa = '';
    }

    public function reactivar(int $id): void
    {
        Colaborador::find($id)?->update(['activo' => true, 'motivo_baja' => null]);
    }

    public function pedirEliminar(int $id): void
    {
        $this->eliminandoId = $id;
        $this->eliminacaoBloqueada = $this->colaboradorTemHistorico($id);
        $this->confirmandoEliminar = true;
    }

    private function colaboradorTemHistorico(int $id): bool
    {
        return DB::table('asignacion_colaborador')->where('colaborador_id', $id)->exists()
            || DB::table('epi_entregas')->where('colaborador_id', $id)->exists()
            || DB::table('epi_pedidos')->where('colaborador_id', $id)->exists()
            || DB::table('ferramenta_logs')->where('colaborador_id', $id)->exists()
            || DB::table('guia_transportes')->where('requerente_id', $id)->exists()
            || DB::table('incident_reports')->where('colaborador_id', $id)->exists()
            || DB::table('vehicle_driver_logs')->where('colaborador_id', $id)->exists();
    }

    public function eliminarPermanente(): void
    {
        if (! auth()->user()?->isAdmin()) {
            $this->confirmandoEliminar = false;

            return;
        }

        $colaborador = Colaborador::find($this->eliminandoId);

        if ($colaborador && ! $this->colaboradorTemHistorico($colaborador->id)) {
            DB::transaction(function () use ($colaborador) {
                $id = $colaborador->id;

                // Nullify nullable FK references
                DB::table('app_notifications')->where('colaborador_id', $id)->update(['colaborador_id' => null]);
                DB::table('epi_entregas')->where('colaborador_id', $id)->update(['colaborador_id' => null]);
                DB::table('ferramenta_logs')->where('colaborador_id', $id)->update(['colaborador_id' => null]);
                DB::table('guia_transportes')->where('requerente_id', $id)->update(['requerente_id' => null]);

                // Delete non-nullable dependent records
                DB::table('asignacion_colaborador')->where('colaborador_id', $id)->delete();
                DB::table('epi_pedidos')->where('colaborador_id', $id)->delete();
                DB::table('incident_reports')->where('colaborador_id', $id)->delete();
                DB::table('push_subscriptions')->where('colaborador_id', $id)->delete();
                DB::table('vehicle_driver_logs')->where('colaborador_id', $id)->delete();

                $colaborador->delete();
            });
        }

        $this->confirmandoEliminar = false;
        $this->eliminandoId = null;
    }

    public function cancelarEliminar(): void
    {
        $this->confirmandoEliminar = false;
        $this->eliminandoId = null;
    }

    public function toggleVisibleDashboard(int $id): void
    {
        $colaborador = Colaborador::find($id);
        if ($colaborador) {
            $colaborador->update(['visible_en_dashboard' => ! $colaborador->visible_en_dashboard]);
        }
    }

    public function render()
    {
        $query = Colaborador::orderBy($this->sortBy, $this->sortDir);

        if ($this->filtro === 'activos') {
            $query->ativos();
        } elseif ($this->filtro === 'inactivos') {
            $query->inativos();
        }

        if ($this->pesquisa !== '') {
            $s = '%'.$this->pesquisa.'%';
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', $s)
                    ->orWhere('apellido', 'like', $s)
                    ->orWhere('numero_colaborador', 'like', $s)
                    ->orWhere('denominacion_cargo', 'like', $s);
            });
        }

        return view('livewire.colaboradores.index', [
            'colaboradores' => $query->get(),
        ]);
    }
}
