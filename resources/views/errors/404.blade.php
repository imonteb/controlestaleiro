<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página não encontrada - ControlEstaleiro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#0f172a] text-white flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full text-center">
        <!-- Icono de Búsqueda/Perdido -->
        <div class="mb-8 flex justify-center">
            <div class="bg-blue-500/10 p-6 rounded-full border border-blue-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <h1 class="text-4xl font-bold mb-4 tracking-tight">404</h1>
        <h2 class="text-2xl font-semibold mb-6 text-blue-400">Página não encontrada</h2>
        
        <p class="text-slate-400 mb-10 text-lg leading-relaxed">
            Ups! Parece que a página que procuras não existe ou foi movida para outro local.
        </p>

        <div class="space-y-4">
            <p class="text-sm text-slate-500">Talvez queiras tentar:</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/dashboard') }}" class="bg-yellow-500 hover:bg-yellow-600 text-slate-900 font-bold py-3 px-8 rounded-lg transition-all transform hover:scale-105 shadow-lg shadow-yellow-500/20">
                    Voltar ao Dashboard
                </a>
                <a href="javascript:history.back()" class="bg-slate-800 hover:bg-slate-700 text-white font-semibold py-3 px-8 rounded-lg transition-all border border-slate-700">
                    Voltar atrás
                </a>
            </div>
        </div>

        <div class="mt-16 text-slate-600 text-sm">
            ControlEstaleiro &bull; Sistema de Gestão Interna
        </div>
    </div>
</body>
</html>
