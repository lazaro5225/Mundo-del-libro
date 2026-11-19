<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iniciar sesión · Biblioteca</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Borde derecho simulando hojas apiladas (igual que en el panel) */
        .borde-hojas {
            background: repeating-linear-gradient(
                to right,
                #ffffff 0px,
                #ffffff 2px,
                #cbd5e1 2px,
                #cbd5e1 3px
            );
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-b from-sky-50 via-slate-50 to-slate-100 text-slate-800 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">

        {{-- LIBRO / TARJETA DE LOGIN --}}
        <div class="flex w-full max-w-md overflow-hidden rounded-l-md rounded-r-2xl bg-sky-50 shadow-xl ring-1 ring-sky-200/70">

            {{-- Lomo izquierdo con 3 líneas --}}
            <div class="flex w-7 shrink-0 flex-col justify-between border-r border-black/10 bg-sky-400 py-8">
                <span class="h-0.5 w-full bg-white/60 shadow-[0_1px_0_rgba(0,0,0,0.12)]"></span>
                <span class="h-0.5 w-full bg-white/60 shadow-[0_1px_0_rgba(0,0,0,0.12)]"></span>
                <span class="h-0.5 w-full bg-white/60 shadow-[0_1px_0_rgba(0,0,0,0.12)]"></span>
            </div>

            {{-- Portada con marco interior --}}
            <div class="flex flex-1 p-3">
                <div class="flex-1 rounded border border-sky-200 bg-white/70 px-6 py-8 sm:px-8">

                    {{-- Encabezado --}}
                    <div class="mb-7 text-center">
                        <div class="mx-auto mb-3 flex size-14 items-center justify-center rounded-full bg-sky-100 text-sky-800 ring-1 ring-sky-300/60">
                            <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.04A8.97 8.97 0 0 0 6 3.75c-1.05 0-2.06.18-3 .51v14.25A9 9 0 0 1 6 18c2.3 0 4.4.86 6 2.29m0-14.25a8.97 8.97 0 0 1 6-2.29c1.05 0 2.06.18 3 .51v14.25A9 9 0 0 0 18 18a8.97 8.97 0 0 0-6 2.29m0-14.25v14.25" />
                            </svg>
                        </div>

                        <h1 class="font-serif text-2xl font-bold tracking-wide text-sky-900">
                            Biblioteca
                        </h1>
                        <div class="mx-auto mt-2 flex items-center justify-center gap-2 text-sky-300">
                            <span class="h-px w-10 bg-current"></span>
                            <span class="text-xs">✦</span>
                            <span class="h-px w-10 bg-current"></span>
                        </div>
                        <p class="mt-2 text-sm text-slate-500">
                            Inicia sesión para continuar
                        </p>
                    </div>

                    {{-- Estado de sesión (por ejemplo, tras restablecer contraseña) --}}
                    @if (session('status'))
                        <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Errores --}}
                    @if ($errors->any())
                        <div class="mb-4 rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Correo --}}
                        <div>
                            <label for="email" class="mb-1 block text-xs font-bold tracking-wider text-sky-900 uppercase">
                                Correo electrónico
                            </label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="tucorreo@ejemplo.com"
                                class="block w-full rounded-md border border-sky-200 bg-white px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 shadow-sm transition focus:border-sky-500 focus:ring-2 focus:ring-sky-300 focus:outline-none"
                            >
                        </div>

                        {{-- Contraseña --}}
                        <div>
                            <label for="password" class="mb-1 block text-xs font-bold tracking-wider text-sky-900 uppercase">
                                Contraseña
                            </label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="block w-full rounded-md border border-sky-200 bg-white px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 shadow-sm transition focus:border-sky-500 focus:ring-2 focus:ring-sky-300 focus:outline-none"
                            >
                        </div>

                        {{-- Recordarme / olvidé contraseña --}}
                        <div class="flex items-center justify-between gap-3">
                            <label for="remember_me" class="inline-flex items-center gap-2">
                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    name="remember"
                                    class="size-4 rounded border-sky-300 text-sky-700 shadow-sm focus:ring-sky-400"
                                >
                                <span class="text-sm text-slate-600">Recordarme</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a
                                    href="{{ route('password.request') }}"
                                    class="rounded text-sm text-sky-700 underline-offset-2 hover:text-sky-900 hover:underline focus:ring-2 focus:ring-sky-300 focus:outline-none"
                                >
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        {{-- Botón --}}
                        <button
                            type="submit"
                            class="w-full rounded-md bg-sky-700 px-4 py-2.5 text-sm font-bold tracking-wider text-white uppercase shadow-md transition duration-300 hover:-translate-y-0.5 hover:bg-sky-800 hover:shadow-lg focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:outline-none"
                        >
                            Entrar
                        </button>
                    </form>
                </div>
            </div>

            {{-- Borde derecho de hojas --}}
            <div class="borde-hojas my-1.5 w-3 shrink-0 rounded-r shadow-[inset_2px_0_3px_rgba(0,0,0,0.08)]"></div>
        </div>

        {{-- Estante decorativo --}}
        <div class="mt-8 w-full max-w-md px-4" aria-hidden="true">
            <div class="flex items-end justify-center gap-1.5">
                <span class="h-12 w-4 rounded-t-sm bg-sky-300"></span>
                <span class="h-16 w-5 rounded-t-sm bg-cyan-300"></span>
                <span class="h-10 w-4 rounded-t-sm bg-rose-300"></span>
                <span class="h-14 w-6 rounded-t-sm bg-blue-300"></span>
                <span class="h-11 w-4 rounded-t-sm bg-emerald-300"></span>
                <span class="h-16 w-5 rounded-t-sm bg-sky-400"></span>
                <span class="h-12 w-4 rounded-t-sm bg-cyan-200"></span>
                <span class="h-14 w-5 rounded-t-sm bg-rose-200"></span>
            </div>
            <div class="h-3 rounded bg-gradient-to-b from-slate-200 to-slate-300 shadow-md"></div>
        </div>
    </div>
</body>
</html>