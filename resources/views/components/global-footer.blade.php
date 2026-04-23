<footer class="mt-auto py-4 px-4 border-t border-white/5 bg-black/40 backdrop-blur-md self-stretch">
    <div class="max-w-7xl mx-auto flex flex-col items-center gap-2 text-center">
        <div>
            <p class="text-white/60 text-xs font-medium tracking-wide">
                © 2024–2026 <span class="text-white font-bold">CME</span>
                <span class="mx-2 text-white/20">·</span>
                v1.0.0
                <span class="mx-2 text-white/20">·</span>
            </p>
        </div>
        <div class="text-white/60 text-xs font-medium tracking-wide">
            Desenvolvido por <span class="text-white font-bold">Israel Montesino Barreto</span>
        </div>
        <div class="flex items-center gap-4 text-xs uppercase  tracking-tighter text-white/30">
            <a href="{{ route('legal', ['tab' => 'privacidade']) }}"
                class="hover:text-white transition-colors">Políticas de Privacidade</a>
            <span class="h-1 w-1 bg-white/10 rounded-full"></span>
            <a href="{{ route('legal', ['tab' => 'termos']) }}" class="hover:text-white transition-colors">Termos e
                Condições</a>
            <span class="h-1 w-1 bg-white/10 rounded-full"></span>
            <a href="{{ route('legal', ['tab' => 'cookies']) }}" class="hover:text-white transition-colors">Cookies</a>
        </div>
    </div>
</footer>
