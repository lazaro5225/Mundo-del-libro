@extends('layouts.app')

@section('titulo', 'Préstamos')

@section('contenido')
    @php
        $pestanas = [
            ['clave' => 'todos',     'texto' => 'Todos',     'ruta' => route('prestamos.index')],
            ['clave' => 'activos',   'texto' => 'Activos',   'ruta' => route('prestamos.index', ['estado' => 'activos'])],
            ['clave' => 'vencidos',  'texto' => 'Vencidos',  'ruta' => route('prestamos.index', ['estado' => 'vencidos'])],
            ['clave' => 'devueltos', 'texto' => 'Devueltos', 'ruta' => route('prestamos.index', ['estado' => 'devueltos'])],
        ];
        $claves = ['activos', 'vencidos', 'devueltos'];
        $pestanaActual = in_array($estado, $claves, true) ? $estado : 'todos';
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 pb-5 mb-6 border-b border-pergamino-200">
        <div>
            <p class="text-[11px] uppercase tracking-[0.18em] text-cielo-700 font-medium">Circulación y devoluciones</p>
            <h2 class="font-serif text-3xl font-semibold text-cielo-900 mt-0.5">Historial de préstamos</h2>
            <p class="text-sm text-slate-500 mt-1">Consulte los préstamos y registre las devoluciones.</p>
        </div>

        <a href="{{ route('prestamos.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-md bg-cielo-700 px-4 py-2 text-sm font-medium text-white shadow-2xs hover:bg-cielo-800 focus:outline-none focus:ring-2 focus:ring-cielo-300">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Registrar préstamo
        </a>
    </div>

    <nav class="flex flex-wrap gap-1 border-b border-pergamino-200" aria-label="Filtrar préstamos por estado">
        @foreach($pestanas as $t)
            @php $activa = $pestanaActual === $t['clave']; @endphp
            <a href="{{ $t['ruta'] }}" @if($activa) aria-current="page" @endif
               class="rounded-t-md border border-b-0 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-cielo-300
                      {{ $activa ? 'bg-white border-pergamino-200 text-cielo-900 -mb-px' : 'bg-pergamino-50/70 border-transparent text-slate-600 hover:bg-pergamino-100 hover:text-cielo-900' }}">
                {{ $t['texto'] }}
            </a>
        @endforeach
    </nav>

    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mt-5 mb-5">
        <input type="search" id="buscarPagina" placeholder="Buscar por lector o libro en esta página..."
               class="flex-1 rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300">
        <p id="contador" class="text-sm text-slate-500"></p>
    </div>

    @if($prestamos->isEmpty())
        <div class="rounded-lg border border-dashed border-pergamino-300 bg-pergamino-50/60 p-12 text-center text-sm text-slate-500">
            No hay préstamos para mostrar.
        </div>
    @else
        <section id="lista" class="grid gap-4 lg:grid-cols-2">
            @foreach($prestamos as $p)
                @php
                    $devuelto = (bool) $p->fecha_devolucion;
                    $diasRestantes = (int) now()->startOfDay()->diffInDays($p->fecha_limite->copy()->startOfDay(), false);
                    $vencido = !$devuelto && $diasRestantes < 0;

                    $colorLomo = $devuelto ? '#3f7d63' : ($vencido ? '#a5495a' : '#4388ad');
                    $claseEstado = $devuelto
                        ? 'bg-emerald-50 border border-emerald-200/80 text-emerald-800'
                        : ($vencido ? 'bg-rose-50 border border-rose-200/80 text-rose-800' : 'bg-cielo-50 border border-cielo-200/80 text-cielo-800');
                @endphp

                <article class="prestamo flex rounded-md overflow-hidden border border-pergamino-200 bg-white shadow-2xs transition-shadow hover:shadow-md"
                         data-buscar="{{ \Illuminate\Support\Str::lower($p->user->name . ' ' . $p->book->titulo) }}">

                    <div class="w-3 shrink-0" style="background: {{ $colorLomo }}"></div>

                    <div class="flex-1 min-w-0 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[11px] uppercase tracking-wider text-slate-400">Préstamo #{{ $p->id }}</p>
                                <h3 class="font-serif text-lg font-semibold leading-snug text-cielo-900 mt-0.5">{{ $p->book->titulo }}</h3>
                                <p class="text-sm text-slate-600">Lector: <span class="font-medium text-cielo-900">{{ $p->user->name }}</span></p>
                            </div>
                            <span class="shrink-0 text-xs font-medium rounded-full px-2.5 py-1 {{ $claseEstado }}">{{ $p->estadoTexto() }}</span>
                        </div>

                        <dl class="mt-4 grid grid-cols-3 gap-2 text-center">
                            <div class="rounded-md border border-dashed border-pergamino-300 bg-pergamino-50/70 px-2 py-2">
                                <dt class="text-[11px] text-slate-500">Préstamo</dt>
                                <dd class="text-sm font-medium text-cielo-900">{{ $p->fecha_prestamo->format('d/m/Y') }}</dd>
                            </div>
                            <div class="rounded-md border border-dashed px-2 py-2 {{ $vencido ? 'border-rose-300 bg-rose-50/70' : 'border-pergamino-300 bg-pergamino-50/70' }}">
                                <dt class="text-[11px] text-slate-500">Fecha límite</dt>
                                <dd class="text-sm font-medium {{ $vencido ? 'text-rose-800' : 'text-cielo-900' }}">{{ $p->fecha_limite->format('d/m/Y') }}</dd>
                            </div>
                            <div class="rounded-md border border-dashed border-pergamino-300 bg-pergamino-50/70 px-2 py-2">
                                <dt class="text-[11px] text-slate-500">Devolución</dt>
                                <dd class="text-sm font-medium text-cielo-900">{{ $p->fecha_devolucion?->format('d/m/Y') ?? '—' }}</dd>
                            </div>
                        </dl>

                        @unless($devuelto)
                            <div class="mt-4 pt-3 border-t border-pergamino-100 flex flex-wrap items-center justify-between gap-3">
                                <p class="text-sm font-medium {{ $vencido ? 'text-rose-700' : 'text-slate-600' }}">
                                    @if($vencido)
                                        {{ abs($diasRestantes) }} {{ abs($diasRestantes) === 1 ? 'día' : 'días' }} de retraso
                                    @elseif($diasRestantes === 0)
                                        Vence hoy
                                    @else
                                        Vence en {{ $diasRestantes }} {{ $diasRestantes === 1 ? 'día' : 'días' }}
                                    @endif
                                </p>

                                <form method="POST" action="{{ route('prestamos.devolver', $p) }}"
                                      data-confirmar="¿Registrar la devolución de «{{ $p->book->titulo }}»?">
                                    @csrf
                                    <button type="submit"
                                            class="rounded-md border border-cielo-300 bg-white px-3 py-1.5 text-sm font-medium text-cielo-800 hover:bg-cielo-50 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                                        Registrar devolución
                                    </button>
                                </form>
                            </div>
                        @endunless
                    </div>
                </article>
            @endforeach
        </section>

        <p id="sinResultados" class="hidden text-center py-12 text-sm text-slate-500">
            Ningún préstamo de esta página coincide con la búsqueda.
        </p>
    @endif

    <div class="mt-6">
        @include('partials.paginacion', ['p' => $prestamos])
    </div>

    <div id="confirmar" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-cielo-900/40 backdrop-blur-2xs" aria-hidden="true">
        <div class="w-full max-w-sm rounded-lg border border-cielo-200 bg-pergamino-50 p-6 shadow-xl" role="dialog" aria-modal="true" aria-labelledby="confirmarTexto">
            <p id="confirmarTexto" class="font-serif text-lg text-cielo-900"></p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" id="confirmarNo"
                        class="rounded-md border border-cielo-200 bg-white px-4 py-2 text-sm text-cielo-800 hover:bg-cielo-50 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                    Cancelar
                </button>
                <button type="button" id="confirmarSi"
                        class="rounded-md bg-cielo-700 px-4 py-2 text-sm font-medium text-white hover:bg-cielo-800 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const buscador = document.getElementById('buscarPagina');
    const fichas = Array.from(document.querySelectorAll('.prestamo'));
    const contador = document.getElementById('contador');
    const sinResultados = document.getElementById('sinResultados');
    const normalizar = (t) => (t || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

    function filtrar() {
        const q = normalizar(buscador.value.trim());
        let visibles = 0;
        fichas.forEach((f) => {
            const coincide = normalizar(f.dataset.buscar).includes(q);
            f.classList.toggle('hidden', !coincide);
            if (coincide) visibles++;
        });
        contador.textContent = fichas.length ? `${visibles} de ${fichas.length} en esta página` : '';
        if (sinResultados) sinResultados.classList.toggle('hidden', visibles > 0 || fichas.length === 0);
    }
    buscador.addEventListener('input', filtrar);
    filtrar();

    const ventana = document.getElementById('confirmar');
    const textoConfirmar = document.getElementById('confirmarTexto');
    const botonSi = document.getElementById('confirmarSi');
    let formPendiente = null;

    function abrirConfirmacion(form) {
        formPendiente = form;
        textoConfirmar.textContent = form.dataset.confirmar;
        ventana.classList.remove('hidden');
        ventana.classList.add('flex');
        botonSi.focus();
    }

    function cerrarConfirmacion() {
        ventana.classList.add('hidden');
        ventana.classList.remove('flex');
        formPendiente = null;
    }

    document.querySelectorAll('form[data-confirmar]').forEach((f) =>
        f.addEventListener('submit', (e) => { e.preventDefault(); abrirConfirmacion(f); }));

    botonSi.addEventListener('click', () => { const f = formPendiente; cerrarConfirmacion(); f && f.submit(); });
    document.getElementById('confirmarNo').addEventListener('click', cerrarConfirmacion);
    ventana.addEventListener('click', (e) => { if (e.target === ventana) cerrarConfirmacion(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') cerrarConfirmacion(); });
</script>
@endpush