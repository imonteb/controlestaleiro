<div class="p-6 space-y-6" x-data="{ 
    signaturePad: null, 
    hasSignature: false,
    showEditModal: @entangle('showEditModal'),
    init() {
        this.$watch('showEditModal', value => {
            if (value) {
                this.$nextTick(() => { this.setupPad(); });
            }
        });
        window.addEventListener('signature-ready', (e) => {
            if (this.signaturePad && e.detail.signature) {
                this.signaturePad.fromDataURL(e.detail.signature);
                this.hasSignature = true;
            }
        });
    },
    setupPad() {
        if (typeof SignaturePad === 'undefined') {
            let script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js';
            script.onload = () => { this.doSetup(); };
            document.head.appendChild(script);
        } else {
            this.doSetup();
        }
    },
    doSetup() {
        let canvas = this.$refs.signatureCanvas;
        if (!canvas) return;
        const resize = () => {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
        };
        resize();
        this.signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(0,0,0,0)',
            penColor: 'rgb(0, 0, 0)'
        });
        this.signaturePad.addEventListener('endStroke', () => {
            this.hasSignature = true;
            @this.set('editSignature', this.signaturePad.toSVG());
        });
        window.addEventListener('resize', resize);

        // Load existing signature
        let currentSig = @this.get('editSignature');
        if (currentSig) {
            this.signaturePad.fromDataURL(currentSig);
            this.hasSignature = true;
        }
    }
}" x-cloak>

    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white uppercase tracking-wide">Gestão de Utilizadores</h1>
            <p class="text-sm text-blue-200 mt-0.5">Gere os utilizadores com acesso ao sistema</p>
        </div>
        <a href="{{ route('register') }}" wire:navigate
           class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold text-sm px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Novo Utilizador
        </a>
    </div>

    {{-- Mensagem de sucesso --}}
    @if($successMessage)
        <div class="bg-green-600/20 border border-green-500 text-green-200 rounded-lg px-4 py-3 text-sm">
            {{ $successMessage }}
        </div>
    @endif

    {{-- Pesquisa --}}
    <div class="max-w-sm">
        <input wire:model.live="search" type="text" placeholder="Pesquisar por nome ou email…"
               class="w-full rounded-lg bg-blue-900/50 border border-blue-700 text-white placeholder-blue-400 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
    </div>

    {{-- Tabela --}}
    <div class="overflow-x-auto rounded-xl border border-blue-700/60">
        <table class="w-full text-sm text-left text-blue-100">
            <thead class="bg-blue-900/70 text-blue-300 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-5 py-3">Nome</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Função</th>
                    <th class="px-5 py-3">Data de Registo</th>
                    <th class="px-5 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-blue-800/50">
                @forelse($users as $user)
                    <tr class="bg-blue-950/30 hover:bg-blue-900/30 transition">
                        <td class="px-5 py-3 font-medium text-white">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span class="ml-2 text-xs bg-yellow-500/20 text-yellow-400 border border-yellow-500/40 rounded px-1.5 py-0.5">Você</span>
                            @endif
                            @if($user->email === 'israelmontesino@gmail.com' && auth()->user()->email !== 'israelmontesino@gmail.com')
                                <span class="ml-2 text-xs bg-gray-500/20 text-gray-400 border border-gray-500/40 rounded px-1.5 py-0.5" title="Protegido — apenas o próprio pode editar">🔒</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach(($user->role ?? [App\Models\User::ROLE_OPERARIO]) as $r)
                                    @if($r === App\Models\User::ROLE_SUPER_ADMIN)
                                        <span class="inline-flex items-center gap-1 bg-red-500/20 text-red-300 border border-red-500/40 rounded-full px-2.5 py-0.5 text-xs font-medium">👑 Super Admin</span>
                                    @elseif($r === App\Models\User::ROLE_ADMIN)
                                        <span class="inline-flex items-center gap-1 bg-purple-500/20 text-purple-300 border border-purple-500/40 rounded-full px-2.5 py-0.5 text-xs font-medium">🛡️ Admin</span>
                                    @elseif($r === App\Models\User::ROLE_LOGI)
                                        <span class="inline-flex items-center gap-1 bg-amber-500/20 text-amber-300 border border-amber-500/40 rounded-full px-2.5 py-0.5 text-xs font-medium">📦 Logística</span>
                                    @elseif($r === App\Models\User::ROLE_EPI)
                                        <span class="inline-flex items-center gap-1 bg-green-500/20 text-green-300 border border-green-500/40 rounded-full px-2.5 py-0.5 text-xs font-medium">🧤 EPI</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-blue-500/20 text-blue-300 border border-blue-500/40 rounded-full px-2.5 py-0.5 text-xs font-medium">👷 Operário</span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-3 text-blue-400">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3">
                            @php $isProtected = $user->email === 'israelmontesino@gmail.com' && auth()->user()->email !== 'israelmontesino@gmail.com'; @endphp
                            <div class="flex items-center justify-end gap-2">
                                @if($isProtected)
                                    <span class="text-xs text-gray-500 italic">protegido</span>
                                @else
                                {{-- Editar --}}
                                <button wire:click="openEdit({{ $user->id }})"
                                        class="text-xs bg-blue-700 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition">
                                    Editar
                                </button>
                                {{-- Palavra-passe --}}
                                <button wire:click="openResetPassword({{ $user->id }})"
                                        class="text-xs bg-amber-600 hover:bg-amber-500 text-white px-3 py-1.5 rounded-lg transition">
                                    Palavra-passe
                                </button>
                                {{-- Eliminar --}}
                                @if($user->id !== auth()->id())
                                    <button wire:click="confirmDelete({{ $user->id }})"
                                            class="text-xs bg-red-700 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition">
                                        Eliminar
                                    </button>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-blue-400">Nenhum utilizador encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Modal: Editar utilizador ─────────────────────────────────── --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto pt-10 pb-10">
            <div class="bg-blue-950 border border-blue-700 rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6 space-y-5 my-auto">
                <h2 class="text-lg font-bold text-white">Editar Utilizador</h2>

                <div class="space-y-4">
                    @if ($errors->any())
                        <div class="bg-red-600/20 border border-red-500 text-red-200 rounded-lg px-3 py-2 text-xs">
                            Por favor, corrija os erros abaixo para guardar as alterações.
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs text-blue-300 font-medium mb-1">Nome</label>
                        <input wire:model="editName" type="text"
                               class="w-full rounded-lg bg-blue-900/60 border border-blue-600 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        @error('editName') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs text-blue-300 font-medium mb-1">Email</label>
                        <input wire:model="editEmail" type="email"
                               class="w-full rounded-lg bg-blue-900/60 border border-blue-600 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        @error('editEmail') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs text-blue-300 font-medium mb-1">Nº Colaborador</label>
                        <input wire:model="editEmployeeNumber" type="text" placeholder="Ex: 12345"
                               class="w-full rounded-lg bg-blue-900/60 border border-blue-600 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        @error('editEmployeeNumber') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs text-blue-300 font-medium mb-2">Funções</label>
                        <div class="flex flex-col gap-2">
                            @if(auth()->user()->isSuperAdmin())
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="editRoles" value="{{ App\Models\User::ROLE_SUPER_ADMIN }}" class="rounded border-blue-600 bg-blue-900/60 text-yellow-500 focus:ring-yellow-500">
                                    <span class="text-sm text-red-300">👑 Super Admin</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="editRoles" value="{{ App\Models\User::ROLE_ADMIN }}" class="rounded border-blue-600 bg-blue-900/60 text-yellow-500 focus:ring-yellow-500">
                                    <span class="text-sm text-purple-300">🛡️ Admin</span>
                                </label>
                            @endif
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="editRoles" value="{{ App\Models\User::ROLE_LOGI }}" class="rounded border-blue-600 bg-blue-900/60 text-yellow-500 focus:ring-yellow-500">
                                <span class="text-sm text-amber-300">📦 Logística</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="editRoles" value="{{ App\Models\User::ROLE_EPI }}" class="rounded border-blue-600 bg-blue-900/60 text-yellow-500 focus:ring-yellow-500">
                                <span class="text-sm text-green-300">🧤 EPI</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="editRoles" value="{{ App\Models\User::ROLE_OPERARIO }}" class="rounded border-blue-600 bg-blue-900/60 text-yellow-500 focus:ring-yellow-500">
                                <span class="text-sm text-blue-300">👷 Operário</span>
                            </label>
                        </div>
                        @error('editRoles') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('editRoles.*') <p class="text-red-400 text-xs mt-1">Uma ou mais funções selecionadas não são válidas.</p> @enderror
                    </div>

                    {{-- Signature Pad --}}
                    <div class="pt-4 border-t border-blue-800">
                        <label class="block text-xs text-blue-300 font-medium mb-2 uppercase tracking-wider">Firma Oficial</label>
                        <div class="bg-white border border-blue-700 rounded-lg p-2" wire:ignore>
                            <div class="border border-gray-300 bg-white rounded-lg overflow-hidden flex justify-center" style="height: 140px;">
                                <canvas x-ref="signatureCanvas" class="w-full h-full cursor-crosshair touch-none"></canvas>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <div class="flex gap-2">
                                    <button type="button" @click="signaturePad.clear(); hasSignature = false; @this.set('editSignature', '')"
                                        class="text-[10px] font-bold uppercase px-2 py-1 rounded bg-blue-800 text-blue-200 hover:bg-red-900/40 hover:text-red-300 transition-colors">Limpar</button>
                                    
                                    <button type="button" wire:click="startMobileSignature"
                                        class="text-[10px] font-bold uppercase px-2 py-1 rounded bg-blue-700 text-blue-100 hover:bg-blue-600 transition-colors flex items-center gap-1">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                        </svg>
                                        Móvil
                                    </button>
                                </div>
                                <span class="text-[10px] text-blue-500 uppercase font-medium italic">Assine acima</span>
                            </div>
                        </div>
                        @error('editSignature') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Modal de Firma Móvil --}}
                    @if($showSignatureModal)
                    <div class="fixed inset-0 z-100 flex items-center justify-center bg-black/80 px-4">
                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden border-t-8 border-yellow-400 transform transition-all">
                            <div class="p-6 flex flex-col items-center">
                                <h3 class="text-xl font-bold text-gray-900 mb-1">Firmar en Móvil</h3>
                                <p class="text-xs text-gray-500 text-center mb-6 uppercase tracking-wider">Captura de firma oficial</p>
                                
                                <div class="bg-white p-3 border-2 border-gray-100 rounded-2xl mb-6 shadow-sm">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($this->signature_url) }}" 
                                         alt="QR Code" width="250" height="250" class="block mx-auto">
                                </div>

                                <div class="w-full bg-gray-50 p-3 rounded-xl mb-6">
                                    <div class="flex gap-2">
                                        <input type="text" readonly value="{{ $this->signature_url }}" id="sig-url-user" class="flex-1 bg-white border-gray-200 rounded-lg text-[10px] py-2 px-2 focus:ring-0 text-gray-600 font-mono">
                                        <button type="button" 
                                                onclick="
                                                    const el = document.getElementById('sig-url-user');
                                                    if (navigator.clipboard && navigator.clipboard.writeText) {
                                                        navigator.clipboard.writeText(el.value).then(() => alert('Link copiado!'));
                                                    } else {
                                                        el.select();
                                                        document.execCommand('copy');
                                                        alert('Link copiado!');
                                                    }
                                                " 
                                                class="bg-gray-200 p-2 rounded-lg hover:bg-gray-300">
                                            <svg class="h-3 w-3 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 text-blue-600 animate-pulse mb-2">
                                    <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" /></svg>
                                    <span class="text-xs font-bold uppercase tracking-widest">Aguardando...</span>
                                </div>
                            </div>
                            <div class="bg-gray-100 px-6 py-4">
                                <button type="button" wire:click="$set('showSignatureModal', false)" class="w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-bold text-gray-700 hover:bg-gray-50 uppercase tracking-widest transition-all">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                        <div wire:poll.3s="checkSignatureStatus"></div>
                    </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="$set('showEditModal', false)"
                            class="px-4 py-2 text-sm text-blue-300 hover:text-white transition">Cancelar</button>
                    <button wire:click="saveEdit"
                            x-init="window.addEventListener('signature-ready', (e) => { 
                                // Forzar refresco del canvas si es necesario o simplemente confiar en el binding
                            })"
                            class="px-4 py-2 text-sm bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold rounded-lg transition">
                        Guardar
                    </button>
                    
                    @if($editSignature)
                         <span class="text-[10px] text-green-400 font-bold uppercase flex items-center gap-1 self-center">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Capturada
                         </span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ── Modal: Redefinir palavra-passe ───────────────────────────── --}}
    @if($showPasswordModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
            <div class="bg-blue-950 border border-blue-700 rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 space-y-5">
                <h2 class="text-lg font-bold text-white">Redefinir Palavra-passe</h2>

                <div class="space-y-4">
                    {{-- Nova palavra-passe --}}
                    <div x-data="{ show: false }">
                        <label class="block text-xs text-blue-300 font-medium mb-1">Nova palavra-passe</label>
                        <div class="relative">
                            <input wire:model="newPassword"
                                   :type="show ? 'text' : 'password'"
                                   class="w-full rounded-lg bg-blue-900/60 border border-blue-600 text-white px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-blue-400 hover:text-white transition">
                                <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.19-3.567M9.88 9.88A3 3 0 0114.12 14.12M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                        @error('newPassword') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Confirmar palavra-passe --}}
                    <div x-data="{ show: false }">
                        <label class="block text-xs text-blue-300 font-medium mb-1">Confirmar palavra-passe</label>
                        <div class="relative">
                            <input wire:model="newPasswordConfirmation"
                                   :type="show ? 'text' : 'password'"
                                   class="w-full rounded-lg bg-blue-900/60 border border-blue-600 text-white px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-blue-400 hover:text-white transition">
                                <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.19-3.567M9.88 9.88A3 3 0 0114.12 14.12M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                        @error('newPasswordConfirmation') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="$set('showPasswordModal', false)"
                            class="px-4 py-2 text-sm text-blue-300 hover:text-white transition">Cancelar</button>
                    <button wire:click="savePassword"
                            class="px-4 py-2 text-sm bg-amber-500 hover:bg-amber-400 text-gray-900 font-semibold rounded-lg transition">
                        Atualizar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Modal: Confirmar eliminação ─────────────────────────────── --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
            <div class="bg-blue-950 border border-red-700 rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 space-y-4">
                <h2 class="text-lg font-bold text-white">Eliminar utilizador?</h2>
                <p class="text-sm text-blue-300">Esta ação não pode ser desfeita.</p>
                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="px-4 py-2 text-sm text-blue-300 hover:text-white transition">Cancelar</button>
                    <button wire:click="deleteUser"
                            class="px-4 py-2 text-sm bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg transition">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
