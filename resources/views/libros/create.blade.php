@extends('layouts.app')

@section('titulo', 'Registrar Libro - Mundo del Libro')

@section('contenido')
    @php
        $campo = 'w-full rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm text-cielo-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cielo-300';
        $etiqueta = 'block mb-1 text-sm font-medium text-cielo-900';
    @endphp

    <div class="pb-5 mb-6 border-b border-pergamino-200">
        <p class="text-[11px] uppercase tracking-[0.18em] text-cielo-700 font-medium">Alta en inventario</p>
        <h2 class="font-serif text-3xl font-semibold text-cielo-900 mt-0.5">Registrar Nuevo Libro</h2>
        <p class="text-sm text-slate-500 mt-1">Ingrese los datos requeridos para agregar un libro al catálogo.</p>
    </div>

    <form method="POST" action="{{ route('libros.store') }}" enctype="multipart/form-data"
          class="grid gap-10 md:grid-cols-5 items-start">
        @csrf

        <div class="md:col-span-2 md:sticky md:top-24">
            <p class="text-xs uppercase tracking-[0.14em] font-medium text-slate-500 mb-3 text-center">Vista previa en estante</p>

            <div class="flex justify-center px-6">
                <div class="relative flex w-48 h-72 rounded-l-sm rounded-r-md shadow-xl">
                    <span id="pvLomo" class="w-4 shrink-0 rounded-l-sm">
                        <span class="block h-full w-full bg-gradient-to-r from-black/25 to-transparent"></span>
                    </span>

                    <img id="pvImagen" src="" alt="Vista previa de la portada" class="hidden flex-1 min-w-0 object-cover">

                    <div id="pvTexto" class="flex-1 min-w-0 flex items-center justify-center p-4 text-center text-white">
                        <div class="border border-white/40 w-full h-full flex items-center justify-center p-2">
                            <span id="pvTitulo" class="font-serif text-xl font-semibold leading-tight"></span>
                        </div>
                    </div>

                    <span class="w-2 rounded-r-md border-y border-r border-black/10"
                          style="background: repeating-linear-gradient(to bottom, #ffffff 0 2px, #e3e8ec 2px 3px)"></span>
                </div>
            </div>

            <div class="h-3.5 rounded-sm shadow-md" style="background: linear-gradient(to bottom, #d9c4a3, #b99a6b)"></div>
            <div class="h-2 bg-gradient-to-b from-black/10 to-transparent"></div>

            <p id="pvAutor" class="mt-2 text-center font-serif italic text-slate-600"></p>
        </div>

        <div class="md:col-span-3">
            <div class="rounded-md border border-pergamino-300 bg-white shadow-md overflow-hidden">
                <div class="h-1.5 bg-cielo-700"></div>
                <div class="bg-pergamino-50/80 px-6 py-2.5 border-b border-dashed border-pergamino-300 text-[11px] uppercase tracking-[0.16em] text-slate-400">
                    Datos del ejemplar
                </div>

                <div class="p-6 sm:p-8 space-y-5">

                    <div>
                        <label for="titulo" class="{{ $etiqueta }}">Título del libro</label>
                        <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}" required class="{{ $campo }}">
                        @error('titulo') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="autor" class="{{ $etiqueta }}">Autor</label>
                        <input type="text" id="autor" name="autor" value="{{ old('autor') }}" required class="{{ $campo }}">
                        @error('autor') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <div class="flex items-baseline justify-between">
                            <label for="descripcion" class="{{ $etiqueta }}">Descripción <span class="font-normal text-slate-400">(opcional)</span></label>
                            <span id="contador" class="text-xs text-slate-400"></span>
                        </div>
                        <textarea id="descripcion" name="descripcion" rows="5" maxlength="2000" class="{{ $campo }}">{{ old('descripcion') }}</textarea>
                        @error('descripcion') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="rounded-md border border-dashed border-pergamino-300 bg-pergamino-50/70 p-4">
                        <label for="imagen" class="{{ $etiqueta }}">
                            Portada <span class="font-normal text-slate-500">(opcional, JPG, PNG o WEBP, máx. 2 MB)</span>
                        </label>
                        <input type="file" id="imagen" name="imagen" accept="image/png,image/jpeg,image/webp"
                               class="w-full text-sm file:mr-3 file:rounded-md file:border file:border-cielo-200 file:bg-white file:px-3 py-1.5 file:text-sm file:font-medium file:text-cielo-800 hover:file:bg-cielo-50">
                        <p id="errorImagen" class="hidden mt-2 text-xs text-rose-700"></p>
                        @error('imagen') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="anio" class="{{ $etiqueta }}">Año de publicación</label>
                            <input type="number" id="anio" name="anio" value="{{ old('anio') }}" class="{{ $campo }}">
                            @error('anio') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="stock_total" class="{{ $etiqueta }}">Cantidad total de ejemplares</label>
                            <input type="number" id="stock_total" name="stock_total" min="1" value="{{ old('stock_total', 1) }}" required class="{{ $campo }}">
                            @error('stock_total') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="category_id" class="{{ $etiqueta }}">Categoría</label>
                        <select id="category_id" name="category_id" required class="{{ $campo }}">
                            <option value="">-- Selecciona una categoría --</option>
                            @foreach($categorias as $c)
                                <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>
                                    {{ $c->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-5 border-t border-pergamino-200">
                        <a href="{{ route('libros.index') }}" class="text-sm text-cielo-700 hover:underline">Cancelar</a>
                        <button type="submit"
                                class="rounded-md bg-cielo-700 px-5 py-2 text-sm font-medium text-white hover:bg-cielo-800 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                            Guardar Libro
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const portadas = ['#346e8f', '#4388ad', '#5a7a96', '#3f7d63', '#7c6a8f', '#a5795a'];
    const MAX_BYTES = 2 * 1024 * 1024;

    const $ = (id) => document.getElementById(id);
    const inTitulo = $('titulo'), inAutor = $('autor'), inCategoria = $('category_id');
    const inImagen = $('imagen'), inDesc = $('descripcion');
    let urlNueva = null;

    function actualizarVista() {
        const color = portadas[(parseInt(inCategoria.value, 10) || 0) % portadas.length];

        $('pvLomo').style.background = color;
        $('pvTitulo').textContent = inTitulo.value.trim() || 'Título del libro';
        $('pvAutor').textContent = inAutor.value.trim();

        if (urlNueva) {
            $('pvImagen').src = urlNueva;
            $('pvImagen').classList.remove('hidden');
            $('pvTexto').classList.add('hidden');
        } else {
            $('pvImagen').classList.add('hidden');
            $('pvTexto').classList.remove('hidden');
            $('pvTexto').style.background = color;
        }
    }

    function actualizarContador() {
        $('contador').textContent = `${inDesc.value.length} / 2000`;
    }

    inImagen.addEventListener('change', (e) => {
        const archivo = e.target.files[0];
        const error = $('errorImagen');
        error.classList.add('hidden');
        if (urlNueva) { URL.revokeObjectURL(urlNueva); urlNueva = null; }

        if (archivo && archivo.size > MAX_BYTES) {
            error.textContent = 'La imagen pesa más de 2 MB. Elige una más liviana.';
            error.classList.remove('hidden');
            inImagen.value = '';
        } else if (archivo) {
            urlNueva = URL.createObjectURL(archivo);
        }
        actualizarVista();
    });

    [inTitulo, inAutor, inCategoria].forEach((el) => el.addEventListener('input', actualizarVista));
    inDesc.addEventListener('input', actualizarContador);

    actualizarVista();
    actualizarContador();
</script>
@endpush