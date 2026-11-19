
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Mundo del Libro')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,600;1,400&family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Newsreader', 'Georgia', 'serif'],
                        sans: ['Public Sans', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        cielo: {
                            50: '#f3f9fd',
                            100: '#e4f1f9',
                            200: '#cde6f3',
                            300: '#a9d3e8',
                            500: '#5b9fc4',
                            600: '#4388ad',
                            700: '#346e8f',
                            800: '#2c5a75',
                            900: '#1f3f52',
                        },
                        pergamino: {
                            50: '#fcfaf5',
                            100: '#f5efe2',
                            200: '#e8dec8',
                            300: '#d6c6a5',
                        },
                    },
                },
            },
        }
    </script>

    @stack('estilos')
</head>

<body class="min-h-screen flex flex-col font-sans text-slate-700 bg-gradient-to-b from-pergamino-50 via-cielo-50/40 to-white selection:bg-cielo-200 selection:text-cielo-900">

@php
    $enlace = fn (bool $activo) =>
        'inline-flex items-center gap-1.5 rounded-md px-3 py-2 text-sm font-medium transition-colors '
        . ($activo
            ? 'bg-cielo-100/90 text-cielo-900 ring-1 ring-inset ring-cielo-200'
            : 'text-slate-600 hover:bg-pergamino-100/80 hover:text-cielo-900');
@endphp

<div class="h-1 w-full bg-gradient-to-r from-cielo-800 via-cielo-600 to-cielo-800"></div>

<header class="bg-pergamino-50/90 backdrop-blur border-b border-cielo-200/90 sticky top-0 z-40 shadow-2xs">

    <div class="max-w-6xl mx-auto px-4">

        <div class="flex items-center justify-between h-16">

            <a href="{{ auth()->check() ? route('panel') : url('/') }}"
               class="group flex items-center gap-2.5">

                <span class="grid place-items-center size-9 rounded-md border border-cielo-200 bg-white text-cielo-700 shadow-2xs transition-colors group-hover:bg-cielo-100"
                      aria-hidden="true">

                    <svg viewBox="0 0 24 24"
                         class="size-5"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">

                        <path d="M4 5a2 2 0 0 1 2-2h12v16H6a2 2 0 0 0-2 2V5z"/>
                        <path d="M18 19v2H6"/>

                    </svg>
                </span>

                <div class="leading-none">
                    <span class="block font-serif text-2xl font-semibold tracking-tight text-cielo-900">
                        Mundo del Libro
                    </span>

                    <span class="hidden sm:block text-[10px] uppercase tracking-[0.18em] text-slate-400 mt-0.5">
                        Sala de lectura
                    </span>
                </div>

            </a>

            @auth
                <button id="btnMenu"
                        type="button"
                        aria-controls="menu"
                        aria-expanded="false"
                        class="lg:hidden rounded-md border border-cielo-200 bg-white p-2 text-cielo-700 hover:bg-cielo-50 focus:outline-none focus:ring-2 focus:ring-cielo-300">

                    <span class="sr-only">Abrir menú</span>

                    <svg viewBox="0 0 24 24"
                         class="size-6"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round">

                        <path d="M4 6h16M4 12h16M4 18h16"/>

                    </svg>

                </button>
            @endauth

        </div>

        @auth
            <nav id="menu"
                 class="hidden lg:flex lg:items-center lg:justify-between pb-3 lg:pb-0 lg:-mt-12 lg:h-16 lg:pl-64">

                <div class="flex flex-col lg:flex-row lg:items-center gap-1">

                    <a href="{{ route('panel') }}"
                       class="{{ $enlace(request()->routeIs('panel')) }}">
                        Inicio
                    </a>

                    <a href="{{ route('libros.index') }}"
                       class="{{ $enlace(request()->routeIs('libros.*')) }}">
                        Catálogo
                    </a>

                    @if(auth()->user()->hasRole('administrador', 'bibliotecario'))

                        <a href="{{ route('prestamos.index') }}"
                           class="{{ $enlace(request()->routeIs('prestamos.index')) }}">
                            Préstamos
                        </a>

                        <a href="{{ route('prestamos.create') }}"
                           class="{{ $enlace(request()->routeIs('prestamos.create')) }}">
                            Nuevo préstamo
                        </a>

                    @endif

                    @if(auth()->user()->hasRole('lector'))

                        <a href="{{ route('prestamos.mios') }}"
                           class="{{ $enlace(request()->routeIs('prestamos.mios')) }}">
                            Mi historial
                        </a>

                    @endif

                    @if(auth()->user()->hasRole('administrador'))

                        <a href="{{ route('usuarios.index') }}"
                           class="{{ $enlace(request()->routeIs('usuarios.*')) }}">
                            Usuarios
                        </a>

                        <a href="{{ route('categorias.index') }}"
                           class="{{ $enlace(request()->routeIs('categorias.*')) }}">
                            Categorías
                        </a>

                    @endif

                </div>

                <div class="flex items-center gap-3 mt-3 pt-3 border-t border-pergamino-200 lg:mt-0 lg:pt-0 lg:border-0">

                    <div class="text-sm leading-tight lg:text-right">

                        <p class="font-medium text-cielo-900">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-[11px] uppercase tracking-wider text-slate-400">
                            {{ auth()->user()->role?->nombre }}
                        </p>

                    </div>

                    <form method="POST" action="{{ route('logout') }}">

                        @csrf

                        <button type="submit"
                                class="rounded-md border border-cielo-200 bg-white px-3 py-1.5 text-sm text-cielo-800 hover:bg-cielo-50 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                            Cerrar sesión
                        </button>

                    </form>

                </div>

            </nav>
        @endauth

    </div>

