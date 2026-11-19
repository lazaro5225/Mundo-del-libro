@extends('layouts.app')

@section('titulo', 'Categorías')

@section('contenido')
    @php
        $colores = ['#346e8f', '#4388ad', '#5a7a96', '#3f7d63', '#7c6a8f', '#a5795a'];
        $alturas = [26, 20, 30, 23, 18, 28, 22, 25];
    @endphp

    <div class="pb-5 mb-6 border-b border-pergamino-200">
        <p class="text-[11px] uppercase tracking-[0.18em] text-cielo-700 font-medium">Clasificación</p>
        <h2 class="font-serif text-3xl font-semibold text-cielo-900 mt-0.5">Categorías</h2>
        <p class="text-sm text-slate-500 mt-1">Las secciones en las que se ordenan los libros de la biblioteca.</p>
    </div>

    <form method="POST" action="{{ route('categorias.store') }}"
          class="rounded-lg border border-pergamino-200 bg-pergamino-50/70 p-4 sm:p-5 shadow-2xs mb-6">
        @csrf
        <label for="nombre" class="block mb-1.5 text-sm font-medium text-cielo-900">Nueva categoría</label>
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required placeholder="Por ejemplo: Novela histórica"
                   class="flex-1 rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-md bg-cielo-700 px-4 py-2 text-sm font-medium text-white hover:bg-cielo-800 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Agregar
            </button>
        </div>
        @error('nombre') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
    </form>

    @if($categorias->isEmpty())
        <div class="rounded-lg border border-dashed border-pergamino-300 bg-pergamino-50/60 p-12 text-center text-sm text-slate-500">
            Todavía no hay categorías. Agrega la primera arriba.
        </div>
    @else
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-5">
            <input type="search" id="buscar" placeholder="Buscar categoría..."
                   class="flex-1 rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300">
            <p id="contador" class="text-sm text-slate-500"></p>
        </div>

        <section id="lista" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($categorias as $c)
                @php $color = $colores[$c->id % count($colores)]; @endphp

                <article class="categoria flex flex-col rounded-md overflow-hidden border border-pergamino-200 bg-white shadow-2xs transition-shadow hover:shadow-md"
                         data-nombre="{{ \Illuminate\Support\Str::lower($c->nombre) }}">

                    <div class="flex flex-1">
                        <div class="w-3 shrink-0" style="background: {{ $color }}"></div>

                        <div class="flex-1 min-w-0 p-4">
                            <h3 class="font-serif text-xl font-semibold leading-snug text-cielo-900 break-words">{{ $c->nombre }}</h3>
                            <p class="text-sm text-slate-500 mt-0.5">
                                {{ $c->books_count }} {{ $c->books_count === 1 ? 'libro' : 'libros' }}
                            </p>

                            <div class="mt-4 h-9 flex items-end gap-1 px-1" aria-hidden="true">
                                @for($i = 0; $i < min($c->books_count, 12); $i++)
                                    <span class="w-2 rounded-t-sm"
                                          style="height: {{ $alturas[($i + $c->id) % count($alturas)] + 6 }}px; background: {{ $color }}; opacity: {{ $i % 2 ? '0.7' : '1' }}"></span>
                                @endfor
                                @if($c->books_count > 12)
                                    <span class="ml-1 text-xs text-slate-400 self-center">+{{ $c->books_count - 12 }}</span>
                                @endif
                            </div>
                            <div class="h-2 rounded-sm shadow-2xs" style="background: linear-gradient(to bottom, #d9c4a3, #b99a6b)"></div>
                        </div>
                    </div>

                    <div class="px-4 py-2.5 border-t border-pergamino-200 bg-pergamino-50/70 flex items-center gap-4 text-sm">
                        <a href="{{ route('categorias.edit', $c) }}" class="font-medium text-cielo-700 hover:underline">Editar</a>

                        <form action="{{ route('categorias.destroy', $c) }}" method="POST"
                              data-confirmar="¿Eliminar la categoría «{{ $c->nombre }}»?@if($c->books_count > 0) Tiene {{ $c->books_count }} {{ $c->books_count === 1 ? 'libro' : 'libros' }} asociado(s).@endif"
                              data-boton="Eliminar">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-700 hover:underline">Eliminar</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </section>

        <p id="sinResultados" class="hidden text-center py-12 text-sm text-slate-500">
            Ninguna categoría coincide con la búsqueda.
        </p>
    @endif

    <div id="confirmar" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-cielo-900/40 backdrop-blur-2xs" aria-hidden="true">
        <div class="w-full max-w-sm rounded-lg border border-cielo-200 bg-pergamino-50 p-6 shadow-xl" role="dialog" aria-modal="true" aria-labelledby="confirmarTexto">
            <p id="confirmarTexto" class="font-serif text-lg text-cielo-900"></p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" id="confirmarNo"
                        class="rounded-md border border-cielo-200 bg-white px-4 py-2 text-sm text-cielo-800 hover:bg-cielo-50 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                    Cancelar
                </button>
                <button type="button" id="confirmarSi"
                        class="rounded-md bg-rose-700 px-4 py-2 text-sm font-medium text-white hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                    Eliminar
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const $ = (id) => document.getElementById(id);
    const tarjetas = Array.from(document.querySelectorAll('.categoria'));
    const normalizar = (t) => (t || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

    if ($('buscar')) {
        const filtrar = () => {
            const q = normalizar($('buscar').value.trim());
            let visibles = 0;
            tarjetas.forEach((t) => {
                const ver = normalizar(t.dataset.nombre).includes(q);
                t.classList.toggle('hidden', !ver);
                if (ver) visibles++;
            });
            $('contador').textContent = `${visibles} de ${tarjetas.length} categorías`;
            $('sinResultados').classList.toggle('hidden', visibles > 0);
        };
        $('buscar').addEventListener('input', filtrar);
        filtrar();
    }

    const ventana = $('confirmar');
    const botonSi = $('confirmarSi');
    let formPendiente = null;

    function abrirConfirmacion(form) {
        formPendiente = form;
        $('confirmarTexto').textContent = form.dataset.confirmar;
        ventana.classList.remove('hidden');
        ventana.classList.add('flex');
        $('confirmarNo').focus();
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