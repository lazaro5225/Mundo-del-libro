@extends('layouts.app')

@section('titulo', 'Panel del administrador')

@section('contenido')
    @php
        $topLibros = $masPrestados->where('loans_count', '>', 0)->values();
        $maxPrestamos = max(1, (int) $topLibros->max('loans_count'));
        $colores = ['#346e8f', '#5b9fc4', '#7c9bb0', '#8aa9a0', '#9aa6c4'];

        // Cada dato es un libro: portada, lomo y texto
        $tarjetas = [
            ['clave' => 'libros',             'texto' => 'Libros registrados', 'portada' => '#e4f1f9', 'lomo' => '#346e8f', 'texto_color' => '#1f3f52'],
            ['clave' => 'usuarios',           'texto' => 'Usuarios',           'portada' => '#dcebf3', 'lomo' => '#4388ad', 'texto_color' => '#1f3f52'],
            ['clave' => 'prestamos_total',    'texto' => 'Préstamos en total', 'portada' => '#e6edf4', 'lomo' => '#5a7a96', 'texto_color' => '#2a3f52'],
            ['clave' => 'prestamos_activos',  'texto' => 'Préstamos activos',  'portada' => '#e3f1ea', 'lomo' => '#3f7d63', 'texto_color' => '#1f4a38'],
            ['clave' => 'prestamos_vencidos', 'texto' => 'Préstamos vencidos', 'portada' => '#f7e4e6', 'lomo' => '#a5495a', 'texto_color' => '#6b2a37'],
        ];
    @endphp

    <div class="mb-8">
        <h2 class="font-serif text-3xl font-semibold text-cielo-900">Panel del administrador</h2>
        <p class="text-sm text-slate-500 mt-1">Resumen del estado de la biblioteca.</p>
    </div>

    {{-- Resumen --}}
    <section aria-labelledby="t-resumen" class="mb-10">
        <h3 id="t-resumen" class="sr-only">Resumen</h3>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-5">
            @foreach ($tarjetas as $t)
                <div class="relative flex h-52 rounded-l-sm rounded-r-md shadow-md transition-transform duration-200 hover:-translate-y-1 hover:shadow-lg"
                     style="color: {{ $t['texto_color'] }}">

                    {{-- Lomo --}}
                    <div class="relative w-5 shrink-0 rounded-l-sm" style="background: {{ $t['lomo'] }}">
                        <span class="absolute inset-x-0 top-5 h-px bg-white/50"></span>
                        <span class="absolute inset-x-0 top-7 h-px bg-white/50"></span>
                        <span class="absolute inset-x-0 bottom-5 h-px bg-white/50"></span>
                    </div>

                    {{-- Portada --}}
                    <div class="relative flex-1 p-4 pl-3" style="background: {{ $t['portada'] }}">
                        <div class="absolute inset-2 border border-current opacity-20 rounded-sm pointer-events-none"></div>
                        <div class="relative h-full flex flex-col justify-between px-1.5 py-1">
                            <p class="font-serif text-base font-semibold leading-snug">{{ $t['texto'] }}</p>
                            <div>
                                <div class="h-px w-8 bg-current opacity-30 mb-2"></div>
                                <p class="font-serif text-5xl font-semibold leading-none" data-contar="{{ $stats[$t['clave']] }}">{{ $stats[$t['clave']] }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Borde de las páginas --}}
                    <div class="w-1.5 rounded-r-md border-y border-r border-black/10"
                         style="background: repeating-linear-gradient(to bottom, #ffffff 0 2px, #e3e8ec 2px 3px)"></div>
                </div>
            @endforeach
        </div>
    </section>

    <div class="grid gap-8 lg:grid-cols-5">

        {{-- Libros más prestados --}}
        <section class="lg:col-span-3" aria-labelledby="t-top">
            <h3 id="t-top" class="font-serif text-xl font-semibold text-cielo-900 mb-3">Libros más prestados</h3>

            @if($topLibros->isEmpty())
                <div class="rounded-lg border border-dashed border-cielo-200 bg-white p-8 text-center text-sm text-slate-500">
                    Aún no hay préstamos registrados.
                </div>
            @else
                <ol class="space-y-3">
                    @foreach($topLibros as $i => $libro)
                        <li class="flex rounded-lg overflow-hidden border border-cielo-100 bg-white shadow-sm">
                            <div class="w-2.5 shrink-0" style="background: {{ $colores[$i % count($colores)] }}"></div>
                            <div class="flex-1 p-4">
                                <div class="flex items-baseline justify-between gap-4">
                                    <p class="font-serif text-lg font-semibold text-cielo-900">{{ $libro->titulo }}</p>
                                    <p class="text-sm text-slate-500 shrink-0">{{ $libro->loans_count }} préstamo(s)</p>
                                </div>
                                <div class="mt-2 h-1.5 rounded-full bg-cielo-100 overflow-hidden">
                                    <div class="barra h-full rounded-full bg-cielo-500 w-0 transition-[width] duration-700 ease-out"
                                         data-ancho="{{ round($libro->loans_count / $maxPrestamos * 100) }}"></div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>

        {{-- Accesos --}}
        <section class="lg:col-span-2" aria-labelledby="t-accesos">
            <h3 id="t-accesos" class="font-serif text-xl font-semibold text-cielo-900 mb-3">Accesos</h3>
            <div class="grid gap-3">
                @php
                    $accesos = [
                        ['ruta' => route('usuarios.index'),                               'texto' => 'Gestionar usuarios y roles', 'alerta' => false],
                        ['ruta' => route('libros.index'),                                 'texto' => 'Catálogo de libros',         'alerta' => false],
                        ['ruta' => route('prestamos.index'),                              'texto' => 'Historial de préstamos',     'alerta' => false],
                        ['ruta' => route('prestamos.index', ['estado' => 'vencidos']),    'texto' => 'Préstamos vencidos',         'alerta' => $stats['prestamos_vencidos'] > 0],
                        ['ruta' => route('categorias.index'),                             'texto' => 'Categorías',                 'alerta' => false],
                    ];
                @endphp

                @foreach($accesos as $a)
                    <a href="{{ $a['ruta'] }}"
                       class="flex items-center justify-between rounded-lg border bg-white px-4 py-3 text-sm font-medium transition-colors
                              focus:outline-none focus:ring-2 focus:ring-cielo-300
                              {{ $a['alerta'] ? 'border-rose-200 text-rose-800 hover:bg-rose-50' : 'border-cielo-100 text-cielo-800 hover:bg-cielo-50' }}">
                        <span>{{ $a['texto'] }}</span>
                        <svg viewBox="0 0 24 24" class="size-4 opacity-60" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    const reducir = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Los números del resumen suben desde 0 al cargar
    document.querySelectorAll('[data-contar]').forEach((el) => {
        const meta = parseInt(el.dataset.contar, 10) || 0;
        if (reducir || meta === 0) return;
        const inicio = performance.now();
        const duracion = 700;
        const paso = (ahora) => {
            const p = Math.min((ahora - inicio) / duracion, 1);
            el.textContent = Math.round(meta * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(paso);
        };
        requestAnimationFrame(paso);
    });

    // Las barras de "más prestados" se llenan
    requestAnimationFrame(() => {
        document.querySelectorAll('.barra').forEach((b) => { b.style.width = b.dataset.ancho + '%'; });
    });
</script>
@endpush