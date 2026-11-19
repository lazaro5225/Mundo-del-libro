@extends('layouts.app')

@section('titulo', isset($category->id) ? 'Editar categoría' : 'Crear categoría')

@section('contenido')
    @php
        $editando = isset($category->id);

        // Mismos colores que usan las portadas y el listado de categorías
        $colores = ['#346e8f', '#4388ad', '#5a7a96', '#3f7d63', '#7c6a8f', '#a5795a'];
        $color = $editando ? $colores[$category->id % count($colores)] : '#4388ad';
        $alturas = [26, 20, 30, 23, 18, 28, 22, 25];
        $totalLibros = $editando ? (int) ($category->books_count ?? $category->books()->count()) : 0;
    @endphp

    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-3" aria-label="Ruta de navegación">
        <a href="{{ route('categorias.index') }}" class="text-cielo-700 hover:underline">Categorías</a>
        <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
        <span class="font-medium text-cielo-900">{{ $editando ? 'Editar' : 'Crear' }}</span>
    </nav>

    <div class="mb-6">
        <h2 class="font-serif text-3xl font-semibold text-cielo-900">{{ $editando ? 'Editar categoría' : 'Crear categoría' }}</h2>
        <p class="text-sm text-slate-500 mt-1">
            {{ $editando ? 'Modifica el nombre de la categoría «' . $category->nombre . '».' : 'Agrega una nueva sección para ordenar los libros.' }}
        </p>
    </div>

    <form method="POST" action="{{ $editando ? route('categorias.update', $category) : route('categorias.store') }}"
          class="grid gap-10 md:grid-cols-5 items-start">
        @csrf
        @if($editando)
            @method('PUT')
        @endif

        {{-- Vista previa de la sección --}}
        <div class="md:col-span-2 md:sticky md:top-24">
            <p class="text-sm font-medium text-cielo-900 mb-3 text-center">Así se verá en el listado</p>

            <article class="flex rounded-md overflow-hidden border border-cielo-100 bg-white shadow-sm">
                <div class="w-3 shrink-0" style="background: {{ $color }}"></div>

                <div class="flex-1 min-w-0 p-4">
                    <h3 id="pvNombre" class="font-serif text-xl font-semibold leading-snug text-cielo-900 break-words"></h3>
                    <p class="text-sm text-slate-500 mt-0.5">
                        {{ $totalLibros }} {{ $totalLibros === 1 ? 'libro' : 'libros' }}
                    </p>

                    <div class="mt-4 h-9 flex items-end gap-1 px-1" aria-hidden="true">
                        @for($i = 0; $i < min($totalLibros, 12); $i++)
                            <span class="w-2 rounded-t-sm"
                                  style="height: {{ $alturas[($i + $category->id) % count($alturas)] + 6 }}px; background: {{ $color }}; opacity: {{ $i % 2 ? '0.7' : '1' }}"></span>
                        @endfor
                        @if($totalLibros > 12)
                            <span class="ml-1 text-xs text-slate-400 self-center">+{{ $totalLibros - 12 }}</span>
                        @endif
                    </div>
                    <div class="h-2 rounded-sm" style="background: linear-gradient(to bottom, #d9c4a3, #b99a6b)"></div>
                </div>
            </article>
        </div>

        {{-- Datos --}}
        <div class="md:col-span-3">
            <div class="rounded-md border border-cielo-200 bg-white shadow-md overflow-hidden">
                <div class="h-1.5 bg-cielo-300"></div>

                <div class="p-6 sm:p-8 space-y-5">
                    <div>
                        <label for="nombre" class="block mb-1 text-sm font-medium text-cielo-900">
                            Nombre <span class="text-rose-700">*</span>
                        </label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $category->nombre ?? '') }}" required
                               placeholder="Por ejemplo: Novela histórica"
                               class="w-full rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300">
                        @error('nombre') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-5 border-t border-cielo-100">
                        <a href="{{ route('categorias.index') }}" class="text-sm text-cielo-700 hover:underline">Cancelar</a>
                        <button type="submit"
                                class="rounded-md bg-cielo-700 px-5 py-2 text-sm font-medium text-white hover:bg-cielo-800 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // La vista previa muestra el nombre mientras se escribe
    const inNombre = document.getElementById('nombre');
    const pvNombre = document.getElementById('pvNombre');
    const actualizar = () => { pvNombre.textContent = inNombre.value.trim() || 'Nombre de la categoría'; };
    inNombre.addEventListener('input', actualizar);
    actualizar();
</script>
@endpush