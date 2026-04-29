<div>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-white uppercase tracking-wider flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-500 rounded-xl flex items-center justify-center text-[#09143B] shadow-[0_0_15px_rgba(234,179,8,0.3)]">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v10a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    Páginas Legais
                </h2>
                <p class="text-blue-300 text-sm mt-1 font-medium pl-14">Gestão do conteúdo jurídico e termos de utilização</p>
            </div>
            <a href="{{ route('legal') }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-blue-800/50 hover:bg-blue-700/50 text-white rounded-lg transition-colors border border-blue-600/30 text-sm font-bold shadow-lg">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Pré-visualizar
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 lg:py-12 px-4 sm:px-6 lg:px-8 animate-in fade-in duration-500">
        
        @if (session()->has('success'))
            <div class="mb-8 p-4 bg-green-500/10 border-l-4 border-green-500 rounded-r-xl flex items-center gap-3 animate-in slide-in-from-top-4">
                <div class="w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center text-green-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h3 class="text-green-400 font-bold text-sm uppercase tracking-wider">Sucesso</h3>
                    <p class="text-green-500/80 text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-blue-900/40 backdrop-blur-xl rounded-3xl border border-blue-700/50 shadow-2xl overflow-hidden">
            
            {{-- Tabs --}}
            <div class="bg-blue-950/50 border-b border-blue-800/50 p-2 flex space-x-2 overflow-x-auto">
                @foreach([
                    'privacidade' => ['Declaração de Privacidade', 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                    'termos' => ['Condições de Utilização', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    'cookies' => ['Política de Cookies', 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z']
                ] as $slug => $data)
                    <button wire:click="$set('tab', '{{ $slug }}')" 
                            class="flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-300 whitespace-nowrap {{ $tab === $slug ? 'bg-blue-600 text-white shadow-lg' : 'text-blue-300/70 hover:bg-blue-800/30 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $data[1] }}" />
                        </svg>
                        {{ $data[0] }}
                    </button>
                @endforeach
            </div>

            {{-- Form Area --}}
            <div class="p-6 md:p-10">
                <form @submit.prevent="saveForm()" class="space-y-8"
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
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {{-- Título --}}
                        <div class="lg:col-span-2 space-y-2">
                            <label class="text-blue-300 text-xs font-bold uppercase tracking-widest pl-1">Título da Página</label>
                            <input type="text" wire:model="titulo" class="w-full bg-blue-950/50 border border-blue-800/50 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all">
                            @error('titulo') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Versão --}}
                        <div class="space-y-2">
                            <label class="text-blue-300 text-xs font-bold uppercase tracking-widest pl-1">Versão</label>
                            <input type="text" wire:model="versao" placeholder="ex: v1.0.0" class="w-full bg-blue-950/50 border border-blue-800/50 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all">
                            @error('versao') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Última Revisão --}}
                        <div class="space-y-2">
                            <label class="text-blue-300 text-xs font-bold uppercase tracking-widest pl-1">Data da Revisão</label>
                            <input type="date" wire:model="ultima_revisao" class="w-full bg-blue-950/50 border border-blue-800/50 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all scheme-dark">
                            @error('ultima_revisao') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Toggle Publicada --}}
                    <div class="flex items-center gap-4 bg-blue-950/30 p-4 rounded-xl border border-blue-800/30">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="publicada" class="sr-only peer">
                            <div class="w-11 h-6 bg-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-500"></div>
                        </label>
                        <div>
                            <span class="text-white font-bold text-sm block">Página Publicada</span>
                            <span class="text-blue-400/70 text-xs">Se desmarcado, esta tab ficará oculta aos utilizadores.</span>
                        </div>
                    </div>

                    {{-- Conteúdo WYSIWYG --}}
                    <div class="space-y-2">
                        <label class="text-blue-300 text-xs font-bold uppercase tracking-widest pl-1">Conteúdo</label>
                        <div wire:ignore class="rounded-2xl overflow-hidden border border-blue-800/50">
                            <div x-ref="editorEl" style="min-height: 420px; background: #050A1F; color: white; font-size: 0.9rem;"></div>
                        </div>
                        @error('conteudo') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Botão Guardar --}}
                    <div class="flex justify-end pt-4 border-t border-blue-800/30">
                        <button type="submit"
                                class="flex items-center gap-2 px-8 py-3.5 bg-yellow-500 hover:bg-yellow-400 text-[#09143B] font-black uppercase tracking-widest text-sm rounded-xl transition-all shadow-[0_0_20px_rgba(234,179,8,0.3)] hover:shadow-[0_0_30px_rgba(234,179,8,0.5)] active:scale-95">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Guardar {{ ucfirst($tab) }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<style>
    .ql-toolbar { background: #0f172a; border-color: rgba(59,130,246,0.3) !important; border-radius: 1rem 1rem 0 0; }
    .ql-toolbar .ql-stroke { stroke: rgba(255,255,255,0.6); }
    .ql-toolbar .ql-fill { fill: rgba(255,255,255,0.6); }
    .ql-toolbar .ql-picker { color: rgba(255,255,255,0.6); }
    .ql-toolbar .ql-picker-options { background: #1e3a5f; border-color: rgba(59,130,246,0.3); }
    .ql-toolbar button:hover .ql-stroke, .ql-toolbar button.ql-active .ql-stroke { stroke: #eab308; }
    .ql-toolbar button:hover .ql-fill, .ql-toolbar button.ql-active .ql-fill { fill: #eab308; }
    .ql-container { border-color: rgba(59,130,246,0.3) !important; border-radius: 0 0 1rem 1rem; font-family: inherit; }
    .ql-editor { color: rgba(255,255,255,0.9); min-height: 420px; }
    .ql-editor.ql-blank::before { color: rgba(255,255,255,0.25); font-style: normal; }
</style>
@endpush
