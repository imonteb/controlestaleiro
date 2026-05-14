<div class="p-6 space-y-6">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-2">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="document-text" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">Páginas Legais</span>
                <span style="color:rgba(255,255,255,0.4); font-size:11px;">— Gestão do conteúdo jurídico</span>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="$set('showVars', true)"
                        class="btn-cme-secondary inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Variáveis Globais
                </button>
                <a href="{{ route('legal') }}" target="_blank"
                   style="background:#FFD300; color:#09143B; font-weight:700; font-size:12px; padding:6px 14px; border-radius:8px; display:inline-flex; align-items:center; gap:6px; text-decoration:none;">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Pré-visualizar
                </a>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="badge-ok px-4 py-3 rounded-xl flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Variáveis Globais --}}
    @if($showVars)
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-5 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" style="color:#FFD300;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span class="text-white font-medium text-sm">Variáveis Globais das Páginas Legais</span>
            </div>
            <button wire:click="$set('showVars', false)" style="background:none; border:none; color:rgba(255,255,255,0.6); cursor:pointer;" class="hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div style="background:#F0EEEB;" class="p-6">
            <p class="cme-muted text-xs mb-4">Utilize <code style="background:rgba(9,20,59,0.08); color:#09143B; padding:1px 5px; border-radius:4px; font-family:monospace;">{nome_variavel}</code> no conteúdo para inserir estes valores automaticamente.</p>
            <form wire:submit="guardarVariaveis">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($labels as $key => $label)
                    <div>
                        <label class="cme-label flex items-center gap-2">
                            <code style="background:rgba(9,20,59,0.08); color:#09143B; padding:1px 5px; border-radius:4px; font-family:monospace; font-size:10px;">{!! '{' . $key . '}' !!}</code>
                            {{ $label }}
                        </label>
                        <input type="{{ str_contains($key, 'email') ? 'email' : 'text' }}"
                               wire:model="variaveis.{{ $key }}"
                               placeholder="{{ $label }}"
                               class="cme-input mt-1">
                        @error('variaveis.'.$key) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    @endforeach
                </div>
                <div class="mt-5 flex items-center justify-between" style="border-top:1px solid rgba(9,20,59,0.08); padding-top:1rem;">
                    <p class="cme-muted text-xs">Campos de DPO: deixar vazios se a empresa não tiver DPO designado (art. 37.º RGPD).</p>
                    <button type="submit"
                            style="background:#FFD300; color:#09143B; font-weight:700; font-size:12px; padding:8px 20px; border-radius:8px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Guardar Variáveis
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Editor de Páginas --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">

        {{-- Tabs --}}
        <div class="bg-[#0d1a4a] flex border-b border-[rgba(255,255,255,0.06)] px-2">
            @foreach([
                'privacidade' => ['Declaração de Privacidade', 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                'termos'      => ['Condições de Utilização', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                'cookies'     => ['Política de Cookies', 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
            ] as $slug => $data)
                <button wire:click="$set('tab', '{{ $slug }}')"
                        class="flex items-center gap-1.5 px-4 py-2.5 text-[11px] font-semibold transition-colors border-b-2 whitespace-nowrap {{ $tab === $slug ? 'text-[#FFD300] border-[#FFD300]' : 'text-white/40 border-transparent hover:text-white/70' }}">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $data[1] }}" />
                    </svg>
                    {{ $data[0] }}
                </button>
            @endforeach
        </div>

        {{-- Form --}}
        <div style="background:#F0EEEB;" class="p-6">
            <form @submit.prevent="saveForm()" class="space-y-5"
                  x-data="{
                      quill: null,
                      initEditor(content) {
                          if (this.quill) {
                              this.quill.root.innerHTML = content || '';
                              return;
                          }
                          this.quill = new Quill(this.$refs.editorEl, {
                              theme: 'snow',
                              modules: {
                                  toolbar: [
                                      [{ header: [1, 2, 3, false] }],
                                      ['bold', 'italic', 'underline'],
                                      [{ list: 'ordered' }, { list: 'bullet' }],
                                      ['link', 'blockquote'],
                                      [{ color: [] }],
                                      ['clean']
                                  ]
                              }
                          });
                          this.quill.root.innerHTML = content || '';
                      },
                      async saveForm() {
                          await $wire.set('conteudo', this.quill ? this.quill.root.innerHTML : '');
                          $wire.call('guardar');
                      }
                  }"
                  x-init="$nextTick(() => initEditor($wire.conteudo)); $watch(() => $wire.conteudo, v => { if (v !== (this.quill?.root.innerHTML ?? '')) initEditor(v); })">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-2">
                        <label class="cme-label">Título da Página</label>
                        <input type="text" wire:model="titulo" class="cme-input mt-1">
                        @error('titulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="cme-label">Versão</label>
                        <input type="text" wire:model="versao" placeholder="ex: v1.0.0" class="cme-input mt-1">
                        @error('versao') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="cme-label">Data da Revisão</label>
                        <input type="date" wire:model="ultima_revisao" class="cme-input mt-1">
                        @error('ultima_revisao') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Toggle Publicada --}}
                <div class="flex items-center gap-4 px-4 py-3 rounded-xl" style="background:white; border:1px solid rgba(9,20,59,0.10);">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="publicada" class="sr-only peer">
                        <div class="w-11 h-6 bg-[#E4E2DF] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#09143B]"></div>
                    </label>
                    <div>
                        <span style="color:#1A1A1A; font-weight:600; font-size:14px;" class="block">Página Publicada</span>
                        <span class="cme-muted text-xs">Se desmarcado, esta tab ficará oculta aos utilizadores.</span>
                    </div>
                </div>

                {{-- Conteúdo WYSIWYG --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="cme-label">Conteúdo</label>
                        <span class="text-xs cme-muted">Use <code style="background:rgba(9,20,59,0.08); color:#09143B; padding:1px 5px; border-radius:4px; font-family:monospace;">{empresa_nome}</code>, <code style="background:rgba(9,20,59,0.08); color:#09143B; padding:1px 5px; border-radius:4px; font-family:monospace;">{empresa_email}</code>, etc.</span>
                    </div>
                    <div wire:ignore class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
                        <div x-ref="editorEl" style="min-height: 420px; background: #050A1F; color: white; font-size: 0.9rem;"></div>
                    </div>
                    @error('conteudo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Guardar --}}
                <div class="flex justify-end pt-2" style="border-top:1px solid rgba(9,20,59,0.08);">
                    <button type="submit"
                            style="background:#FFD300; color:#09143B; font-weight:700; font-size:13px; padding:10px 24px; border-radius:8px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px;">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Guardar {{ ucfirst($tab) }}
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

@push('scripts')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<style>
    .ql-toolbar { background: #09143B; border-color: rgba(9,20,59,0.3) !important; border-radius: 0; padding: 0.6rem 1rem; }
    .ql-toolbar .ql-stroke { stroke: rgba(255,255,255,0.5); }
    .ql-toolbar .ql-fill { fill: rgba(255,255,255,0.5); }
    .ql-toolbar .ql-picker { color: rgba(255,255,255,0.5); }
    .ql-toolbar .ql-picker-options { background: #0d1a4a; border-color: rgba(9,20,59,0.3); }
    .ql-toolbar .ql-picker-label { border-color: rgba(9,20,59,0.3); }
    .ql-toolbar button:hover .ql-stroke, .ql-toolbar button.ql-active .ql-stroke { stroke: #FFD300; }
    .ql-toolbar button:hover .ql-fill, .ql-toolbar button.ql-active .ql-fill { fill: #FFD300; }
    .ql-toolbar .ql-picker-label:hover, .ql-toolbar .ql-picker-label.ql-active { color: #FFD300; }
    .ql-toolbar .ql-picker-label:hover .ql-stroke { stroke: #FFD300; }
    .ql-container { border-color: rgba(9,20,59,0.14) !important; border-radius: 0; font-family: inherit; }
    .ql-editor {
        background: #050d2e; color: rgba(255,255,255,0.75);
        font-size: 0.875rem; line-height: 1.75; min-height: 420px; padding: 2.5rem 3rem;
    }
    .ql-editor.ql-blank::before { color: rgba(255,255,255,0.2); font-style: normal; left: 3rem; }
    .ql-editor h1, .ql-editor h2 { color: #fff; font-size: 1rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; margin: 2.5rem 0 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); }
    .ql-editor h3 { color: #93c5fd; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; margin: 1.75rem 0 0.5rem; }
    .ql-editor p { margin: 0.75rem 0; }
    .ql-editor ul, .ql-editor ol { margin: 0.75rem 0 0.75rem 1.5rem; }
    .ql-editor ul { list-style-type: disc; }
    .ql-editor ol { list-style-type: decimal; }
    .ql-editor li { margin: 0.4rem 0; }
    .ql-editor strong { color: #fff; font-weight: 700; }
    .ql-editor em { color: rgba(255,255,255,0.6); }
    .ql-editor a { color: #60a5fa; text-decoration: underline; }
    .ql-editor blockquote { border-left: 3px solid #FFD300; padding-left: 1rem; margin: 1rem 0; color: rgba(255,255,255,0.5); font-style: italic; border-right: none; }
    .ql-editor code { background: rgba(255,255,255,0.08); color: #93c5fd; padding: 0.1em 0.4em; border-radius: 4px; font-size: 0.8em; font-family: monospace; }
    .ql-editor table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.8rem; }
    .ql-editor th { background: rgba(255,255,255,0.05); color: #fff; font-weight: 800; text-align: left; padding: 0.6rem 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.15); }
    .ql-editor td { padding: 0.55rem 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.06); vertical-align: top; }
    .ql-editor tr:last-child td { border-bottom: none; }
    .ql-editor .ql-indent-1 { padding-left: 2rem; }
    .ql-editor li.ql-indent-1 { padding-left: 2rem; }
</style>
@endpush
