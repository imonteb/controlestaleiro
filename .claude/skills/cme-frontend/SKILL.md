---
name: cme-frontend
description: >
  Diseño y maquetación de interfaces para ControlEstaleiro (CME C016) usando
  Laravel 11 + Livewire 4 + Flux UI + Alpine.js + Tailwind CSS.
  Usar este skill SIEMPRE que se vaya a crear o modificar una vista Blade,
  componente Livewire, tabla, formulario, dashboard, o cualquier elemento visual
  del proyecto. También usar cuando el usuario mencione "estilo CME", "diseño app",
  "reestilizar", "mejorar UI", "aplicar la paleta" o cualquier cambio visual.
  Este skill define la fuente única de verdad para decisiones de diseño en CME C016.
---

# CME Frontend — Guía de diseño y componentes

## Stack y restricciones absolutas

- **Framework**: Laravel 11 + Livewire 4 + Flux UI + Alpine.js + Tailwind CSS
- **Colores**: siempre en el blade correspondiente, **nunca en `app.css` global**
- **Tema**: dark mode en desktop (`/`), tema propio en `/phone`
- **Clases Tailwind**: usar utilities estándar; clases arbitrarias `[#hex]` solo para la paleta CME
- **Flux UI**: usar los componentes Flux nativos (`flux:table`, `flux:modal`, `flux:badge`, etc.) antes de crear HTML custom

---

## Paleta CME — tokens de diseño

### Colores base
```
--cme-dark:        #09143B   ← azul oscuro CME (header, sidebar, botón primary)
--cme-dark2:       #0d1a4a   ← azul medio (sub-header, tabs activos)
--cme-yellow:      #FFD300   ← amarillo CME (acentos, badges, texto en dark)
```

### Grises del body (estilo híbrido)
```
--cme-gray-bg:      #EEECEA   ← fondo de página / body principal
--cme-gray-surface: #E4E2DF   ← superficies secundarias (sidebar interior, table head)
--cme-gray-card:    #F0EEEB   ← cards, inputs, table rows
--cme-gray-border:  rgba(9,20,59,0.10)   ← borde suave
--cme-gray-border2: rgba(9,20,59,0.16)   ← borde énfasis
```

### Texto sobre grises
```
--cme-text:       #1A1A1A   ← texto principal
--cme-text-mid:   #4A4845   ← texto secundario
--cme-text-muted: #7A7775   ← labels, placeholders, muted
```

### Estados semánticos (sobre fondo gris)
```
verde ok:    bg #d4ede4  text #0F6E56
amarillo:    bg #fdf0c2  text #854F0B
gris off:    bg surface  text muted
rojo danger: bg #fde8e8  text #A32D2D
```

---

## Sistema de layout — estructura híbrida

La app usa un sistema **dark header + body gris** en todas las páginas interiores.

### Estructura de página estándar
```
┌─────────────────────────────────────────┐
│  HEADER dark (#09143B)                  │  ← título página + badge contextual
│  TABS dark (#0d1a4a)  [opcional]        │  ← pestañas de sección
├──────────────────────┬──────────────────┤
│  SIDEBAR gris        │  CONTENIDO gris  │  ← solo en páginas con sub-nav
│  (#E4E2DF)           │  (#EEECEA)       │
└──────────────────────┴──────────────────┘
```

### Sin sidebar (páginas simples)
```
┌─────────────────────────────────────────┐
│  HEADER dark                            │
├─────────────────────────────────────────┤
│  MÉTRICAS (cards grises)                │
├─────────────────────────────────────────┤
│  TABLA o FORMULARIO (card gris)         │
└─────────────────────────────────────────┘
```

---

## Componentes — patrones Blade

### Header de página
```blade
<div class="rounded-t-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
    <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <flux:icon name="ICON" class="text-[#FFD300] w-4 h-4" />
            <span class="text-white font-medium text-sm">Título da página</span>
        </div>
        <span class="text-[10px] bg-[rgba(255,211,0,0.15)] text-[#FFD300]
                     px-2 py-1 rounded font-medium tracking-wide">
            Contexto · Maio 2026
        </span>
    </div>
```

### Tabs (bajo el header)
```blade
    <div class="bg-[#0d1a4a] flex border-b border-[rgba(255,255,255,0.06)] px-4">
        <button wire:click="$set('tab','lista')"
                class="{{ $tab === 'lista'
                    ? 'text-[#FFD300] border-b-2 border-[#FFD300]'
                    : 'text-white/35' }}
                       text-[11px] px-3 py-2">
            Lista
        </button>
        {{-- repetir por cada tab --}}
    </div>
```

