@extends('layouts.app')

@section('titulo', 'Mi historial')

@section('contenido')
    @php
        $portadas = ['#346e8f', '#4388ad', '#5a7a96', '#3f7d63', '#7c6a8f', '#a5795a'];
    @endphp

    <div class="mb-6">
        <h2 class="font-serif text-3xl font-semibold text-cielo-900">Mi historial de préstamos</h2>
        <p class="text-sm text-slate-500 mt-1">Los libros que has llevado y cuándo debes devolverlos.</p>
    </div>

    @if($prestamos->isEmpty())
        <div class="rounded-lg border border-dashed border-cielo-200 bg-white p-12 text-center">
            <p class="font-serif text-xl text-cielo-900">Aún no tienes préstamos.</p>
            <p class="text-sm text-slate-500 mt-1">Cuando pidas un libro prestado, aparecerá aquí.</p>
            <a href="{{ route('libros.index') }}"
               class="inline-block mt-5 rounded-md bg-cielo-700 px-4 py-2 text-sm font-medium text-white hover:bg-cielo-800 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                Ver el catálogo
            </a>
        </div>
    @else
        {{-- Pestañas (filtran los préstamos de esta página) --}}
        <div id="pestanas" class="flex flex-wrap gap-1 border-b border-cielo-200" role="tablist" aria-label="Filtrar por estado">
            @foreach(['todos' => 'Todos', 'activo' => 'Activos', 'vencido' => 'Vencidos', 'devuelto' => 'Devueltos'] as $clave => $texto)
                <button type="button" role="tab" data-filtro="{{ $clave }}"
                        class="pestana rounded-t-md border border-b-0 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-cielo-300">
                    {{ $texto }} <span class="cuenta ml-1 text-xs opacity-70"></span>
                </button>
            @endforeach
        </div>

        <section id="lista" class="mt-5 grid gap-4 lg:grid-cols-2">
            @foreach($prestamos as $p)
                @php
                    $devuelto = (bool) $p->fecha_devolucion;
                    $diasRestantes = (int) now()->startOfDay()->diffInDays($p->fecha_limite->copy()->startOfDay(), false);
                    $vencido = !$devuelto && $diasRestantes < 0;
                    $estadoClave = $devuelto ? 'devuelto' : ($vencido ? 'vencido' : 'activo');

                    $colorLomo = $devuelto ? '#3f7d63' : ($vencido ? '#a5795a' : '#4388ad');
                    $colorPortada = $portadas[($p->book->category_id ?? 0) % count($portadas)];
                    $claseEstado = $devuelto
                        ? 'bg-emerald-100 text-emerald-800'
                        : ($vencido ? 'bg-rose-100 text-rose-800' : 'bg-cielo-100 text-cielo-800');
                @endphp

                <article class="prestamo flex rounded-md overflow-hidden border border-cielo-100 bg-white shadow-sm" data-estado="{{ $estadoClave }}">

                    {{-- Lomo según el estado --}}
                    <div class="w-3 shrink-0" style="background: {{ $vencido ? '#a5495a' : $colorLomo }}"></div>

                    <div class="flex-1 min-w-0 p-4 flex gap-4">

                        {{-- Libro en miniatura --}}
                        <div class="relative flex w-16 h-24 shrink-0 rounded-l-sm rounded-r-sm shadow-md">
                            <span class="w-1.5 shrink-0 rounded-l-sm" style="background: {{ $colorPortada }}"></span>
                            @if($p->book->imagen_url)
                                <img src="{{ $p->book->imagen_url }}" alt="Portada de {{ $p->book->titulo }}" loading="lazy"
                                     class="flex-1 min-w-0 object-cover">
                            @else
                                <span class="flex-1 min-w-0 flex items-center justify-center p-1 text-center text-white" style="background: {{ $colorPortada }}">
                                    <span class="font-serif text-[10px] font-semibold leading-tight line-clamp-4">{{ $p->book->titulo }}</span>
                                </span>
                            @endif
                            <span class="w-1 rounded-r-sm"
                                  style="background: repeating-linear-gradient(to bottom, #ffffff 0 2px, #e3e8ec 2px 3px)"></span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-serif text-lg font-semibold leading-snug text-cielo-900">{{ $p->book->titulo }}</h3>
                                <span class="shrink-0 text-xs font-medium rounded-full px-2.5 py-1 {{ $claseEstado }}">{{ $p->estadoTexto() }}</span>
                            </div>

                            {{-- Fechas como sellos de la ficha --}}
                            <dl class="mt-3 grid grid-cols-3 gap-2 text-center">
                                <div class="rounded-md border border-dashed border-cielo-200 bg-cielo-50/60 px-1.5 py-1.5">
                                    <dt class="text-[11px] text-slate-500">Préstamo</dt>
                                    <dd class="text-xs font-medium text-cielo-900">{{ $p->fecha_prestamo->format('d/m/Y') }}</dd>
                                </div>
                                <div class="rounded-md border border-dashed px-1.5 py-1.5 {{ $vencido ? 'border-rose-300 bg-rose-50/60' : 'border-cielo-200 bg-cielo-50/60' }}">
                                    <dt class="text-[11px] text-slate-500">Fecha límite</dt>
                                    <dd class="text-xs font-medium {{ $vencido ? 'text-rose-800' : 'text-cielo-900' }}">{{ $p->fecha_limite->format('d/m/Y') }}</dd>
                                </div>
                                <div class="rounded-md border border-dashed border-cielo-200 bg-cielo-50/60 px-1.5 py-1.5">
                                    <dt class="text-[11px] text-slate-500">Devolución</dt>
                                    <dd class="text-xs font-medium text-cielo-900">{{ $p->fecha_devolucion?->format('d/m/Y') ?? '—' }}</dd>
                                </div>
                            </dl>

                            @unless($devuelto)
                                <p class="mt-3 text-sm font-medium {{ $vencido ? 'text-rose-700' : 'text-slate-600' }}">
                                    @if($vencido)
                                        Llevas {{ abs($diasRestantes) }} {{ abs($diasRestantes) === 1 ? 'día' : 'días' }} de retraso. Devuélvelo en la biblioteca.
                                    @elseif($diasRestantes === 0)
                                        Debes devolverlo hoy.
                                    @else
                                        Te quedan {{ $diasRestantes }} {{ $diasRestantes === 1 ? 'día' : 'días' }} para devolverlo.
                                    @endif
                                </p>
                            @endunless
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <p id="sinResultados" class="hidden text-center py-12 text-sm text-slate-500">
            No tienes préstamos con ese estado en esta página.
        </p>
    @endif

    <div class="mt-6">
        @include('partials.paginacion', ['p' => $prestamos])
    </div>
