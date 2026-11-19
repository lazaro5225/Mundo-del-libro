@extends('layouts.app')

@section('titulo', 'Panel del lector')

@section('contenido')
<style>
    /* Borde derecho simulando hojas apiladas */
    .borde-hojas {
        background: repeating-linear-gradient(
            to right,
            #ffffff 0px,
            #ffffff 2px,
            #cbd5e1 2px,
            #cbd5e1 3px
        );
    }

    /* Fondo decorativo sutil de libros/páginas sin cargar imágenes pesadas si no las subes */
    .fondo-biblioteca-patron {
        background-image:
            radial-gradient(circle at 20% 20%, rgba(14, 165, 233, 0.08) 0%, transparent 45%),
            radial-gradient(circle at 80% 75%, rgba(56, 189, 248, 0.10) 0%, transparent 50%),
            repeating-linear-gradient(
                -18deg,
                rgba(14, 116, 144, 0.03) 0px,
                rgba(14, 116, 144, 0.03) 2px,
                transparent 2px,
                transparent 18px
            );
    }

    /* Ocultar barra de scroll nativa en el carrusel */
    .sin-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .sin-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<div class="relative mx-auto max-w-5xl overflow-hidden rounded-2xl border border-sky-100 px-4 py-6 text-slate-800 shadow-sm">
    {{-- Capa de imagen de fondo de biblioteca (cambia la ruta a tu imagen local, ej. asset('images/libros-bg.jpg')) --}}
    <div
        id="fondo-libros-bg"
        class="fondo-biblioteca-patron pointer-events-none absolute inset-0 scale-105 bg-cover bg-center opacity-90 transition-transform duration-300 ease-out"
        style="background-image: url('{{ asset('images/libros-fondo.jpg') }}');"
    ></div>

    {{-- Velo suave para que el texto y los libros resalten con claridad --}}
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/85 via-sky-50/80 to-white/88 backdrop-blur-[1px]"></div>

    {{-- Contenido principal sobre el fondo --}}
    <div class="relative z-10">
        {{-- Encabezado --}}
        <div class="mb-8 flex flex-col justify-between gap-2 border-b-2 border-sky-200 pb-3 sm:flex-row sm:items-end">
            <div>
                <p class="text-xs font-semibold tracking-wider text-sky-600 uppercase">Panel del lector</p>
                <h2 class="text-2xl font-bold text-sky-950">
                    Bienvenido, {{ auth()->user()->name }}
                </h2>
            </div>

            @if(!$prestamos->isEmpty())
                <span class="inline-flex items-center self-start rounded-full bg-sky-100/90 px-3 py-1 text-xs font-bold text-sky-800 shadow-xs sm:self-auto">
                    {{ $prestamos->count() }} {{ $prestamos->count() === 1 ? 'libro en tu poder' : 'libros en tu poder' }}
                </span>
            @endif
        </div>

        {{-- 1. CARRUSEL DE PRÉSTAMOS ACTIVOS --}}
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-sky-800">
                Mis préstamos activos
            </h3>

            @if($prestamos->count() > 1)
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        id="btn-prev"
                        aria-label="Anterior"
                        class="flex h-9 w-9 items-center justify-center rounded-full border border-sky-200 bg-white/90 text-sky-800 shadow-sm transition hover:bg-sky-100 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        &#8592;
                    </button>
                    <button
                        type="button"
                        id="btn-next"
                        aria-label="Siguiente"
                        class="flex h-9 w-9 items-center justify-center rounded-full border border-sky-200 bg-white/90 text-sky-800 shadow-sm transition hover:bg-sky-100 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        &#8594;
                    </button>
                </div>
            @endif
        </div>

        @if($prestamos->isEmpty())
            <div class="rounded-xl border-2 border-dashed border-sky-200 bg-white/80 p-8 text-center">
                <p class="text-base font-medium text-sky-900">No tienes préstamos activos en este momento.</p>
                <p class="mt-1 text-sm text-slate-500">Explora el catálogo para solicitar tu próxima lectura.</p>
                <a
                    href="{{ route('libros.index') }}"
                    class="mt-4 inline-block rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                >
                    Ir al catálogo
                </a>
            </div>
        @else
            <div class="relative">
                <div
                    id="carrusel-libros"
                    class="sin-scrollbar flex snap-x snap-mandatory gap-6 overflow-x-auto scroll-smooth py-3 pr-4"
                >
                    @foreach($prestamos as $p)
                        @php
                            $vencido = $p->fecha_limite->isPast();
                            $portadaLibro = !empty($p->book->imagen) ? asset('storage/' . $p->book->imagen) : null;
                        @endphp

                        {{-- Tarjeta Libro --}}
                        <article
                            class="libro-item group relative flex h-64 w-64 shrink-0 snap-start overflow-hidden rounded-l-md rounded-r-xl shadow-md transition-all duration-300 hover:-translate-y-2 hover:shadow-xl {{ $vencido ? 'bg-rose-100 text-rose-950' : 'bg-sky-100 text-sky-950' }}"
                        >
                            {{-- Lomo izquierdo con 3 líneas --}}
                            <div class="relative z-10 flex w-6 shrink-0 flex-col justify-between border-r border-black/10 py-5 {{ $vencido ? 'bg-rose-400' : 'bg-sky-400' }}">
                                <span class="h-0.5 w-full bg-white/65 shadow-[0_1px_0_rgba(0,0,0,0.12)]"></span>
                                <span class="h-0.5 w-full bg-white/65 shadow-[0_1px_0_rgba(0,0,0,0.12)]"></span>
                                <span class="h-0.5 w-full bg-white/65 shadow-[0_1px_0_rgba(0,0,0,0.12)]"></span>
                            </div>

                            {{-- Portada con imagen de fondo sutil y marco interior --}}
                            <div class="relative flex flex-1 p-2.5">
                                @if($portadaLibro)
                                    <div
                                        class="pointer-events-none absolute inset-0 bg-cover bg-center opacity-15 transition-opacity duration-300 group-hover:opacity-25"
                                        style="background-image: url('{{ $portadaLibro }}');"
                                    ></div>
                                @endif

                                <div class="relative z-10 flex flex-1 flex-col justify-between rounded border border-white/80 bg-white/30 p-3 backdrop-blur-[0.5px]">
                                    <div>
                                        <span class="inline-block rounded-full px-2.5 py-0.5 text-[11px] font-bold tracking-wide uppercase {{ $vencido ? 'bg-rose-200 text-rose-900' : 'bg-emerald-200 text-emerald-900' }}">
                                            {{ $p->estadoTexto() }}
                                        </span>

                                        <h4 class="mt-2.5 line-clamp-3 text-base leading-snug font-extrabold" title="{{ $p->book->titulo }}">
                                            {{ $p->book->titulo }}
                                        </h4>
                                    </div>

                                    <div class="space-y-1 border-t border-black/10 pt-2 text-xs">
                                        <div class="flex justify-between">
                                            <span class="opacity-75">Prestado:</span>
                                            <span class="font-semibold">{{ $p->fecha_prestamo->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="opacity-75">Límite:</span>
                                            <span class="font-bold {{ $vencido ? 'text-rose-800 underline' : '' }}">
                                                {{ $p->fecha_limite->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Borde derecho de hojas --}}
                            <div class="borde-hojas relative z-10 my-1.5 w-3 shrink-0 rounded-r shadow-[inset_2px_0_3px_rgba(0,0,0,0.08)]"></div>
                        </article>
                    @endforeach
                </div>

                {{-- Repisa inferior bajo el carrusel --}}
                <div class="h-3.5 rounded bg-gradient-to-b from-slate-200 to-slate-300 shadow-md"></div>

                @if($prestamos->count() > 1)
                    <div id="carrusel-dots" class="mt-4 flex justify-center gap-2"></div>
                @endif
            </div>
        @endif

        {{-- 2. ACCESOS RÁPIDOS --}}
        <h3 class="mt-10 mb-4 text-lg font-semibold text-sky-800">
            Accesos rápidos
        </h3>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <a
                href="{{ route('libros.index') }}"
                class="group flex h-24 overflow-hidden rounded-l-md rounded-r-xl bg-sky-200/95 text-sky-950 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:bg-sky-300 hover:shadow-md"
            >
                <div class="flex w-5 shrink-0 flex-col justify-between border-r border-black/10 bg-sky-500 py-3">
                    <span class="h-0.5 w-full bg-white/60"></span>
                    <span class="h-0.5 w-full bg-white/60"></span>
                    <span class="h-0.5 w-full bg-white/60"></span>
                </div>
                <div class="flex flex-1 items-center justify-between px-4">
                    <div>
                        <span class="block text-base font-bold">Consultar el catálogo</span>
                        <span class="text-xs text-sky-900/80">Explora los libros disponibles</span>
                    </div>
                    <span class="text-lg font-bold transition-transform group-hover:translate-x-1">&#8594;</span>
                </div>
                <div class="borde-hojas my-1 w-2.5 shrink-0 rounded-r"></div>
            </a>

            <a
                href="{{ route('prestamos.mios') }}"
                class="group flex h-24 overflow-hidden rounded-l-md rounded-r-xl bg-cyan-100/95 text-cyan-950 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:bg-cyan-200 hover:shadow-md"
            >
                <div class="flex w-5 shrink-0 flex-col justify-between border-r border-black/10 bg-cyan-500 py-3">
                    <span class="h-0.5 w-full bg-white/60"></span>
                    <span class="h-0.5 w-full bg-white/60"></span>
                    <span class="h-0.5 w-full bg-white/60"></span>
                </div>
                <div class="flex flex-1 items-center justify-between px-4">
                    <div>
                        <span class="block text-base font-bold">Ver mi historial completo</span>
                        <span class="text-xs text-cyan-900/80">Consulta tus préstamos anteriores</span>
                    </div>
                    <span class="text-lg font-bold transition-transform group-hover:translate-x-1">&#8594;</span>
                </div>
                <div class="borde-hojas my-1 w-2.5 shrink-0 rounded-r"></div>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Movimiento sutil (paralaje) del fondo de libros al mover el cursor
    const panel = document.querySelector('.relative.mx-auto.max-w-5xl');
    const fondoBg = document.getElementById('fondo-libros-bg');

    if (panel && fondoBg && window.matchMedia('(pointer: fine)').matches) {
        panel.addEventListener('mousemove', (e) => {
            const rect = panel.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width - 0.5) * -14;
            const y = ((e.clientY - rect.top) / rect.height - 0.5) * -14;
            fondoBg.style.transform = `translate3d(${x}px, ${y}px, 0) scale(1.06)`;
        });

        panel.addEventListener('mouseleave', () => {
            fondoBg.style.transform = 'translate3d(0px, 0px, 0) scale(1.05)';
        });
    }

    // 2. Lógica del carrusel de libros
    const carrusel = document.getElementById('carrusel-libros');
    if (!carrusel) return;

    const items = carrusel.querySelectorAll('.libro-item');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const dotsContainer = document.getElementById('carrusel-dots');

    items.forEach((libro, i) => {
        libro.classList.add('opacity-0', 'translate-y-3');
        setTimeout(() => {
            libro.classList.remove('opacity-0', 'translate-y-3');
        }, 100 * i);
    });

    if (items.length <= 1) return;

    const paso = 280;

    items.forEach((_, idx) => {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.ariaLabel = `Ir al libro ${idx + 1}`;
        dot.className = 'h-2.5 w-2.5 rounded-full bg-sky-200 transition-all duration-200';
        dot.addEventListener('click', () => {
            carrusel.scrollTo({ left: idx * paso, behavior: 'smooth' });
        });
        dotsContainer?.appendChild(dot);
    });

    const actualizarControles = () => {
        const maxScroll = carrusel.scrollWidth - carrusel.clientWidth;
        if (btnPrev) btnPrev.disabled = carrusel.scrollLeft <= 5;
        if (btnNext) btnNext.disabled = carrusel.scrollLeft >= maxScroll - 5;

        if (dotsContainer) {
            const indiceActivo = Math.round(carrusel.scrollLeft / paso);
            Array.from(dotsContainer.children).forEach((dot, idx) => {
                if (idx === indiceActivo) {
                    dot.classList.remove('bg-sky-200', 'w-2.5');
                    dot.classList.add('bg-sky-600', 'w-6');
                } else {
                    dot.classList.remove('bg-sky-600', 'w-6');
                    dot.classList.add('bg-sky-200', 'w-2.5');
                }
            });
        }
    };

    btnPrev?.addEventListener('click', () => {
        carrusel.scrollBy({ left: -paso, behavior: 'smooth' });
    });

    btnNext?.addEventListener('click', () => {
        carrusel.scrollBy({ left: paso, behavior: 'smooth' });
    });

    carrusel.addEventListener('scroll', actualizarControles, { passive: true });
    window.addEventListener('resize', actualizarControles);
    actualizarControles();
});
</script>
@endsection