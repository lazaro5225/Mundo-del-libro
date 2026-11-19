@extends('layouts.app')

@section('titulo', 'Catálogo de Libros - Mundo del Libro')

@section('contenido')
    @php
        $user = auth()->user();
        $esAdmin = $user && $user->hasRole('administrador');
        $esPersonal = $user && $user->hasRole('administrador', 'bibliotecario');
        $hayFiltros = request()->hasAny(['q', 'titulo', 'autor', 'anio', 'category_id', 'disponibilidad', 'estado']);
        $hayAvanzados = request()->hasAny(['titulo', 'autor', 'anio', 'category_id', 'disponibilidad', 'estado']);

        $portadas = ['#346e8f', '#4388ad', '#5a7a96', '#3f7d63', '#7c6a8f', '#a5795a'];
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 pb-5 mb-6 border-b border-pergamino-200">
        <div>
            <p class="text-[11px] uppercase tracking-[0.18em] text-cielo-700 font-medium">Fondo bibliográfico</p>
            <h2 class="font-serif text-3xl font-semibold text-cielo-900 mt-0.5">Inventario de Libros</h2>
            <p class="text-sm text-slate-500 mt-1">Consulte los títulos disponibles en la biblioteca.</p>
        </div>

        @if($esAdmin)
            <a href="{{ route('libros.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-md bg-cielo-700 px-4 py-2 text-sm font-medium text-white shadow-2xs hover:bg-cielo-800 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Registrar Libro
            </a>
        @endif
    </div>

    <form id="formFiltros" method="GET" action="{{ route('libros.index') }}"
          class="rounded-lg border border-pergamino-200 bg-pergamino-50/70 p-4 sm:p-5 shadow-2xs mb-6">

        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por título, autor o categoría..."
                   class="flex-1 rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm text-cielo-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cielo-300">

            <button type="submit"
                    class="rounded-md bg-cielo-700 px-4 py-2 text-sm font-medium text-white hover:bg-cielo-800 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                Buscar
            </button>

            <button type="button" id="btnFiltros" aria-expanded="{{ $hayAvanzados ? 'true' : 'false' }}" aria-controls="filtrosAvanzados"
                    class="rounded-md border border-cielo-200 bg-white px-4 py-2 text-sm font-medium text-cielo-800 hover:bg-cielo-50 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                Más filtros
            </button>
        </div>

        <div id="filtrosAvanzados" class="{{ $hayAvanzados ? '' : 'hidden' }} mt-4 pt-4 border-t border-pergamino-200">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <label class="text-sm">
                    <span class="block mb-1 font-medium text-cielo-900">Título</span>
                    <input type="text" name="titulo" value="{{ request('titulo') }}"
                           class="w-full rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300">
                </label>

                <label class="text-sm">
                    <span class="block mb-1 font-medium text-cielo-900">Autor</span>
                    <input type="text" name="autor" value="{{ request('autor') }}"
                           class="w-full rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300">
                </label>

                <label class="text-sm">
                    <span class="block mb-1 font-medium text-cielo-900">Año</span>
                    <input type="number" name="anio" value="{{ request('anio') }}" min="1000" max="{{ date('Y') }}"
                           class="w-full rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300">
                </label>

                <label class="text-sm">
                    <span class="block mb-1 font-medium text-cielo-900">Categoría</span>
                    <select name="category_id" class="w-full rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300">
                        <option value="">Todas</option>
                        @foreach($categorias as $c)
                            <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </label>

                @if($esPersonal)
                    <label class="text-sm">
                        <span class="block mb-1 font-medium text-cielo-900">Disponibilidad</span>
                        <select name="disponibilidad" class="w-full rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300">
                            <option value="">Todas</option>
                            <option value="disponibles" @selected(request('disponibilidad') === 'disponibles')>Con ejemplares disponibles</option>
                            <option value="agotados" @selected(request('disponibilidad') === 'agotados')>Agotados</option>
                        </select>
                    </label>
                @endif

                @if($esAdmin)
                    <label class="text-sm">
                        <span class="block mb-1 font-medium text-cielo-900">Estado</span>
                        <select name="estado" class="w-full rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300">
                            <option value="">Todos</option>
                            <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                            <option value="baja" @selected(request('estado') === 'baja')>Dados de baja</option>
                        </select>
                    </label>
                @endif
            </div>

            <div class="mt-4 flex items-center gap-4">
                <button type="submit"
                        class="rounded-md bg-cielo-700 px-4 py-2 text-sm font-medium text-white hover:bg-cielo-800 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                    Aplicar filtros
                </button>
                @if($hayFiltros)
                    <a href="{{ route('libros.index') }}" class="text-sm font-medium text-cielo-700 hover:underline">Limpiar filtros</a>
                @endif
            </div>
        </div>
    </form>

    <div class="flex items-center justify-between text-sm text-slate-500 mb-3">
        <p>
            <strong class="font-semibold text-cielo-900">{{ $libros->total() }}</strong> {{ $libros->total() === 1 ? 'libro encontrado' : 'libros encontrados' }}
        </p>
        @if($hayFiltros)
            <span class="text-xs rounded-full bg-cielo-100/80 text-cielo-800 px-2.5 py-0.5">Filtro activo</span>
        @endif
    </div>

    @if($libros->isEmpty())
        <div class="rounded-lg border border-dashed border-pergamino-300 bg-pergamino-50/60 p-12 text-center text-sm text-slate-500">
            @if($hayFiltros)
                No se encontraron libros con los filtros aplicados.
            @else
                No hay libros registrados.
            @endif
        </div>
    @else
        <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 rounded-lg border border-pergamino-200 bg-white/80 shadow-2xs overflow-hidden">
            @foreach ($libros as $libro)
                @php $colorPortada = $portadas[($libro->category_id ?? 0) % count($portadas)]; @endphp

                <article class="libro flex flex-col border-b sm:border-r border-pergamino-200/90 {{ $esAdmin && !$libro->activo ? 'opacity-70 bg-slate-50/60' : '' }}">

                    <div class="px-4 pt-8 pb-0 flex justify-center bg-gradient-to-b from-pergamino-50/70 to-transparent">
                        <a href="{{ route('libros.show', $libro) }}"
                           class="portada relative flex w-36 h-52 sm:w-40 sm:h-60 rounded-l-sm rounded-r-md shadow-lg origin-bottom transition-transform duration-200 hover:-translate-y-2 hover:-rotate-1 focus:outline-none focus:ring-2 focus:ring-cielo-300"
                           aria-label="Ver {{ $libro->titulo }}">

                            <span class="w-3 shrink-0 rounded-l-sm" style="background: {{ $colorPortada }}">
                                <span class="block h-full w-full bg-gradient-to-r from-black/25 to-transparent"></span>
                            </span>

                            @if($libro->imagen_url)
                                <img src="{{ $libro->imagen_url }}" alt="Portada de {{ $libro->titulo }}" loading="lazy"
                                     class="flex-1 min-w-0 object-cover">
                            @else
                                <span class="flex-1 min-w-0 flex items-center justify-center p-3.5 text-center text-white" style="background: {{ $colorPortada }}">
                                    <span class="border border-white/40 w-full h-full flex items-center justify-center p-2">
                                        <span class="font-serif text-base font-semibold leading-tight">{{ $libro->titulo }}</span>
                                    </span>
                                </span>
                            @endif

                            <span class="w-1.5 rounded-r-md border-y border-r border-black/10"
                                  style="background: repeating-linear-gradient(to bottom, #ffffff 0 2px, #e3e8ec 2px 3px)"></span>
                        </a>
                    </div>

                    <div class="h-3 shadow-md" style="background: linear-gradient(to bottom, #d9c4a3, #b99a6b)"></div>
                    <div class="h-1.5 bg-gradient-to-b from-black/10 to-transparent"></div>

                    <div class="flex-1 flex flex-col px-4 pt-3 pb-5">
                        <span class="text-[11px] uppercase tracking-wider text-cielo-700 font-medium">
                            {{ $libro->category->nombre ?? 'Sin categoría' }}
                        </span>
                        <h3 class="font-serif text-lg font-semibold leading-snug text-cielo-900 mt-0.5">{{ $libro->titulo }}</h3>
                        <p class="text-sm italic font-serif text-slate-600">{{ $libro->autor }}</p>
                        <p class="text-xs text-slate-400 mt-1">Edición: {{ $libro->anio ?? 'N/D' }}</p>

                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @if($libro->stock_disponible > 0)
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-800 px-2.5 py-0.5">
                                    <span class="size-1.5 rounded-full bg-emerald-600"></span>
                                    {{ $libro->stock_disponible }} de {{ $libro->stock_total }} disponibles
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium rounded-full bg-rose-50 border border-rose-200/80 text-rose-800 px-2.5 py-0.5">
                                    <span class="size-1.5 rounded-full bg-rose-600"></span>
                                    Agotado · 0/{{ $libro->stock_total }}
                                </span>
                            @endif

                            @if($esAdmin)
                                <span class="text-xs font-medium rounded-full px-2.5 py-0.5 {{ $libro->activo ? 'bg-cielo-50 border border-cielo-200/80 text-cielo-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $libro->activo ? 'Activo' : 'De baja' }}
                                </span>
                            @endif
                        </div>

                        <div class="mt-auto pt-4 border-t border-pergamino-100 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm">
                            <a href="{{ route('libros.show', $libro) }}" class="font-medium text-cielo-700 hover:underline">Ver ficha</a>

                            @if($esAdmin)
                                <a href="{{ route('libros.edit', $libro) }}" class="text-slate-600 hover:text-cielo-800 hover:underline">Editar</a>

                                <form action="{{ route('libros.estado', $libro) }}" method="POST" class="inline"
                                      data-confirmar="¿Confirmas el cambio de estado?" data-boton="Confirmar">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-slate-600 hover:text-cielo-800 hover:underline">
                                        {{ $libro->activo ? 'Dar de baja' : 'Reactivar' }}
                                    </button>
                                </form>

                                <form action="{{ route('libros.destroy', $libro) }}" method="POST" class="inline"
                                      data-confirmar="¿Estás seguro de eliminar permanentemente este libro?" data-boton="Eliminar" data-peligro="1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-700 hover:underline">Eliminar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
    @endif

    <div class="mt-6">
        @include('partials.paginacion', ['p' => $libros])
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
                        class="rounded-md px-4 py-2 text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-cielo-300"></button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const btnFiltros = document.getElementById('btnFiltros');
    const panelFiltros = document.getElementById('filtrosAvanzados');
    btnFiltros.addEventListener('click', () => {
        const abierto = panelFiltros.classList.toggle('hidden') === false;
        btnFiltros.setAttribute('aria-expanded', abierto);
    });

    const formFiltros = document.getElementById('formFiltros');
    formFiltros.querySelectorAll('select').forEach((s) =>
        s.addEventListener('change', () => formFiltros.submit()));

    const ventana = document.getElementById('confirmar');
    const textoConfirmar = document.getElementById('confirmarTexto');
    const botonSi = document.getElementById('confirmarSi');
    let formPendiente = null;

    function abrirConfirmacion(form) {
        formPendiente = form;
        textoConfirmar.textContent = form.dataset.confirmar;
        botonSi.textContent = form.dataset.boton || 'Confirmar';
        botonSi.className = 'rounded-md px-4 py-2 text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-cielo-300 '
            + (form.dataset.peligro ? 'bg-rose-700 hover:bg-rose-800' : 'bg-cielo-700 hover:bg-cielo-800');
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