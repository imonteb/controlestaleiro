<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (request()->has('manual')) {
        session(['manual_desktop' => true]);
    }

    return view('welcome');
})->name('home');

// Web Push — públicas (colaboradores no tienen auth Laravel)
Route::prefix('push')->name('push.')->controller(\App\Http\Controllers\WebPushController::class)->group(function () {
    Route::get('public-key', 'publicKey')->name('public-key');
    Route::post('subscribe', 'subscribe')->name('subscribe');
    Route::post('unsubscribe', 'unsubscribe')->name('unsubscribe');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    // Operário + Admin
    Route::middleware(['operario'])->group(function () {
        Route::get('gestao-equipas', \App\Livewire\GestaoEquipas::class)->name('gestao-equipas');
        Route::get('/publicar-dia', \App\Livewire\PublicarDia::class)->name('publicar-dia');
        Route::get('/monitor', \App\Livewire\PcDisplay::class)->name('monitor');
    });

    // Admin only
    Route::middleware(['admin'])->group(function () {
        // Resumo mensal
        Route::get('/resumo-mensal', \App\Livewire\ResumoMensal::class)->name('resumo-mensal');

        // Estatísticas
        Route::get('/estatisticas/veiculos', \App\Livewire\Estatisticas\Veiculos::class)->name('estatisticas.veiculos');
        Route::get('/estatisticas/colaboradores', \App\Livewire\Estatisticas\Colaboradores::class)->name('estatisticas.colaboradores');

        // Exportar estatísticas
        Route::get('/exportar/estatisticas/veiculos', [\App\Http\Controllers\ExportController::class, 'estatisticasVeiculos'])->name('exportar.estatisticas.veiculos');
        Route::get('/exportar/estatisticas/colaboradores', [\App\Http\Controllers\ExportController::class, 'estatisticasColaboradores'])->name('exportar.estatisticas.colaboradores');

        // Colaboradores
        Route::get('/colaboradores', \App\Livewire\Colaboradores\Index::class)->name('colaboradores.index');
        Route::get('/colaboradores/nuevo', \App\Livewire\Colaboradores\Form::class)->name('colaboradores.crear');
        Route::get('/colaboradores/editar/{colaborador}', \App\Livewire\Colaboradores\Form::class)->name('colaboradores.editar');

        // Veículos
        Route::get('/veiculos', \App\Livewire\Veiculos\Index::class)->name('veiculos.index');
        Route::get('/veiculos/novo', \App\Livewire\Veiculos\Form::class)->name('veiculos.crear');
        Route::get('/veiculos/editar/{veiculo}', \App\Livewire\Veiculos\Form::class)->name('veiculos.editar');

        // PEPs
        Route::get('/peps', \App\Livewire\Peps\Index::class)->name('peps.index');
        Route::get('/peps/nuevo', \App\Livewire\Peps\Form::class)->name('peps.crear');
        Route::get('/peps/editar/{pep}', \App\Livewire\Peps\Form::class)->name('peps.editar');
    });

    // Administración — Super Admin (Gestión de Usuarios)
    Route::middleware(['super_admin'])->group(function () {
        Route::get('/register', fn () => view('livewire.auth.register'))->name('register');
        Route::post('/register', [\Laravel\Fortify\Http\Controllers\RegisteredUserController::class, 'store'])->name('register.store');
    });

    Route::middleware(['admin'])->group(function () {
        Route::get('/utilizadores', \App\Livewire\GestaoUtilizadores::class)->name('utilizadores.index');
        Route::get('/sessoes', \App\Livewire\GestaoSessoes::class)->name('sessoes.index');

        // Importar / exportar dados
        Route::get('/importar', \App\Livewire\ImportarDados::class)->name('importar');
        Route::get('/importar/plantilla/{tipo}', function (\Illuminate\Http\Request $request, string $tipo) {
            return app(\App\Http\Controllers\ImportTemplateController::class)($request, $tipo);
        })->name('importar.plantilla');
        Route::get('/exportar/{tipo}', \App\Http\Controllers\ExportController::class)->name('exportar');

        // Avisos TV
        Route::get('/avisos-tv', \App\Livewire\AvisosTv\Index::class)->name('avisos-tv.index');

        // Avisos PWA (Notificações Móveis)
        Route::get('/notificacoes', \App\Livewire\GestaoNotificacoes::class)->name('notificacoes.index');

        // Segurança e Apoio (Gestão PWA)
        Route::get('/seguranca', \App\Livewire\GestaoSeguranca::class)->name('seguranca.index');

        // Catálogo de Materiais
        Route::get('/materiais', \App\Livewire\Materiais\Index::class)->name('materiais.index');
        Route::get('/materiais/novo', \App\Livewire\Materiais\Form::class)->name('materiais.crear');
        Route::get('/materiais/editar/{material}', \App\Livewire\Materiais\Form::class)->name('materiais.editar');
        Route::get('/materiais/plantilla', function (\Illuminate\Http\Request $request) {
            return app(\App\Http\Controllers\ImportTemplateController::class)($request, 'materiais');
        })->name('materiais.plantilla');

        // Categorias de Materiais
        Route::get('/material-categorias', \App\Livewire\MaterialCategorias\Index::class)->name('material-categorias.index');
        Route::get('/material-categorias/novo', \App\Livewire\MaterialCategorias\Form::class)->name('material-categorias.crear');
        Route::get('/material-categorias/editar/{categoria}', \App\Livewire\MaterialCategorias\Form::class)->name('material-categorias.editar');

        // Locais Frequentes
        Route::get('/locais-frequentes', \App\Livewire\LocaisFrequentes\Index::class)->name('locais-frequentes.index');
        Route::get('/locais-frequentes/novo', \App\Livewire\LocaisFrequentes\Form::class)->name('locais-frequentes.crear');
        Route::get('/locais-frequentes/editar/{local}', \App\Livewire\LocaisFrequentes\Form::class)->name('locais-frequentes.editar');

        // Tipos de Trabalho
        Route::get('/tipos-trabalho', \App\Livewire\TiposTrabalho\Index::class)->name('tipos-trabalho.index');
        Route::get('/tipos-trabalho/novo', \App\Livewire\TiposTrabalho\Form::class)->name('tipos-trabalho.crear');
        Route::get('/tipos-trabalho/editar/{tipoTrabalho}', \App\Livewire\TiposTrabalho\Form::class)->name('tipos-trabalho.editar');

        // Localizações
        Route::get('/localizacoes', \App\Livewire\Localizacoes\Index::class)->name('localizacoes.index');
        Route::get('/localizacoes/nueva', \App\Livewire\Localizacoes\Form::class)->name('localizacoes.crear');
        Route::get('/localizacoes/editar/{localizacao}', \App\Livewire\Localizacoes\Form::class)->name('localizacoes.editar');

        // Páginas Legais (App Info)
        Route::get('/legal-pages', \App\Livewire\GestaoLegalPages::class)->name('legal-pages.index');
    });

    // Logística y Segurança — solo logi, admin o super_admin
    Route::middleware(['logi'])->group(function () {
        // Ferramentas
        Route::get('/ferramentas', \App\Livewire\Ferramentas\Index::class)->name('ferramentas.index');
        Route::get('/ferramentas/nuevo', \App\Livewire\Ferramentas\Form::class)->name('ferramentas.crear');
        Route::get('/ferramentas/editar/{ferramenta}', \App\Livewire\Ferramentas\Form::class)->name('ferramentas.editar');
        Route::get('/ferramentas/log/{ferramenta}', \App\Livewire\Ferramentas\Log::class)->name('ferramentas.log');
        Route::get('/ferramentas/folha-verificacao', \App\Livewire\Ferramentas\FolhaVerificacao::class)->name('ferramentas.folha-verificacao');
        Route::get('/ferramentas/imprimir-folha/{folhaId}', \App\Http\Controllers\FerramentaLogPrintController::class)->name('ferramentas.imprimir-folha');
        Route::get('/ferramentas/plantilla', function (\Illuminate\Http\Request $request) {
            return app(\App\Http\Controllers\ImportTemplateController::class)($request, 'ferramentas');
        })->name('ferramentas.plantilla');

        // Extintores
        Route::get('/extintores', \App\Livewire\Extintores\Index::class)->name('extintores.index');
        Route::get('/extintores/nuevo', \App\Livewire\Extintores\Form::class)->name('extintores.crear');
        Route::get('/extintores/editar/{extintor}', \App\Livewire\Extintores\Form::class)->name('extintores.editar');
        Route::get('/extintores/plantilla', function (\Illuminate\Http\Request $request) {
            return app(\App\Http\Controllers\ImportTemplateController::class)($request, 'extintores');
        })->name('extintores.plantilla');

        // Saúde
        Route::get('/saude', \App\Livewire\Saude\Index::class)->name('saude.index');
        Route::get('/saude/itens', \App\Livewire\Saude\ItensIndex::class)->name('saude.itens');
        Route::get('/saude/kit/nuevo', \App\Livewire\Saude\KitForm::class)->name('saude.kit.crear');
        Route::get('/saude/kit/editar/{kit}', \App\Livewire\Saude\KitForm::class)->name('saude.kit.editar');
        Route::get('/saude/plantilla', function (\Illuminate\Http\Request $request) {
            return app(\App\Http\Controllers\ImportTemplateController::class)($request, 'saude_kits');
        })->name('saude.plantilla');

        // Gestão Unificada de Activos
        Route::get('/gestao-activos', \App\Livewire\GestaoActivos::class)->name('gestao-activos');
    });

    // EPIs — solo usuarios con rol 'epi', admin o super_admin
    Route::middleware(['epi'])->group(function () {
        // Catálogo
        Route::get('/epis', \App\Livewire\Epis\Index::class)->name('epis.index');
        Route::get('/epis/nuevo', \App\Livewire\Epis\Form::class)->name('epis.crear');
        Route::get('/epis/editar/{epiItem}', \App\Livewire\Epis\Form::class)->name('epis.editar');
        Route::get('/epis/plantilla', function (\Illuminate\Http\Request $request) {
            return app(\App\Http\Controllers\ImportTemplateController::class)($request, 'epis');
        })->name('epis.plantilla');

        // Entregas
        Route::get('/epis/entregas', \App\Livewire\Epis\Entregas\Index::class)->name('epis.entregas.index');
        Route::get('/epis/entregas/nueva', \App\Livewire\Epis\Entregas\Form::class)->name('epis.entregas.crear');

        // Recepções
        Route::get('/epis/rececoes', \App\Livewire\Epis\Rececoes\Index::class)->name('epis.rececoes.index');
        Route::get('/epis/rececoes/nueva', \App\Livewire\Epis\Rececoes\Form::class)->name('epis.rececoes.crear');
        Route::get('/epis/rececoes/plantilla', function (\Illuminate\Http\Request $request) {
            return app(\App\Http\Controllers\ImportTemplateController::class)($request, 'epi_rececoes');
        })->name('epis.rececoes.plantilla');

        // Inventário
        Route::get('/epis/inventario', \App\Livewire\Epis\Inventario::class)->name('epis.inventario');

        // Histórico Mensal
        Route::get('/epis/historico', \App\Livewire\Epis\Historico::class)->name('epis.historico');

        // Pedidos de EPI (via Mobile)
        Route::get('/epis/pedidos', \App\Livewire\Epis\Pedidos::class)->name('epis.pedidos');
        Route::get('/epis/dotacao', \App\Livewire\Epis\Dotacao::class)->name('epis.dotacao');

        // Ficha EPI (print view)
        Route::get('/epis/ficha/{colaborador}', \App\Http\Controllers\FichaEpiController::class)->name('epis.ficha');
        Route::get('/epis/imprimir-mensal', \App\Http\Controllers\BulkFichaEpiController::class)->name('epis.imprimir-mensal');

        // Transporte
        Route::get('/guias', \App\Livewire\GestaoGuias::class)->name('guias.index');
        Route::get('/veiculos/registo-conducao', \App\Livewire\Condutores\RegistoConducao::class)->name('condutores.registo');
    });

    // Panel TV — operario + admin
    Route::get('/tv', \App\Livewire\TvDisplay::class)->name('tv');
});

Route::get('/phone', \App\Livewire\PhoneDisplay::class)->name('phone');
Route::get('/legal/{tab?}', \App\Livewire\LegalPages::class)->name('legal');

// Public mobile signature routes
Route::get('/sign/{token}', [\App\Http\Controllers\SignatureController::class, 'show'])->name('signature.show');
Route::post('/sign/{token}', [\App\Http\Controllers\SignatureController::class, 'store'])->name('signature.store');
Route::get('/sign/{token}/status', [\App\Http\Controllers\SignatureController::class, 'status'])->name('signature.status');

require __DIR__.'/settings.php';
