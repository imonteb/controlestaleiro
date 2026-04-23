<x-layouts::auth :title="__('Registrar Usuario')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Registrar Nuevo Usuario')" :description="__('Crea una cuenta de acceso al sistema')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />

            {{-- Checkbox 1: Termos + Privacidade --}}
            <div class="p-3 bg-white/5 rounded-xl border border-white/10 space-y-0">
                <div class="flex items-start gap-3">
                    <flux:checkbox name="accept_terms" required />
                    <span class="text-xs text-white/80 leading-relaxed">
                        Li e aceito os <a href="{{ route('legal') }}" target="_blank" class="text-blue-300 underline hover:text-white transition">Termos e Condições</a>
                        e a <a href="{{ route('legal') }}" target="_blank" class="text-blue-300 underline hover:text-white transition">Política de Privacidade</a>
                        da plataforma ControlEstaleiro, operada pela <strong class="text-white">CME — Unidade C016</strong>.
                    </span>
                </div>
                @error('accept_terms')
                    <p class="mt-1 text-xs text-red-400 pl-7">{{ $message }}</p>
                @enderror
            </div>

            {{-- Checkbox 2: Uso profissional --}}
            <div class="p-3 bg-yellow-500/5 rounded-xl border border-yellow-500/20">
                <div class="flex items-start gap-3">
                    <flux:checkbox name="accept_professional_use" required />
                    <span class="text-xs text-white/80 leading-relaxed">
                        Reconheço que o acesso é <strong class="text-white">exclusivamente profissional</strong>
                        e que as minhas credenciais são pessoais e intransmissíveis.
                    </span>
                </div>
                @error('accept_professional_use')
                    <p class="mt-1 text-xs text-red-400 pl-7">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="text-center">
            <a href="{{ route('dashboard') }}" wire:navigate
               class="inline-flex items-center gap-1.5 text-sm text-blue-300 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar ao Dashboard
            </a>
        </div>
    </div>
</x-layouts::auth>
