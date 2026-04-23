<?php

namespace App\Livewire;

use App\Models\Atribuicao;
use App\Models\AvisoTv;
use App\Models\Colaborador;
use App\Models\EpiItem;
use App\Models\EpiPedido;
use App\Models\Extintor;
use App\Models\Ferramenta;
use App\Models\Pep;
use App\Models\SignatureRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function mount()
    {
        if (request()->has('manual')) {
            session(['manual_desktop' => true]);

            return;
        }

        // Simple mobile detection via User-Agent
        $userAgent = request()->header('User-Agent');
        $isMobile = preg_match('/Mobile|Android|BlackBerry|iPhone|Windows Phone/i', $userAgent);

        if ($isMobile && ! session()->has('manual_desktop')) {
            return redirect()->route('phone');
        }
    }

    public function render()
    {
        $user = Auth::user();
        $hoje = Carbon::today();

        // 1. Widget: Stock Crítico (EPI/Admin)
        $stockCritico = [];
        if ($user->hasRole('epi') || $user->isAdmin()) {
            $stockCritico = EpiItem::whereColumn('stock_total', '<=', 'stock_minimo')
                ->where('stock_minimo', '>', 0)
                ->orderBy('stock_total', 'asc')
                ->take(5)
                ->get();
        }

        // 2. Widget: Inspeções de Segurança (Logi/Admin)
        $inspecoesProximas = [];
        if ($user->hasRole('logi') || $user->isAdmin()) {
            $extintores = Extintor::where('proxima_revisao', '<=', $hoje->copy()->addDays(30))
                ->orderBy('proxima_revisao', 'asc')
                ->take(5)
                ->get();

            $ferramentas = Ferramenta::whereHas('ultimoLog', function ($q) use ($hoje) {
                $q->where('proxima_verificacao', '<=', $hoje->copy()->addDays(7));
            })
                ->with('ultimoLog')
                ->get()
                ->sortBy(fn ($f) => $f->ultimoLog->proxima_verificacao)
                ->take(5);

            $inspecoesProximas = [
                'extintores' => $extintores,
                'ferramentas' => $ferramentas,
            ];
        }

        // 3. Widget: Avisos TV (Todos)
        $avisos = AvisoTv::where('ativo', true)
            ->orderBy('ordem')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // 4. Widget: Assinaturas Pendentes (EPI/Admin)
        $assinaturasPendentesCount = 0;
        $pedidosPendentesCount = 0;
        if ($user->hasRole('epi') || $user->isAdmin()) {
            $assinaturasPendentesCount = SignatureRequest::where('status', 'pending')
                ->where('expires_at', '>=', now())
                ->count();

            $pedidosPendentesCount = EpiPedido::where('estado', 'pendente')->count();
        }

        // 5. Widget: Minha Equipa (Se for chefe ou tiver atribuição)
        $minhaEquipa = null;
        if ($user->employee_number) {
            $colab = Colaborador::where('numero_colaborador', $user->employee_number)->first();
            if ($colab) {
                $minhaEquipa = Atribuicao::where('fecha', $hoje->toDateString())
                    ->whereHas('colaboradores', function ($q) use ($colab) {
                        $q->where('colaboradores.id', $colab->id);
                    })
                    ->with(['pep.localizacao', 'colaboradores', 'veiculos'])
                    ->first();
            }
        }

        // 6. Widget: Resumo Atividade (Admin/Logi)
        $resumoAtividade = [];
        if ($user->isAdmin() || $user->hasRole('logi') || $user->hasRole('epi')) {
            $resumoAtividade = [
                'peps_ativos' => Pep::where('activo', true)->count(),
                'peps_com_equipa' => Atribuicao::where('fecha', $hoje->toDateString())->whereNotNull('pep_id')->distinct('pep_id')->count(),
                'colaboradores_em_campo' => DB::table('asignacion_colaborador')
                    ->join('asignaciones', 'asignacion_colaborador.asignacion_id', '=', 'asignaciones.id')
                    ->where('asignaciones.fecha', $hoje->toDateString())
                    ->whereNotNull('asignaciones.pep_id')
                    ->count(),
                'veiculos_em_campo' => DB::table('asignacion_vehiculo')
                    ->join('asignaciones', 'asignacion_vehiculo.asignacion_id', '=', 'asignaciones.id')
                    ->where('asignaciones.fecha', $hoje->toDateString())
                    ->whereNotNull('asignaciones.pep_id')
                    ->count(),
            ];
        }

        return view('livewire.dashboard', [
            'stockCritico' => $stockCritico,
            'inspecoesProximas' => $inspecoesProximas,
            'avisos' => $avisos,
            'assinaturasPendentesCount' => $assinaturasPendentesCount,
            'pedidosPendentesCount' => $pedidosPendentesCount,
            'minhaEquipa' => $minhaEquipa,
            'resumoAtividade' => $resumoAtividade,
            'hoje' => $hoje,
        ]);
    }
}
