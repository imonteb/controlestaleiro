<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
        <x-global-footer />
    </flux:main>
</x-layouts::app.sidebar>
