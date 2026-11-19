@extends('layouts.app')

@section('titulo', $libro->titulo . ' - Mundo del Libro')

@section('contenido')
    @php
        $portadas = ['#346e8f', '#4388ad', '#5a7a96', '#3f7d63', '#7c6a8f', '#a5795a'];
        $colorPortada = $portadas[($libro->category_id ?? 0) % count($portadas)];
        $agotado = $libro->stock_disponible < 1;
    @endphp

    <a href="{{ route('libros.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-cielo-700 hover:underline">
        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
        Volver al catálogo
    </a>

    <div class="mt-6 grid gap-10 md:grid-cols-5 items-start">

        <div class="md:col-span-2">
            <div class="flex justify-center px-6 pt-4">
                <div class="relative flex w-56 h-80 sm:w-64 sm:h-96 rounded-l-sm rounded-r-md shadow-xl">

                    <span class="w-4 shrink-0 rounded-l-sm" style="background: {{ $colorPortada }}">
                        <span class="block h-full w-full bg-gradient-to-r from-black/25 to-transparent"></span>
                    </span>

                    @if($libro->imagen_url)
                        <button type="button" id="abrirPortada" aria-label="Ampliar la portada de {{ $libro->titulo }}"
                                class="flex-1 min-w-0 cursor-zoom-in focus:outline-none focus:ring-2 focus:ring-cielo-300">
                            <img src="{{ $libro->imagen_url }}" alt="Portada de {{ $libro->titulo }}" class="w-full h-full object-cover">
                        </button>
                    @else
                        <div class="flex-1 min-w-0 flex items-center justify-center p-5 text-center text-white" style="background: {{ $colorPortada }}">
                            <div class="border border-white/40 w-full h-full flex items-center justify-center p-3">
                                <span class="font-serif text-2xl font-semibold leading-tight">{{ $libro->titulo }}</span>
                            </div>
                        </div>
                    @endif

                    <span class="w-2 rounded-r-md border-y border-r border-black/10"
                          style="background: repeating-linear-gradient(to bottom, #ffffff 0 2px, #e3e8ec 2px 3px)"></span>
                </div>
            </div>

            <div class="h-4 rounded-sm shadow-md" style="background: linear-gradient(to bottom, #d9c4a3, #b99a6b)"></div>
            <div class="h-2 bg-gradient-to-b from-black/10 to-transparent"></div>

            @if($libro->imagen_url)
                <p class="mt-2 text-center text-xs text-slate-500">Toca la portada para ampliarla</p>
            @endif
        </div>

        <div class="md:col-span-3">
            <article class="relative rounded-md border border-pergamino-300 bg-pergamino-50/70 shadow-md overflow-hidden">
                <div class="h-1.5 bg-cielo-700"></div>

                <div class="bg-white/90 px-6 py-2.5 border-b border-dashed border-pergamino-300 flex items-center justify-between text-[11px] uppercase tracking-[0.16em] text-slate-400">
                    <span>Ficha catalográfica</span>
                    <span>Ref. #{{ $libro->id }}</span>
                </div>

                <div class="p-6 sm:p-8 pb-14 bg-white/75">
                    <h2 class="font-serif text-3xl font-semibold leading-tight text-cielo-900">{{ $libro->titulo }}</h2>
                    <p class="mt-1 font-serif italic text-lg text-slate-600">{{ $libro->autor }}</p>

                    @if(!$libro->activo)
                        <p class="mt-4 rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-medium text-rose-800">
                            Este libro está dado de baja.
                        </p>
                    @endif

                    <dl class="mt-6 text-sm">
                        <div class="flex justify-between gap-4 py-2.5 border-b border-dashed border-pergamino-300">
                            <dt class="text-slate-500">Autor</dt>
                            <dd class="font-medium text-cielo-900 text-right">{{ $libro->autor }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-2.5 border-b border-dashed border-pergamino-300">
                            <dt class="text-slate-500">Categoría</dt>
                            <dd class="font-medium text-cielo-900 text-right">{{ $libro->category->nombre ?? 'Sin categoría' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-2.5 border-b border-dashed border-pergamino-300">
                            <dt class="text-slate-500">Año de publicación</dt>
                            <dd class="font-medium text-cielo-900 text-right">{{ $libro->anio ?? 'N/D' }}</dd>
                        </div>
                        <div class="flex justify-between items-center gap-4 py-2.5 border-b border-dashed border-pergamino-300">
                            <dt class="text-slate-500">Disponibilidad</dt>
                            <dd class="text-right">
                                @if($agotado)
                                    <span class="text-xs font-medium rounded-full bg-rose-100 text-rose-800 px-2.5 py-1">
                                        Agotado · 0 de {{ $libro->stock_total }} ejemplares
                                    </span>
                                @else
                                    <span class="text-xs font-medium rounded-full bg-emerald-100 text-emerald-800 px-2.5 py-1">
                                        {{ $libro->stock_disponible }} de {{ $libro->stock_total }} ejemplares disponibles
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    <h3 class="font-serif text-xl font-semibold text-cielo-900 mt-8 mb-2">Descripción</h3>
                    @if($libro->descripcion)
                        <div id="descripcion" class="relative max-h-40 overflow-hidden transition-[max-height] duration-300">
                            <p class="text-sm leading-relaxed whitespace-pre-line text-slate-700">{{ $libro->descripcion }}</p>
                            <div id="velo" class="hidden absolute inset-x-0 bottom-0 h-12 bg-gradient-to-t from-white to-transparent"></div>
                        </div>
                        <button type="button" id="verMas" class="hidden mt-2 text-sm font-medium text-cielo-700 hover:underline focus:outline-none focus:ring-2 focus:ring-cielo-300 rounded">
                            Leer más
                        </button>
                    @else
                        <p class="text-sm italic text-slate-500">Este libro aún no tiene descripción.</p>
                    @endif
                </div>

                <span class="absolute bottom-4 left-1/2 -translate-x-1/2 size-4 rounded-full bg-pergamino-100 border border-pergamino-300 shadow-inner" aria-hidden="true"></span>
            </article>
        </div>
    </div>

    @if($libro->imagen_url)
        <div id="visor" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-cielo-900/75 backdrop-blur-2xs" aria-hidden="true">
            <div class="relative max-h-full" role="dialog" aria-modal="true" aria-label="Portada de {{ $libro->titulo }}">
                <img src="{{ $libro->imagen_url }}" alt="Portada de {{ $libro->titulo }}"
                     class="max-h-[85vh] max-w-full rounded-md shadow-2xl object-contain">
                <button type="button" id="cerrarVisor"
                        class="absolute -top-3 -right-3 grid place-items-center size-9 rounded-full bg-white text-cielo-800 shadow-md hover:bg-cielo-50 focus:outline-none focus:ring-2 focus:ring-cielo-300"
                        aria-label="Cerrar">&times;</button>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    const desc = document.getElementById('descripcion');
    const btnMas = document.getElementById('verMas');
    const velo = document.getElementById('velo');
    if (desc && btnMas) {
        if (desc.scrollHeight > desc.clientHeight + 4) {
            btnMas.classList.remove('hidden');
            velo.classList.remove('hidden');
        }
        btnMas.addEventListener('click', () => {
            const abierta = desc.dataset.abierta === '1';
            desc.style.maxHeight = abierta ? '' : desc.scrollHeight + 'px';
            desc.dataset.abierta = abierta ? '0' : '1';
            velo.classList.toggle('hidden', !abierta);
            btnMas.textContent = abierta ? 'Leer más' : 'Leer menos';
        });
    }

    const visor = document.getElementById('visor');
    const abrirPortada = document.getElementById('abrirPortada');
    if (visor && abrirPortada) {
        const cerrar = () => { visor.classList.add('hidden'); visor.classList.remove('flex'); abrirPortada.focus(); };
        abrirPortada.addEventListener('click', () => {
            visor.classList.remove('hidden');
            visor.classList.add('flex');
            document.getElementById('cerrarVisor').focus();
        });
        document.getElementById('cerrarVisor').addEventListener('click', cerrar);
        visor.addEventListener('click', (e) => { if (e.target === visor) cerrar(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !visor.classList.contains('hidden')) cerrar(); });
    }
</script>
@endpush