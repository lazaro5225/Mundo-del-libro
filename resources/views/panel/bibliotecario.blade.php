@extends('layouts.app')

@section('titulo', 'Panel del bibliotecario')

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

    /* Texto vertical en los lomos del estante en pantallas medianas y grandes */
    @media (min-width: 640px) {
        .texto-lomo {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
        }
    }
</style>

<div class="mx-auto max-w-5xl px-4 py-6 text-slate-800">
    <h2 class="mb-6 border-b-2 border-sky-200 pb-2 text-2xl font-bold text-sky-900">
        Panel del bibliotecario
    </h2>

    {{-- 1. RESUMEN EN FORMATO DE LIBROS --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        {{-- Préstamos activos (Verde suave) --}}
        <a
            href="{{ route('prestamos.index', ['estado' => 'activos']) }}"
            class="group flex h-40 overflow-hidden rounded-l-md rounded-r-xl bg-emerald-100 text-emerald-950 shadow-md transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl"
        >
            {{-- Lomo izquierdo con 3 líneas --}}
            <div class="flex w-6 shrink-0 flex-col justify-between border-r border-black/10 bg-emerald-400 py-4">
                <span class="h-0.5 w-full bg-white/60 shadow-[0_1px_0_rgba(0,0,0,0.12)]"></span>
                <span class="h-0.5 w-full bg-white/60 shadow-[0_1px_0_rgba(0,0,0,0.12)]"></span>
                <span class="h-0.5 w-full bg-white/60 shadow-[0_1px_0_rgba(0,0,0,0.12)]"></span>
            </div>

            {{-- Portada con marco interior --}}
            <div class="flex flex-1 p-2.5">
                <div class="flex flex-1 flex-col justify-between rounded border border-white/70 px-3.5 py-3">
                    <span class="text-xs font-bold tracking-wider text-emerald-900 uppercase">
                        Préstamos activos
                    </span>
                    <span
                        class="contador-libro self-end text-4xl leading-none font-extrabold text-emerald-950"
                        data-target="{{ $activos }}"
                    >
                        0
                    </span>
                </div>
            </div>

            {{-- Borde derecho de hojas --}}
            <div class="borde-hojas my-1 w-3 shrink-0 rounded-r shadow-[inset_2px_0_3px_rgba(0,0,0,0.08)]"></div>
        </a>

        {{-- Préstamos vencidos (Rosa apagado) --}}
        <a
            href="{{ route('prestamos.index', ['estado' => 'vencidos']) }}"
            class="group flex h-40 overflow-hidden rounded-l-md rounded-r-xl bg-rose-100 text-rose-950 shadow-md transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl"
        >
            {{-- Lomo izquierdo con 3 líneas --}}
            <div class="flex w-6 shrink-0 flex-col justify-between border-r border-black/10 bg-rose-400 py-4">
                <span class="h-0.5 w-full bg-white/60 shadow-[0_1px_0_rgba(0,0,0,0.12)]"></span>
                <span class="h-0.5 w-full bg-white/60 shadow-[0_1px_0_rgba(0,0,0,0.12)]"></span>
                <span class="h-0.5 w-full bg-white/60 shadow-[0_1px_0_rgba(0,0,0,0.12)]"></span>
            </div>

            {{-- Portada con marco interior --}}
            <div class="flex flex-1 p-2.5">
                <div class="flex flex-1 flex-col justify-between rounded border border-white/70 px-3.5 py-3">
                    <span class="text-xs font-bold tracking-wider text-rose-900 uppercase">
                        Préstamos vencidos
                    </span>
                    <span
                        class="contador-libro self-end text-4xl leading-none font-extrabold text-rose-950"
                        data-target="{{ $vencidos }}"
                    >
                        0
                    </span>
                </div>
            </div>

            {{-- Borde derecho de hojas --}}
            <div class="borde-hojas my-1 w-3 shrink-0 rounded-r shadow-[inset_2px_0_3px_rgba(0,0,0,0.08)]"></div>
        </a>
    </div>

    {{-- 2. ACCESOS RÁPIDOS EN ESTANTE --}}
    <h3 class="mt-9 mb-4 text-lg font-semibold text-sky-800">
        Accesos rápidos
    </h3>

    <div class="mt-3">
        <div
            id="estante-accesos"
            class="flex flex-col gap-2.5 sm:flex-row sm:items-end sm:gap-3 sm:overflow-x-auto sm:px-5 sm:pt-4"
        >
            <a
                href="{{ route('prestamos.create') }}"
                class="acceso-lomo flex h-14 w-full shrink-0 items-center justify-between rounded-md bg-sky-200 px-4 text-sky-950 shadow-sm transition-all duration-300 hover:translate-x-1.5 hover:bg-sky-300 hover:shadow-lg sm:h-52 sm:w-20 sm:flex-col sm:rounded-t-lg sm:rounded-b-sm sm:px-1.5 sm:py-3.5 sm:hover:-translate-y-3.5 sm:hover:translate-x-0 sm:hover:-rotate-1"
            >
                <span class="h-7 w-0.5 rounded-full bg-white/65 sm:h-0.5 sm:w-4/5"></span>
                <span class="texto-lomo max-h-36 overflow-hidden text-center text-sm leading-tight font-bold">
                    Registrar préstamo
                </span>
                <span class="h-7 w-0.5 rounded-full bg-white/65 sm:h-0.5 sm:w-4/5"></span>
            </a>

            <a
                href="{{ route('prestamos.index', ['estado' => 'activos']) }}"
                class="acceso-lomo flex h-14 w-full shrink-0 items-center justify-between rounded-md bg-sky-300 px-4 text-sky-950 shadow-sm transition-all duration-300 hover:translate-x-1.5 hover:bg-sky-400/80 hover:shadow-lg sm:h-48 sm:w-20 sm:flex-col sm:rounded-t-lg sm:rounded-b-sm sm:px-1.5 sm:py-3.5 sm:hover:-translate-y-3.5 sm:hover:translate-x-0 sm:hover:-rotate-1"
            >
                <span class="h-7 w-0.5 rounded-full bg-white/65 sm:h-0.5 sm:w-4/5"></span>
                <span class="texto-lomo max-h-36 overflow-hidden text-center text-sm leading-tight font-bold">
                    Registrar devolución
                </span>
                <span class="h-7 w-0.5 rounded-full bg-white/65 sm:h-0.5 sm:w-4/5"></span>
            </a>

            <a
                href="{{ route('prestamos.index', ['estado' => 'vencidos']) }}"
                class="acceso-lomo flex h-14 w-full shrink-0 items-center justify-between rounded-md bg-rose-200 px-4 text-rose-950 shadow-sm transition-all duration-300 hover:translate-x-1.5 hover:bg-rose-300 hover:shadow-lg sm:h-44 sm:w-20 sm:flex-col sm:rounded-t-lg sm:rounded-b-sm sm:px-1.5 sm:py-3.5 sm:hover:-translate-y-3.5 sm:hover:translate-x-0 sm:hover:-rotate-1"
            >
                <span class="h-7 w-0.5 rounded-full bg-white/65 sm:h-0.5 sm:w-4/5"></span>
                <span class="texto-lomo max-h-36 overflow-hidden text-center text-sm leading-tight font-bold">
                    Préstamos vencidos
                </span>
                <span class="h-7 w-0.5 rounded-full bg-white/65 sm:h-0.5 sm:w-4/5"></span>
            </a>

            <a
                href="{{ route('prestamos.index') }}"
                class="acceso-lomo flex h-14 w-full shrink-0 items-center justify-between rounded-md bg-cyan-200 px-4 text-cyan-950 shadow-sm ring-1 ring-cyan-300/60 transition-all duration-300 hover:translate-x-1.5 hover:bg-cyan-300 hover:shadow-lg sm:h-[208px] sm:w-24 sm:flex-col sm:rounded-t-lg sm:rounded-b-sm sm:px-1.5 sm:py-3.5 sm:hover:-translate-y-3.5 sm:hover:translate-x-0 sm:hover:-rotate-1"
            >
                <span class="h-7 w-0.5 rounded-full bg-white/65 sm:h-0.5 sm:w-4/5"></span>

                {{-- Icono de reloj con flecha (historial), solo en pantallas medianas y grandes --}}
                <svg class="hidden size-5 text-cyan-800 sm:block" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>

                <span class="texto-lomo text-center text-sm leading-snug font-bold tracking-wide">
                    Historial<br class="hidden sm:inline"> de préstamos
                </span>
                <span class="h-7 w-0.5 rounded-full bg-white/65 sm:h-0.5 sm:w-4/5"></span>
            </a>

            <a
                href="{{ route('libros.index') }}"
                class="acceso-lomo flex h-14 w-full shrink-0 items-center justify-between rounded-md bg-blue-200 px-4 text-blue-950 shadow-sm ring-1 ring-blue-300/60 transition-all duration-300 hover:translate-x-1.5 hover:bg-blue-300 hover:shadow-lg sm:h-[192px] sm:w-24 sm:flex-col sm:rounded-t-lg sm:rounded-b-sm sm:px-1.5 sm:py-3.5 sm:hover:-translate-y-3.5 sm:hover:translate-x-0 sm:hover:-rotate-1"
            >
                <span class="h-7 w-0.5 rounded-full bg-white/65 sm:h-0.5 sm:w-4/5"></span>

                {{-- Icono de libro abierto (solo en pantallas medianas y grandes) --}}
                <svg class="hidden size-5 text-blue-800 sm:block" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.04A8.97 8.97 0 0 0 6 3.75c-1.05 0-2.06.18-3 .51v14.25A9 9 0 0 1 6 18c2.3 0 4.4.86 6 2.29m0-14.25a8.97 8.97 0 0 1 6-2.29c1.05 0 2.06.18 3 .51v14.25A9 9 0 0 0 18 18a8.97 8.97 0 0 0-6 2.29m0-14.25v14.25" />
                </svg>

                <span class="texto-lomo text-center text-sm leading-snug font-bold tracking-wide">
                    Catálogo<br class="hidden sm:inline"> de libros
                </span>
                <span class="h-7 w-0.5 rounded-full bg-white/65 sm:h-0.5 sm:w-4/5"></span>
            </a>
        </div>

        {{-- Repisa del estante --}}
        <div class="hidden h-4 rounded bg-gradient-to-b from-slate-200 to-slate-300 shadow-md sm:block"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Conteo animado desde 0
    const contadores = document.querySelectorAll('.contador-libro');
    const duracion = 900;

    contadores.forEach(contador => {
        const objetivo = parseInt(contador.getAttribute('data-target'), 10) || 0;
        if (objetivo === 0) {
            contador.textContent = '0';
            return;
        }

        const inicio = performance.now();

        const animar = (ahora) => {
            const progreso = Math.min((ahora - inicio) / duracion, 1);
            const ease = 1 - Math.pow(1 - progreso, 4);
            contador.textContent = Math.floor(ease * objetivo);

            if (progreso < 1) {
                requestAnimationFrame(animar);
            } else {
                contador.textContent = objetivo;
            }
        };

        requestAnimationFrame(animar);
    });

    // 2. Entrada escalonada de los libros en el estante
    const librosEstante = document.querySelectorAll('.acceso-lomo');
    librosEstante.forEach((libro, i) => {
        libro.classList.add('opacity-0', 'translate-y-4');

        setTimeout(() => {
            libro.classList.remove('opacity-0', 'translate-y-4');
        }, 120 * i);
    });
});
</script>
@endsection