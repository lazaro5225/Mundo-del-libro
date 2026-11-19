@extends('layouts.app')

@section('titulo', 'Usuarios')

@section('contenido')
    @php
        $roles = collect($usuarios->items())->map(fn ($u) => $u->role?->nombre)->filter()->unique()->sort()->values();

        // Color de la etiqueta de cada rol
        $clasesRol = [
            'administrador' => 'bg-cielo-100 text-cielo-800',
            'bibliotecario' => 'bg-amber-100 text-amber-800',
            'lector'        => 'bg-slate-100 text-slate-700',
        ];
    @endphp

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="font-serif text-3xl font-semibold text-cielo-900">Usuarios y roles</h2>
            <p class="text-sm text-slate-500 mt-1">Cada usuario tiene su carnet de la biblioteca.</p>
        </div>

        <a href="{{ route('usuarios.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-md bg-cielo-700 px-4 py-2 text-sm font-medium text-white hover:bg-cielo-800 focus:outline-none focus:ring-2 focus:ring-cielo-300">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Crear usuario
        </a>
    </div>

    {{-- Búsqueda y filtros (sobre los usuarios de esta página) --}}
    <div class="rounded-lg border border-cielo-200 bg-white p-4 shadow-sm mb-4 flex flex-col lg:flex-row gap-3 lg:items-center">
        <input type="search" id="buscar" placeholder="Buscar por nombre o correo en esta página..."
               class="flex-1 rounded-md border border-cielo-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300">

        <select id="filtroRol" class="rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300">
            <option value="">Todos los roles</option>
            @foreach($roles as $r)
                <option value="{{ \Illuminate\Support\Str::lower($r) }}">{{ $r }}</option>
            @endforeach
        </select>

        <select id="filtroEstado" class="rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300">
            <option value="">Activos e inactivos</option>
            <option value="activo">Solo activos</option>
            <option value="inactivo">Solo inactivos</option>
        </select>
    </div>

    <p id="contador" class="text-sm text-slate-500 mb-4"></p>

    {{-- Carnets --}}
    <section id="lista" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($usuarios as $u)
            @php
                $nombreRol = $u->role?->nombre;
                $claveRol = \Illuminate\Support\Str::lower($nombreRol ?? '');
                $inicial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($u->name, 0, 1));
                $esYo = $u->id === auth()->id();
            @endphp

            <article class="usuario relative flex flex-col rounded-md border border-cielo-200 bg-white shadow-sm overflow-hidden {{ $u->activo ? '' : 'opacity-75' }}"
                     data-buscar="{{ \Illuminate\Support\Str::lower($u->name . ' ' . $u->email) }}"
                     data-rol="{{ $claveRol }}"
                     data-estado="{{ $u->activo ? 'activo' : 'inactivo' }}">

                <div class="h-1.5 {{ $u->activo ? 'bg-cielo-300' : 'bg-slate-300' }}"></div>

                {{-- Agujero del carnet --}}
                <span class="absolute top-4 right-4 size-3 rounded-full bg-cielo-50 border border-cielo-200 shadow-inner" aria-hidden="true"></span>

                <div class="p-4 flex items-center gap-3">
                    <span class="grid place-items-center size-12 shrink-0 rounded-full bg-cielo-100 text-cielo-800 font-serif text-xl font-semibold" aria-hidden="true">{{ $inicial }}</span>
                    <div class="min-w-0 pr-6">
                        <h3 class="font-serif text-lg font-semibold leading-snug text-cielo-900 truncate">
                            {{ $u->name }}
                            @if($esYo) <span class="ml-1 align-middle text-xs font-sans font-medium text-slate-500">(tú)</span> @endif
                        </h3>
                        <p class="text-sm text-slate-500 truncate">{{ $u->email }}</p>
                    </div>
                </div>

                <dl class="px-4 text-sm">
                    <div class="flex items-center justify-between py-2 border-t border-dashed border-cielo-200">
                        <dt class="text-slate-500">Rol</dt>
                        <dd>
                            @if($nombreRol)
                                <span class="text-xs font-medium rounded-full px-2.5 py-1 {{ $clasesRol[$claveRol] ?? 'bg-slate-100 text-slate-700' }}">{{ $nombreRol }}</span>
                            @else
                                <span class="text-xs text-slate-400">Sin rol</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-center justify-between py-2 border-t border-dashed border-cielo-200">
                        <dt class="text-slate-500">Estado</dt>
                        <dd>
                            <span class="text-xs font-medium rounded-full px-2.5 py-1 {{ $u->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                {{ $u->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </dd>
                    </div>
                </dl>

                <div class="mt-auto px-4 py-3 border-t border-cielo-100 bg-cielo-50/50 flex items-center gap-4 text-sm">
                    <a href="{{ route('usuarios.edit', $u) }}" class="font-medium text-cielo-700 hover:underline">Editar</a>

                    @if(!$esYo)
                        <form method="POST" action="{{ route('usuarios.estado', $u) }}"
                              data-confirmar="¿Confirmas el cambio de estado de {{ $u->name }}?"
                              data-boton="{{ $u->activo ? 'Desactivar' : 'Activar' }}"
                              @if($u->activo) data-peligro="1" @endif>
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="{{ $u->activo ? 'text-rose-700' : 'text-emerald-700' }} hover:underline">
                                {{ $u->activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    @endif
                </div>
            </article>
        @endforeach
    </section>

    <p id="sinResultados" class="hidden text-center py-12 text-sm text-slate-500">
        Ningún usuario de esta página coincide con la búsqueda.
    </p>

    <div class="mt-6">
        @include('partials.paginacion', ['p' => $usuarios])
    </div>

    {{-- Ventana de confirmación --}}
    <div id="confirmar" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-cielo-900/40" aria-hidden="true">
        <div class="w-full max-w-sm rounded-lg bg-white p-6 shadow-xl" role="dialog" aria-modal="true" aria-labelledby="confirmarTexto">
            <p id="confirmarTexto" class="font-serif text-lg text-cielo-900"></p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" id="confirmarNo"
                        class="rounded-md border border-cielo-200 px-4 py-2 text-sm text-cielo-800 hover:bg-cielo-50 focus:outline-none focus:ring-2 focus:ring-cielo-300">
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
    const $ = (id) => document.getElementById(id);
    const fichas = Array.from(document.querySelectorAll('.usuario'));
    const normalizar = (t) => (t || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

    // Búsqueda y filtros instantáneos sobre los usuarios de la página
    function filtrar() {
        const q = normalizar($('buscar').value.trim());
        const rol = $('filtroRol').value;
        const estado = $('filtroEstado').value;
        let visibles = 0;

        fichas.forEach((f) => {
            const ver = normalizar(f.dataset.buscar).includes(q)
                && (!rol || f.dataset.rol === rol)
                && (!estado || f.dataset.estado === estado);
            f.classList.toggle('hidden', !ver);
            if (ver) visibles++;
        });

        $('contador').textContent = `${visibles} de ${fichas.length} usuarios en esta página`;
        $('sinResultados').classList.toggle('hidden', visibles > 0 || fichas.length === 0);
    }
    ['buscar', 'filtroRol', 'filtroEstado'].forEach((id) => $(id).addEventListener('input', filtrar));
    filtrar();

    // Confirmación propia para activar y desactivar
    const ventana = $('confirmar');
    const botonSi = $('confirmarSi');
    let formPendiente = null;

    function abrirConfirmacion(form) {
        formPendiente = form;
        $('confirmarTexto').textContent = form.dataset.confirmar;
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
    $('confirmarNo').addEventListener('click', cerrarConfirmacion);
    ventana.addEventListener('click', (e) => { if (e.target === ventana) cerrarConfirmacion(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') cerrarConfirmacion(); });
</script>
@endpush