</header>

<main class="flex-1 w-full max-w-6xl mx-auto px-4 py-8">

    {{-- Mensaje de éxito --}}
    @if(session('success'))

        <div data-alerta
             role="status"
             class="mb-6 flex items-start justify-between gap-4 rounded-md border border-emerald-200 border-l-4 border-l-emerald-600 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-900 shadow-2xs">

            <p class="font-medium">
                {{ session('success') }}
            </p>

            <button type="button"
                    data-cerrar-alerta
                    class="text-emerald-700 hover:text-emerald-950 px-1"
                    aria-label="Cerrar aviso">
                &times;
            </button>

        </div>

    @endif

    {{-- Mensajes de validación --}}
    @if($errors->any())

        <div data-alerta
             role="alert"
             class="mb-6 flex items-start justify-between gap-4 rounded-md border border-rose-200 border-l-4 border-l-rose-600 bg-rose-50/90 px-4 py-3 text-sm text-rose-900 shadow-2xs">

            <ul class="list-disc pl-5 space-y-1">

                @foreach($errors->all() as $error)

                    <li class="font-medium">
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

            <button type="button"
                    data-cerrar-alerta
                    class="text-rose-700 hover:text-rose-950 px-1"
                    aria-label="Cerrar aviso">
                &times;
            </button>

        </div>

    @endif

    {{-- Contenido de cada panel --}}
    @yield('contenido')

</main>

<footer class="border-t border-pergamino-200 bg-pergamino-50/80">

    <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-slate-500">

        <span class="font-serif italic text-sm text-cielo-900">
            Mundo del Libro
        </span>

        <span>
            Sistema de gestión de prestamos de libros
        </span>

    </div>

</footer>

<script>
    // Menú responsive
    const btnMenu = document.getElementById('btnMenu');
    const menu = document.getElementById('menu');

    if (btnMenu && menu) {

        btnMenu.addEventListener('click', () => {

            const abierto = menu.classList.toggle('hidden') === false;

            btnMenu.setAttribute('aria-expanded', abierto);

        });

    }

    // Ocultar alertas
    const ocultar = (el) => {

        if (!el) return;

        el.style.transition = 'opacity .3s';
        el.style.opacity = '0';

        setTimeout(() => el.remove(), 300);

    };

    document.querySelectorAll('[data-cerrar-alerta]').forEach((b) => {

        b.addEventListener('click', () => {
            ocultar(b.closest('[data-alerta]'));
        });

    });

    document.querySelectorAll('[data-alerta][role="status"]').forEach((el) => {

        setTimeout(() => {

            if (el.isConnected) {
                ocultar(el);
            }

        }, 5000);

    });
</script>

@stack('scripts')

</body>
</html>
