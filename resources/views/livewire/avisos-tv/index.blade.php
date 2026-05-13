<div class="p-6 space-y-6">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="tv" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">Avisos para a TV</span>
            </div>
            <button wire:click="novo"
                style="background:#FFD300; color:#09143B; font-weight:700; font-size:12px; padding:6px 16px; border-radius:8px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Novo Aviso
            </button>
        </div>
    </div>

    {{-- Mensagem de sucesso --}}
    @if($successMessage)
        <div class="badge-ok px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ $successMessage }}
        </div>
    @endif

    {{-- Lista --}}
    @if($avisos->isEmpty())
        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl px-6 py-12 text-center">
            <p class="cme-muted text-sm italic">Nenhum aviso criado ainda.</p>
        </div>
    @else
    <div class="space-y-3">
        @foreach($avisos as $aviso)
        @php
            $cores = [
                'azul'     => ['dot' => 'bg-blue-400',   'label' => 'Azul'],
                'verde'    => ['dot' => 'bg-emerald-400', 'label' => 'Verde'],
                'amarelo'  => ['dot' => 'bg-yellow-400',  'label' => 'Amarelo'],
                'vermelho' => ['dot' => 'bg-red-400',     'label' => 'Vermelho'],
                'roxo'     => ['dot' => 'bg-purple-400',  'label' => 'Roxo'],
                'branco'   => ['dot' => 'bg-white',       'label' => 'Branco'],
            ];
            $c = $cores[$aviso->cor] ?? $cores['azul'];
        @endphp
        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="flex items-start gap-4 rounded-xl px-5 py-4 {{ $aviso->ativo ? '' : 'opacity-50' }}">
            {{-- Ordem --}}
            <div class="shrink-0 w-8 text-center">
                <span style="color:#7A7775; font-size:11px; font-weight:700;">{{ $aviso->ordem }}</span>
            </div>
            {{-- Cor ponto --}}
            <div class="shrink-0 mt-1">
                <span class="inline-block w-3 h-3 rounded-full {{ $c['dot'] }}"></span>
            </div>
            {{-- Imagem miniatura --}}
            @if($aviso->imagem)
            <div class="shrink-0">
                <img src="{{ $aviso->imageUrl() }}" alt="" style="width:48px; height:48px; object-fit:cover; border-radius:8px; border:1px solid rgba(9,20,59,0.14);">
            </div>
            @endif
            {{-- Conteúdo --}}
            <div class="flex-1 min-w-0">
                <div style="color:#1A1A1A; font-weight:600; font-size:14px; line-height:1.35;">{{ $aviso->titulo }}</div>
                @if($aviso->conteudo)
                <p style="color:#4A4845; font-size:12px; margin-top:4px; line-height:1.5;">{{ $aviso->conteudo }}</p>
                @endif
            </div>
            {{-- Estado --}}
            <div class="shrink-0">
                @if($aviso->ativo)
                    <span class="badge-ok inline-flex items-center gap-1 px-2 py-0.5 text-xs">✓ Ativo</span>
                @else
                    <span class="badge-neutral inline-flex items-center gap-1 px-2 py-0.5 text-xs">Inativo</span>
                @endif
            </div>
            {{-- Ações --}}
            <div class="shrink-0 flex items-center gap-2">
                <button wire:click="toggleAtivo({{ $aviso->id }})"
                    style="{{ $aviso->ativo ? 'background:#fdf0c2; color:#854F0B; border:1px solid rgba(133,79,11,0.20);' : 'background:#d4ede4; color:#0F6E56; border:1px solid rgba(15,110,86,0.20);' }} font-size:11px; font-weight:600; padding:6px 12px; border-radius:6px; cursor:pointer;">
                    {{ $aviso->ativo ? 'Desativar' : 'Ativar' }}
                </button>
                <button wire:click="editar({{ $aviso->id }})"
                    class="btn-cme-secondary text-xs px-3 py-1.5 rounded-lg">
                    Editar
                </button>
                <button wire:click="confirmarEliminar({{ $aviso->id }})"
                    style="background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); font-size:11px; font-weight:600; padding:6px 12px; border-radius:6px; cursor:pointer;">
                    Eliminar
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── Modal Criar / Editar ──────────────────────────────── --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/60 py-6">
        <div class="w-full max-w-lg mx-4 rounded-2xl overflow-hidden shadow-2xl border border-[rgba(9,20,59,0.16)]">
            {{-- Header --}}
            <div class="bg-[#09143B] px-5 py-3 flex items-center gap-2">
                <flux:icon name="tv" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">{{ $editingId ? 'Editar Aviso' : 'Novo Aviso' }}</span>
            </div>
            {{-- Body --}}
            <div style="background:white;" class="p-6 space-y-4">
                {{-- Título --}}
                <div>
                    <label class="cme-label">Título <span class="text-red-500">*</span></label>
                    <input wire:model="titulo" type="text" maxlength="255" class="cme-input mt-1">
                    @error('titulo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Conteúdo --}}
                <div>
                    <label class="cme-label">Texto adicional (opcional)</label>
                    <textarea wire:model="conteudo" rows="3" maxlength="2000"
                              class="cme-input mt-1" style="resize:none;"></textarea>
                    @error('conteudo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Imagem com Crop --}}
                <div x-data="{
                    cropperInstance: null,
                    showCropModal: false,
                    preview: null,
                    croppedLabel: '',
                    openPicker() {
                        this.$refs.pickerInput.value = '';
                        this.$refs.pickerInput.click();
                    },
                    onFileSelected(e) {
                        const file = e.target.files[0];
                        if (!file) return;
                        const reader = new FileReader();
                        reader.onload = (ev) => {
                            this.$refs.cropImg.src = ev.target.result;
                            this.showCropModal = true;
                            this.$nextTick(() => this.initCropper());
                        };
                        reader.readAsDataURL(file);
                    },
                    initCropper() {
                        if (this.cropperInstance) { this.cropperInstance.destroy(); this.cropperInstance = null; }
                        this.cropperInstance = new Cropper(this.$refs.cropImg, {
                            viewMode: 1,
                            autoCropArea: 0.9,
                            responsive: true,
                            background: true,
                            movable: true,
                            zoomable: true,
                            rotatable: true,
                        });
                    },
                    aplicarCorte() {
                        const canvas = this.cropperInstance.getCroppedCanvas({ maxWidth: 500, maxHeight: 500 });
                        canvas.toBlob((blob) => {
                            this.preview = canvas.toDataURL('image/jpeg', 0.95);
                            this.croppedLabel = 'Imagem com corte aplicado';
                            const file = new File([blob], 'aviso_crop.png', { type: 'image/png' });
                            const dt = new DataTransfer();
                            dt.items.add(file);
                            this.$refs.livewireInput.files = dt.files;
                            this.$refs.livewireInput.dispatchEvent(new Event('change', { bubbles: true }));
                            this.fecharCropModal();
                        }, 'image/png');
                    },
                    fecharCropModal() {
                        this.showCropModal = false;
                        if (this.cropperInstance) { this.cropperInstance.destroy(); this.cropperInstance = null; }
                    }
                }">
                    <label class="cme-label">Imagem (opcional)</label>

                    {{-- Imagem atual guardada --}}
                    @if($imagemAtual)
                    <div class="flex items-center gap-3 mb-2 p-2 rounded-lg" style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);">
                        <img src="{{ Storage::url($imagemAtual) }}" class="w-16 h-16 object-cover rounded-lg">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold" style="color:#4A4845;">Imagem atual</p>
                            <p class="text-xs cme-muted truncate">{{ basename($imagemAtual) }}</p>
                        </div>
                        <button wire:click="removerImagem" type="button"
                                style="background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); font-size:11px; font-weight:600; padding:4px 10px; border-radius:6px; cursor:pointer;">
                            Remover
                        </button>
                    </div>
                    @endif

                    {{-- Preview do crop --}}
                    <div x-show="preview" class="mb-2">
                        <img :src="preview" class="w-full max-h-44 object-contain rounded-lg" style="border:1px solid rgba(15,110,86,0.3);">
                        <p class="text-xs mt-1" style="color:#0F6E56;" x-text="croppedLabel"></p>
                    </div>

                    {{-- Botão selecionar --}}
                    <button type="button" @click="openPicker()"
                            class="flex items-center gap-2 w-full rounded-lg px-3 py-3 transition cursor-pointer"
                            style="border:1px dashed rgba(9,20,59,0.25); background:#F0EEEB;"
                            onmouseover="this.style.background='#E4E2DF';" onmouseout="this.style.background='#F0EEEB';">
                        <svg class="w-5 h-5 shrink-0" style="color:#7A7775;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs" style="color:#4A4845;" x-text="preview ? 'Escolher outra imagem…' : '{{ $imagemAtual ? 'Substituir imagem…' : 'Selecionar imagem e recortar…' }}'">
                        </span>
                    </button>

                    {{-- Input picker nativo (não ligado ao Livewire) --}}
                    <input x-ref="pickerInput" type="file" accept="image/jpeg,image/png,image/webp,image/gif"
                           class="hidden" @change="onFileSelected($event)">

                    {{-- Input oculto ligado ao Livewire --}}
                    <input x-ref="livewireInput" wire:model="novaImagem" type="file"
                           accept="image/jpeg,image/png,image/webp,image/gif,image/png" class="hidden">

                    <p class="cme-muted text-xs mt-1">JPEG, PNG ou WebP · Recortado a máx. 500×500 px · ≤1 MB</p>
                    @error('novaImagem') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    {{-- Layout da imagem --}}
                    @if($imagemAtual || $novaImagem)
                    <div class="mt-3">
                        <label class="cme-label mb-1.5">Posição da imagem na TV</label>
                        <div class="flex gap-2">
                            <button type="button" wire:click="$set('layoutImagem','lateral')"
                                class="flex-1 flex flex-col items-center gap-1.5 px-3 py-2.5 rounded-lg border text-xs font-semibold transition-colors"
                                style="{{ $layoutImagem === 'lateral' ? 'border:2px solid #09143B; background:#09143B; color:#FFD300;' : 'border:1px solid rgba(9,20,59,0.20); background:#F0EEEB; color:#4A4845;' }}">
                                {{-- Icon: image on side --}}
                                <svg viewBox="0 0 40 28" width="40" height="28" fill="none">
                                    <rect x="0.5" y="0.5" width="39" height="27" rx="3" stroke="currentColor" stroke-opacity="0.4"/>
                                    <rect x="1" y="1" width="14" height="26" rx="2" fill="currentColor" fill-opacity="0.35"/>
                                    <rect x="18" y="5" width="18" height="3" rx="1.5" fill="currentColor" fill-opacity="0.6"/>
                                    <rect x="18" y="11" width="14" height="2" rx="1" fill="currentColor" fill-opacity="0.4"/>
                                    <rect x="18" y="15" width="16" height="2" rx="1" fill="currentColor" fill-opacity="0.4"/>
                                    <rect x="18" y="19" width="12" height="2" rx="1" fill="currentColor" fill-opacity="0.4"/>
                                </svg>
                                Lateral
                            </button>
                            <button type="button" wire:click="$set('layoutImagem','topo')"
                                class="flex-1 flex flex-col items-center gap-1.5 px-3 py-2.5 rounded-lg border text-xs font-semibold transition-colors"
                                style="{{ $layoutImagem === 'topo' ? 'border:2px solid #09143B; background:#09143B; color:#FFD300;' : 'border:1px solid rgba(9,20,59,0.20); background:#F0EEEB; color:#4A4845;' }}">
                                {{-- Icon: image on top --}}
                                <svg viewBox="0 0 40 28" width="40" height="28" fill="none">
                                    <rect x="0.5" y="0.5" width="39" height="27" rx="3" stroke="currentColor" stroke-opacity="0.4"/>
                                    <rect x="1" y="1" width="38" height="13" rx="2" fill="currentColor" fill-opacity="0.35"/>
                                    <rect x="4" y="18" width="20" height="3" rx="1.5" fill="currentColor" fill-opacity="0.6"/>
                                    <rect x="4" y="23" width="28" height="2" rx="1" fill="currentColor" fill-opacity="0.4"/>
                                </svg>
                                Topo (horizontal)
                            </button>
                        </div>
                    </div>
                    @endif

                    {{-- Modal de Crop --}}
                    <div x-show="showCropModal" x-cloak
                         class="fixed inset-0 z-70 flex items-center justify-center bg-black/80"
                         @keydown.escape.window="fecharCropModal()">
                        <div class="bg-blue-950 border border-blue-600 rounded-2xl shadow-2xl w-full max-w-2xl mx-4 p-5 flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <span class="text-white font-bold text-base">✂️ Recortar imagem</span>
                                <button type="button" @click="fecharCropModal()"
                                        class="text-blue-400 hover:text-white text-xl leading-none">&times;</button>
                            </div>
                            {{-- Área do cropper --}}
                            <div style="max-height:420px;overflow:hidden;border-radius:10px;background:#0f172a;">
                                <img x-ref="cropImg" src="" alt="" style="display:block;max-width:100%;">
                            </div>
                            {{-- Controlos --}}
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <div class="flex gap-2 flex-wrap">
                                    <button type="button" @click="cropperInstance.rotate(-90)"
                                            class="text-xs bg-blue-800 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg" title="Rodar 90° esquerda">↺ 90°</button>
                                    <button type="button" @click="cropperInstance.rotate(90)"
                                            class="text-xs bg-blue-800 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg" title="Rodar 90° direita">↻ 90°</button>
                                    <button type="button" @click="cropperInstance.zoom(0.1)"
                                            class="text-xs bg-blue-800 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg">＋ Zoom</button>
                                    <button type="button" @click="cropperInstance.zoom(-0.1)"
                                            class="text-xs bg-blue-800 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg">－ Zoom</button>
                                    <button type="button" @click="cropperInstance.reset()"
                                            class="text-xs bg-blue-800 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg">↺ Reset</button>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" @click="fecharCropModal()"
                                            class="text-sm text-blue-300 hover:text-white px-4 py-2 rounded-lg border border-blue-700 hover:border-blue-500 transition">
                                        Cancelar
                                    </button>
                                    <button type="button" @click="aplicarCorte()"
                                            class="text-sm bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold px-5 py-2 rounded-lg transition">
                                        ✓ Aplicar corte
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cor + Ordem --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="cme-label">Cor do destaque</label>
                        <select wire:model="cor" class="cme-input mt-1">
                            <option value="azul">🔵 Azul</option>
                            <option value="verde">🟢 Verde</option>
                            <option value="amarelo">🟡 Amarelo</option>
                            <option value="vermelho">🔴 Vermelho</option>
                            <option value="roxo">🟣 Roxo</option>
                            <option value="branco">⚪ Branco</option>
                        </select>
                    </div>
                    <div>
                        <label class="cme-label">Ordem</label>
                        <input wire:model="ordem" type="number" min="0" class="cme-input mt-1">
                    </div>
                </div>
                {{-- Ativo --}}
                <div class="flex items-center gap-3">
                    <input wire:model="ativo" type="checkbox" id="ativo-check"
                           class="w-4 h-4 accent-[#09143B] cursor-pointer">
                    <label for="ativo-check" class="text-sm font-semibold cursor-pointer" style="color:#4A4845;">Exibir na TV (ativo)</label>
                </div>
            </div>

            <div class="flex justify-end gap-3 px-6 py-4" style="background:white; border-top:1px solid rgba(9,20,59,0.08);">
                <button wire:click="$set('showModal', false)" class="btn-cme-secondary">
                    Cancelar
                </button>
                <button wire:click="guardar"
                    style="background:#FFD300; color:#09143B; font-weight:700; font-size:13px; padding:10px 20px; border-radius:8px; border:none; cursor:pointer;">
                    Guardar
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Modal Eliminar ────────────────────────────────────── --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
        <div class="w-full max-w-sm mx-4 rounded-2xl overflow-hidden shadow-2xl border border-[rgba(163,45,45,0.30)]">
            <div style="background:#09143B;" class="px-5 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" style="color:#FFD300;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span class="text-white font-medium text-sm">Eliminar aviso?</span>
            </div>
            <div style="background:white;" class="px-6 py-5">
                <p class="text-sm mb-5" style="color:#4A4845;">Esta ação não pode ser desfeita.</p>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-cme-secondary">
                        Cancelar
                    </button>
                    <button wire:click="eliminar"
                        style="background:#A32D2D; color:white; font-weight:700; font-size:13px; padding:10px 20px; border-radius:8px; border:none; cursor:pointer;">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
@endpush