@endsection

@push('scripts')
<script>
    const pestanas = Array.from(document.querySelectorAll('.pestana'));
    const fichas = Array.from(document.querySelectorAll('.prestamo'));
    const sinResultados = document.getElementById('sinResultados');

    const claseActiva = ['bg-white', 'border-cielo-200', 'text-cielo-900', '-mb-px'];
    const claseInactiva = ['bg-cielo-50', 'border-transparent', 'text-slate-600', 'hover:bg-cielo-100', 'hover:text-cielo-800'];

    // Cantidad de préstamos de cada estado en esta página
    pestanas.forEach((p) => {
        const f = p.dataset.filtro;
        const n = f === 'todos' ? fichas.length : fichas.filter((x) => x.dataset.estado === f).length;
        p.querySelector('.cuenta').textContent = n;
    });

    function filtrar(filtro) {
        let visibles = 0;
        fichas.forEach((f) => {
            const ver = filtro === 'todos' || f.dataset.estado === filtro;
            f.classList.toggle('hidden', !ver);
            if (ver) visibles++;
        });

        pestanas.forEach((p) => {
            const activa = p.dataset.filtro === filtro;
            p.setAttribute('aria-selected', activa);
            claseActiva.forEach((c) => p.classList.toggle(c, activa));
            claseInactiva.forEach((c) => p.classList.toggle(c, !activa));
        });

        if (sinResultados) sinResultados.classList.toggle('hidden', visibles > 0);
    }

    pestanas.forEach((p) => p.addEventListener('click', () => filtrar(p.dataset.filtro)));
    if (pestanas.length) filtrar('todos');
</script>
@endpush