### Body wrapper
```blade
    <div class="bg-[#EEECEA] p-4">
        {{-- contenido aquí --}}
    </div>
</div>{{-- cierra el div del header --}}
```

### Grid de métricas (4 columnas)
```blade
<div class="grid grid-cols-4 gap-2 mb-3">
    <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg px-3 py-2.5">
        <div class="text-[10px] uppercase tracking-wide text-[#7A7775] mb-1">Label</div>
        <div class="text-xl font-medium text-[#1A1A1A] leading-none">84</div>
        <div class="text-[10px] text-[#7A7775] mt-1">subtexto</div>
    </div>
</div>
```

### Tabla estándar
```blade
<div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg overflow-hidden">
    {{-- Cabecera --}}
    <div class="grid grid-cols-[2fr_1fr_1fr_80px] px-3 py-2
                bg-[#E4E2DF] border-b border-[rgba(9,20,59,0.08)]
                text-[10px] uppercase tracking-wide text-[#7A7775]">
        <span>Colaborador</span>
        <span>Equipa</span>
        <span>Turno</span>
        <span>Estado</span>
    </div>
    {{-- Filas --}}
    @foreach($items as $item)
    <div class="grid grid-cols-[2fr_1fr_1fr_80px] px-3 py-2
                border-b border-[rgba(9,20,59,0.05)] last:border-0
                text-[12px] text-[#1A1A1A] items-center">
        <span>{{ $item->nome }}</span>
        <span class="text-[#4A4845]">{{ $item->equipa }}</span>
        <span class="text-[#4A4845]">{{ $item->turno }}</span>
        <span>
            <x-cme-badge :estado="$item->estado" />
        </span>
    </div>
    @endforeach
</div>
```

### Formulario
```blade
<div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg p-4">
    <div class="grid grid-cols-2 gap-3 mb-3">
        <div>
            <label class="block text-[10px] uppercase tracking-wide
                          text-[#7A7775] mb-1">Label</label>
            <input type="text"
                   class="w-full text-[12px] px-2.5 py-1.5
                          bg-[#EEECEA] border border-[rgba(9,20,59,0.16)]
                          rounded-lg text-[#1A1A1A]
                          focus:outline-none focus:border-[#09143B]
                          focus:ring-2 focus:ring-[rgba(9,20,59,0.10)]"
                   placeholder="..." />
        </div>
    </div>
    <div class="flex justify-end gap-2 mt-4">
        <flux:button variant="ghost" size="sm">Cancelar</flux:button>
        <flux:button variant="filled" size="sm"
                     class="bg-[#09143B] text-[#FFD300]">
            Guardar
        </flux:button>
    </div>
</div>
```

### Badge de estado
```blade
{{-- Usar el componente x-cme-badge o inline: --}}
@php
$badgeClass = match($estado) {
    'activo'   => 'bg-[#d4ede4] text-[#0F6E56]',
    'pendente' => 'bg-[#fdf0c2] text-[#854F0B]',
    'folga'    => 'bg-[#E4E2DF] text-[#7A7775]',
    'inactivo' => 'bg-[#fde8e8] text-[#A32D2D]',
    default    => 'bg-[#E4E2DF] text-[#7A7775]',
};
@endphp
<span class="inline-block text-[10px] px-1.5 py-0.5 rounded font-medium
             {{ $badgeClass }}">
    {{ ucfirst($estado) }}
</span>
```

---

## Reglas de aplicación

1. **Colores siempre en el blade** — nunca en `app.css` ni en clases Tailwind globales
2. **Flux UI primero** — usar `flux:table`, `flux:modal`, `flux:badge` cuando existan
3. **Tailwind utilities** — clases arbitrarias `[#hex]` solo para la paleta CME definida arriba
4. **Responsive**: mobile usa `/phone` con su propio tema; estas reglas son solo para desktop
5. **Dark mode desktop**: el sidebar de `layouts/app/sidebar.blade.php` mantiene su tema oscuro propio — no aplicar grises del body al sidebar
6. **Commits**: siempre mensajes descriptivos + `Co-Authored-By: Claude`
7. **Verificar en browser** antes de pasar al siguiente cambio

---

## Referencia de archivos clave

Ver `references/estructura.md` para el mapa completo de ficheros del proyecto.

Los componentes reutilizables van en `resources/views/components/`.
Las vistas Livewire van en `resources/views/livewire/`.
El layout principal es `resources/views/layouts/app.blade.php`.
