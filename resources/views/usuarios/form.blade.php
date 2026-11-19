@extends('layouts.app')

@php $editando = $usuario->exists; @endphp

@section('titulo', $editando ? 'Editar usuario' : 'Crear usuario')

@section('contenido')
    @php
        $campo = 'w-full rounded-md border border-cielo-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cielo-300';
        $etiqueta = 'block mb-1 text-sm font-medium text-cielo-900';
    @endphp

    <div class="mb-6">
        <h2 class="font-serif text-3xl font-semibold text-cielo-900">{{ $editando ? 'Editar usuario' : 'Crear usuario' }}</h2>
        <p class="text-sm text-slate-500 mt-1">
            {{ $editando ? 'Modifique los datos del carnet de este usuario.' : 'Complete los datos para emitir un nuevo carnet.' }}
        </p>
    </div>

    <form method="POST" action="{{ $editando ? route('usuarios.update', $usuario) : route('usuarios.store') }}"
          class="grid gap-10 md:grid-cols-5 items-start">
        @csrf
        @if($editando)
            @method('PUT')
        @endif

        {{-- Carnet (vista previa) --}}
        <div class="md:col-span-2 md:sticky md:top-24">
            <p class="text-sm font-medium text-cielo-900 mb-3 text-center">Así se verá el carnet</p>

            <article class="relative rounded-md border border-cielo-200 bg-white shadow-md overflow-hidden">
                <div class="h-1.5 bg-cielo-300"></div>
                <span class="absolute top-4 right-4 size-3 rounded-full bg-cielo-50 border border-cielo-200 shadow-inner" aria-hidden="true"></span>

                <div class="p-5">
                    <p class="font-serif text-sm text-slate-500">Mundo del Libro &middot; Carnet de biblioteca</p>

                    <div class="mt-4 flex items-center gap-3">
                        <span id="pvInicial" class="grid place-items-center size-14 shrink-0 rounded-full bg-cielo-100 text-cielo-800 font-serif text-2xl font-semibold" aria-hidden="true">?</span>
                        <div class="min-w-0">
                            <p id="pvNombre" class="font-serif text-xl font-semibold leading-snug text-cielo-900 truncate"></p>
                            <p id="pvCorreo" class="text-sm text-slate-500 truncate"></p>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-between py-2 border-t border-dashed border-cielo-200 text-sm">
                        <span class="text-slate-500">Rol</span>
                        <span id="pvRol" class="text-xs font-medium rounded-full px-2.5 py-1"></span>
                    </div>
                </div>
            </article>
        </div>

        {{-- Datos --}}
        <div class="md:col-span-3">
            <div class="rounded-md border border-cielo-200 bg-white shadow-md overflow-hidden">
                <div class="h-1.5 bg-cielo-300"></div>

                <div class="p-6 sm:p-8 space-y-5">

                    <div>
                        <label for="name" class="{{ $etiqueta }}">Nombre</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $usuario->name) }}" required class="{{ $campo }}">
                        @error('name') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="{{ $etiqueta }}">Correo</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" required class="{{ $campo }}">
                        @error('email') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="{{ $etiqueta }}">
                            Contraseña
                            @if($editando) <span class="font-normal text-slate-500">(déjala vacía para no cambiarla)</span> @endif
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" autocomplete="new-password"
                                   {{ $editando ? '' : 'required' }} class="{{ $campo }} pr-20">
                            <button type="button" id="verClave"
                                    class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-cielo-700 hover:text-cielo-900 focus:outline-none focus:ring-2 focus:ring-cielo-300 rounded-r-md"
                                    aria-controls="password" aria-pressed="false">
                                Mostrar
                            </button>
                        </div>

                        {{-- Fuerza de la contraseña --}}
                        <div id="fuerza" class="hidden mt-2">
                            <div class="flex gap-1">
                                <span class="barra h-1.5 flex-1 rounded-full bg-cielo-100"></span>
                                <span class="barra h-1.5 flex-1 rounded-full bg-cielo-100"></span>
                                <span class="barra h-1.5 flex-1 rounded-full bg-cielo-100"></span>
                                <span class="barra h-1.5 flex-1 rounded-full bg-cielo-100"></span>
                            </div>
                            <p id="fuerzaTexto" class="mt-1 text-xs text-slate-500"></p>
                        </div>
                        @error('password') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="role_id" class="{{ $etiqueta }}">Rol</label>
                        <select id="role_id" name="role_id" required class="{{ $campo }}">
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}" @selected(old('role_id', $usuario->role_id) == $r->id)>{{ $r->nombre }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit"
                                class="rounded-md bg-cielo-700 px-5 py-2 text-sm font-medium text-white hover:bg-cielo-800 focus:outline-none focus:ring-2 focus:ring-cielo-300">
                            Guardar
                        </button>
                        <a href="{{ route('usuarios.index') }}" class="text-sm text-cielo-700 hover:underline">Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const $ = (id) => document.getElementById(id);
    const inNombre = $('name'), inCorreo = $('email'), inRol = $('role_id'), inClave = $('password');

    const clasesRol = {
        administrador: 'bg-cielo-100 text-cielo-800',
        bibliotecario: 'bg-amber-100 text-amber-800',
        lector: 'bg-slate-100 text-slate-700',
    };

    // Carnet: inicial, nombre, correo y rol
    function actualizarCarnet() {
        const nombre = inNombre.value.trim();
        $('pvInicial').textContent = nombre ? nombre.charAt(0).toUpperCase() : '?';
        $('pvNombre').textContent = nombre || 'Nombre del usuario';
        $('pvCorreo').textContent = inCorreo.value.trim() || 'correo@ejemplo.com';

        const textoRol = inRol.options[inRol.selectedIndex] ? inRol.options[inRol.selectedIndex].text.trim() : '';
        const rol = $('pvRol');
        rol.textContent = textoRol || 'Sin rol';
        rol.className = 'text-xs font-medium rounded-full px-2.5 py-1 '
            + (clasesRol[textoRol.toLowerCase()] || 'bg-slate-100 text-slate-700');
    }

    // Mostrar u ocultar la contraseña
    $('verClave').addEventListener('click', () => {
        const oculta = inClave.type === 'password';
        inClave.type = oculta ? 'text' : 'password';
        $('verClave').textContent = oculta ? 'Ocultar' : 'Mostrar';
        $('verClave').setAttribute('aria-pressed', oculta);
    });

    // Fuerza de la contraseña (solo orientativa; la regla real la valida el servidor)
    const niveles = [
        { texto: 'Muy débil', color: 'bg-rose-400' },
        { texto: 'Débil', color: 'bg-amber-400' },
        { texto: 'Aceptable', color: 'bg-cielo-500' },
        { texto: 'Fuerte', color: 'bg-emerald-500' },
    ];

    function medirClave() {
        const v = inClave.value;
        $('fuerza').classList.toggle('hidden', v.length === 0);
        if (!v) return;

        let puntos = 0;
        if (v.length >= 8) puntos++;
        if (/[a-z]/.test(v) && /[A-Z]/.test(v)) puntos++;
        if (/\d/.test(v)) puntos++;
        if (/[^A-Za-z0-9]/.test(v)) puntos++;
        const nivel = Math.max(puntos, 1);

        document.querySelectorAll('#fuerza .barra').forEach((b, i) => {
            b.className = 'barra h-1.5 flex-1 rounded-full ' + (i < nivel ? niveles[nivel - 1].color : 'bg-cielo-100');
        });
        $('fuerzaTexto').textContent = niveles[nivel - 1].texto;
    }

    [inNombre, inCorreo, inRol].forEach((el) => el.addEventListener('input', actualizarCarnet));
    inClave.addEventListener('input', medirClave);

    actualizarCarnet();
</script>
@endpush