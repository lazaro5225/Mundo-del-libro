
@extends('layouts.app')

@section('titulo', 'Registrar préstamo')

@section('contenido')
    @php
        $campo = 'w-full rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm text-cielo-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cielo-300';
        $etiqueta = 'block mb-1 text-sm font-medium text-cielo-900';
        $lista = 'hidden absolute left-0 right-0 z-20 mt-1 max-h-60 overflow-y-auto rounded-md border border-cielo-200 bg-white shadow-lg';

        $fechaPrestamoInicial = old('fecha_prestamo', now()->format('Y-m-d'));
        $fechaLimiteInicial = old('fecha_limite', now()->addDays(7)->format('Y-m-d'));
    @endphp

    <div class="pb-5 mb-6 border-b border-pergamino-200">
        <p class="text-[11px] uppercase tracking-[0.18em] text-cielo-700 font-medium">Mostrador de préstamos</p>
        <h2 class="font-serif text-3xl font-semibold text-cielo-900 mt-0.5">Registrar préstamo</h2>
        <p class="text-sm text-slate-500 mt-1">Elija al lector, al libro y las fechas del préstamo.</p>
    </div>

    <form method="POST" action="{{ route('prestamos.store') }}" id="form-prestamo"
          class="grid gap-10 md:grid-cols-5 items-start">
        @csrf

        <div class="md:col-span-2 md:sticky md:top-24">
            <article class="relative rounded-md border border-pergamino-300 bg-pergamino-50/75 shadow-md overflow-hidden">
                <div class="h-1.5 bg-cielo-700"></div>

                <div class="p-6 pb-12 bg-white/75">
                    <p class="text-[11px] uppercase tracking-[0.16em] text-slate-400">Comprobante de sala</p>
                    <p class="font-serif text-xl font-semibold text-cielo-900 mt-0.5">Ficha de préstamo</p>
                    <p class="text-xs italic font-serif text-slate-500">Mundo del Libro</p>

                    <dl class="mt-5 text-sm">
                        <div class="py-2.5 border-b border-dashed border-pergamino-300">
                            <dt class="text-xs text-slate-500">Lector</dt>
                            <dd id="pvLector" class="font-medium text-slate-400">Sin elegir</dd>
                        </div>

                        <div class="py-2.5 border-b border-dashed border-pergamino-300">
                            <dt class="text-xs text-slate-500">Libro</dt>
                            <dd id="pvLibro" class="font-serif text-base font-semibold text-slate-400">Sin elegir</dd>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-4">
                            <div class="rounded-md border border-dashed border-pergamino-300 bg-pergamino-50 px-3 py-2 text-center">
                                <dt class="text-xs text-slate-500">Fecha de préstamo</dt>
                                <dd id="pvHoy" class="text-sm font-medium text-cielo-900"></dd>
                            </div>

                            <div class="rounded-md border border-dashed border-cielo-300 bg-cielo-50 px-3 py-2 text-center">
                                <dt class="text-xs text-slate-500">Fecha de devolución</dt>
                                <dd id="pvLimite" class="text-sm font-semibold text-cielo-900"></dd>
                            </div>
                        </div>
                    </dl>
                </div>

                <span class="absolute bottom-4 left-1/2 -translate-x-1/2 size-4 rounded-full bg-pergamino-100 border border-pergamino-300 shadow-inner" aria-hidden="true"></span>
            </article>
        </div>

        <div class="md:col-span-3">
            <div class="rounded-md border border-pergamino-300 bg-white shadow-md overflow-hidden">
                <div class="h-1.5 bg-cielo-700"></div>

                <div class="bg-pergamino-50/80 px-6 py-2.5 border-b border-dashed border-pergamino-300 text-[11px] uppercase tracking-[0.16em] text-slate-400">
                    Asignación de ejemplar
                </div>

                <div class="p-6 sm:p-8 space-y-6">

                    {{-- Seleccionar lector --}}
                    <div>
                        <label for="lector_buscar" class="{{ $etiqueta }}">Lector</label>

                        <div class="relative">
                            <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id') }}">
                            <input type="hidden" name="user_label" id="user_label" value="{{ old('user_label') }}">

                            <input type="text" id="lector_buscar" autocomplete="off"
                                   placeholder="Escribe el nombre o correo del lector..."
                                   value="{{ old('user_label') }}" class="{{ $campo }}">

                            <ul id="lector_lista" role="listbox" class="{{ $lista }}"></ul>
                        </div>

                        <p id="lector_ok" class="hidden mt-1 text-xs font-medium text-emerald-700">Lector seleccionado</p>
                        <p id="lector_error" class="hidden mt-1 text-xs text-rose-700"></p>
                        <p class="mt-1 text-xs text-slate-500">Escribe para buscar y elige un lector de la lista.</p>

                        @error('user_id')
                            <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Seleccionar libro --}}
                    <div>
                        <label for="libro_buscar" class="{{ $etiqueta }}">Libro</label>

                        <div class="relative">
                            <input type="hidden" name="book_id" id="book_id" value="{{ old('book_id') }}">
                            <input type="hidden" name="book_label" id="book_label" value="{{ old('book_label') }}">

                            <input type="text" id="libro_buscar" autocomplete="off"
                                   placeholder="Escribe el título o autor del libro..."
                                   value="{{ old('book_label') }}" class="{{ $campo }}">

                            <ul id="libro_lista" role="listbox" class="{{ $lista }}"></ul>
                        </div>

                        <p id="libro_ok" class="hidden mt-1 text-xs font-medium text-emerald-700">Libro seleccionado</p>
                        <p id="libro_error" class="hidden mt-1 text-xs text-rose-700"></p>
                        <p class="mt-1 text-xs text-slate-500">Solo aparecen libros activos con ejemplares disponibles.</p>

                        @error('book_id')
                            <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Calendario de fecha de préstamo --}}
                    <div>
                        <label for="fecha_prestamo" class="{{ $etiqueta }}">Fecha en que se entregó el libro</label>

                        <input
                            type="date"
                            name="fecha_prestamo"
                            id="fecha_prestamo"
                            value="{{ $fechaPrestamoInicial }}"
                            required
                            class="{{ $campo }}"
                        >

                        <p class="mt-1 text-xs text-slate-500">
                            Selecciona el día en que se realizó el préstamo.
                        </p>

                        @error('fecha_prestamo')
                            <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Calendario de fecha límite --}}
                    <div>
                        <label for="fecha_limite" class="{{ $etiqueta }}">Fecha en que debe devolver el libro</label>

                        <input
                            type="date"
                            name="fecha_limite"
                            id="fecha_limite"
                            value="{{ $fechaLimiteInicial }}"
                            min="{{ $fechaPrestamoInicial }}"
                            required
                            class="{{ $campo }}"
                        >

                        <p class="mt-1 text-xs text-slate-500">
                            Selecciona el día límite para que el lector devuelva el libro.
                        </p>

                        @error('fecha_limite')
                            <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-5 border-t border-pergamino-200">
                        <a href="{{ route('prestamos.index') }}" class="text-sm text-cielo-700 hover:underline">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="rounded-md bg-cielo-700 px-5 py-2 text-sm font-medium text-white hover:bg-cielo-800 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                            Registrar préstamo
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const $ = (id) => document.getElementById(id);

    const fechaCorta = (valor) => {
        if (!valor) return 'Sin seleccionar';

        const partes = valor.split('-');
        return `${partes[2]}/${partes[1]}/${partes[0]}`;
    };

    function actualizarFicha() {
        const fechaPrestamo = $('fecha_prestamo').value;
        const fechaLimite = $('fecha_limite').value;

        $('pvHoy').textContent = fechaCorta(fechaPrestamo);
        $('pvLimite').textContent = fechaCorta(fechaLimite);

        const poner = (el, texto) => {
            el.textContent = texto || 'Sin elegir';
            el.classList.toggle('text-slate-400', !texto);
            el.classList.toggle('text-cielo-900', !!texto);
        };

        poner($('pvLector'), $('user_id').value ? $('user_label').value : '');
        poner($('pvLibro'), $('book_id').value ? $('book_label').value : '');

        $('lector_ok').classList.toggle('hidden', !$('user_id').value);
        $('libro_ok').classList.toggle('hidden', !$('book_id').value);
    }

    function actualizarFechaMinima() {
        const fechaPrestamo = $('fecha_prestamo').value;

        $('fecha_limite').min = fechaPrestamo;

        if ($('fecha_limite').value && $('fecha_limite').value < fechaPrestamo) {
            const fecha = new Date(fechaPrestamo + 'T00:00:00');
            fecha.setDate(fecha.getDate() + 7);

            const anio = fecha.getFullYear();
            const mes = String(fecha.getMonth() + 1).padStart(2, '0');
            const dia = String(fecha.getDate()).padStart(2, '0');

            $('fecha_limite').value = `${anio}-${mes}-${dia}`;
        }

        actualizarFicha();
    }

    function activarBuscador(cfg) {
        const input = $(cfg.input);
        const hidden = $(cfg.hidden);
        const label = $(cfg.label);
        const lista = $(cfg.lista);
        const error = $(cfg.error);

        let temporizador = null;
        let opciones = [];
        let activo = -1;

        const claseItem = 'px-3 py-2 text-sm border-b border-pergamino-100 last:border-0';

        function cerrar() {
            lista.innerHTML = '';
            lista.classList.add('hidden');
            opciones = [];
            activo = -1;
        }

        function elegir(item) {
            hidden.value = item.id;
            label.value = item.texto;
            input.value = item.texto;

            error.classList.add('hidden');

            cerrar();
            actualizarFicha();
        }

        function marcar(i) {
            opciones.forEach((o, n) => {
                o.li.classList.toggle('bg-cielo-100', n === i);
            });

            activo = i;

            if (opciones[i]) {
                opciones[i].li.scrollIntoView({ block: 'nearest' });
            }
        }

        function mensaje(texto) {
            const li = document.createElement('li');
            li.className = claseItem + ' text-slate-500';
            li.textContent = texto;
            lista.appendChild(li);
        }

        async function buscar() {
            try {
                const res = await fetch(cfg.url + '?q=' + encodeURIComponent(input.value.trim()), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!res.ok) throw new Error('Respuesta no válida');

                const datos = await res.json();

                lista.innerHTML = '';
                opciones = [];
                activo = -1;

                if (datos.length === 0) {
                    mensaje('Sin resultados');
                }

                datos.forEach(function (item) {
                    const li = document.createElement('li');

                    li.setAttribute('role', 'option');
                    li.textContent = item.texto;

                    if (item.bloqueado) {
                        li.className = claseItem + ' text-slate-400 bg-slate-50 cursor-not-allowed';
                        li.textContent += ' — tiene préstamos vencidos';
                    } else {
                        li.className = claseItem + ' cursor-pointer hover:bg-pergamino-50';

                        li.addEventListener('mousedown', function (e) {
                            e.preventDefault();
                            elegir(item);
                        });

                        opciones.push({ li, item });
                    }

                    lista.appendChild(li);
                });

                lista.classList.remove('hidden');

            } catch (e) {
                lista.innerHTML = '';
                mensaje('No se pudo buscar. Recarga la página o inicia sesión de nuevo.');
                lista.classList.remove('hidden');
            }
        }

        input.addEventListener('input', function () {
            hidden.value = '';
            label.value = '';

            actualizarFicha();

            clearTimeout(temporizador);
            temporizador = setTimeout(buscar, 250);
        });

        input.addEventListener('focus', buscar);

        input.addEventListener('blur', function () {
            setTimeout(cerrar, 150);
        });

        input.addEventListener('keydown', function (e) {
            if (lista.classList.contains('hidden') || opciones.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                marcar((activo + 1) % opciones.length);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                marcar((activo - 1 + opciones.length) % opciones.length);
            } else if (e.key === 'Enter' && activo >= 0) {
                e.preventDefault();
                elegir(opciones[activo].item);
            } else if (e.key === 'Escape') {
                cerrar();
            }
        });
    }

    activarBuscador({
        input: 'lector_buscar',
        hidden: 'user_id',
        label: 'user_label',
        lista: 'lector_lista',
        error: 'lector_error',
        url: @json(route('prestamos.buscar.lectores'))
    });

    activarBuscador({
        input: 'libro_buscar',
        hidden: 'book_id',
        label: 'book_label',
        lista: 'libro_lista',
        error: 'libro_error',
        url: @json(route('prestamos.buscar.libros'))
    });

    $('fecha_prestamo').addEventListener('change', actualizarFechaMinima);
    $('fecha_limite').addEventListener('change', actualizarFicha);

    $('form-prestamo').addEventListener('submit', function (e) {
        const faltantes = [
            {
                id: 'user_id',
                error: 'lector_error',
                input: 'lector_buscar',
                texto: 'Busca y selecciona un lector de la lista.'
            },
            {
                id: 'book_id',
                error: 'libro_error',
                input: 'libro_buscar',
                texto: 'Busca y selecciona un libro de la lista.'
            },
        ].filter((c) => !$(c.id).value);

        ['lector_error', 'libro_error'].forEach((id) => {
            $(id).classList.add('hidden');
        });

        if (faltantes.length) {
            e.preventDefault();

            faltantes.forEach((c) => {
                $(c.error).textContent = c.texto;
                $(c.error).classList.remove('hidden');
            });

            $(faltantes[0].input).focus();
            return;
        }

        if ($('fecha_limite').value <= $('fecha_prestamo').value) {
            e.preventDefault();
            alert('La fecha de devolución debe ser posterior a la fecha del préstamo.');
            $('fecha_limite').focus();
        }
    });

    actualizarFicha();
</script>
@endpush
