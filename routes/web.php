<?php

use App\Http\Controllers\BibliotecaController;
use Illuminate\Support\Facades\Route;

// Todas las rutas apuntan a BibliotecaController.
// El control de roles (administrador / bibliotecario / lector) se hace dentro del controlador.

// Invitados
Route::middleware('guest')->group(function () {
    Route::get('/login', [BibliotecaController::class, 'loginForm'])->name('login');
    Route::post('/login', [BibliotecaController::class, 'login'])->name('login.store');
});

// Usuarios autenticados
Route::middleware('auth')->group(function () {
    // Redirigir la raíz a /panel
    Route::get('/', fn () => redirect()->route('panel'));
    Route::post('/logout', [BibliotecaController::class, 'logout'])->name('logout');

    // Panel para cada tipo de usuario
    Route::get('/panel', [BibliotecaController::class, 'panel'])->name('panel');
    Route::get('/panel/administrador', [BibliotecaController::class, 'panelAdministrador'])->name('panel.administrador');
    Route::get('/panel/bibliotecario', [BibliotecaController::class, 'panelBibliotecario'])->name('panel.bibliotecario');
    Route::get('/panel/lector', [BibliotecaController::class, 'panelLector'])->name('panel.lector');

    // Libros (/libros/crear debe ir ANTES de /libros/{book})
    Route::get('/libros', [BibliotecaController::class, 'libros'])->name('libros.index');
    Route::get('/libros/crear', [BibliotecaController::class, 'libroCrear'])->name('libros.create');
    Route::get('/libros/{book}', [BibliotecaController::class, 'libroVer'])->name('libros.show');
    Route::post('/libros', [BibliotecaController::class, 'libroGuardar'])->name('libros.store');
    Route::get('/libros/{book}/editar', [BibliotecaController::class, 'libroEditar'])->name('libros.edit');
    Route::put('/libros/{book}', [BibliotecaController::class, 'libroActualizar'])->name('libros.update');
    Route::patch('/libros/{book}/estado', [BibliotecaController::class, 'libroEstado'])->name('libros.estado');
    Route::delete('/libros/{book}', [BibliotecaController::class, 'libroEliminar'])->name('libros.destroy');

    // Préstamos y devoluciones
    Route::get('/mis-prestamos', [BibliotecaController::class, 'prestamosMios'])->name('prestamos.mios');
    Route::get('/prestamos', [BibliotecaController::class, 'prestamos'])->name('prestamos.index');
    Route::get('/prestamos/crear', [BibliotecaController::class, 'prestamoCrear'])->name('prestamos.create');
    Route::get('/prestamos/buscar/lectores', [BibliotecaController::class, 'buscarLectores'])->name('prestamos.buscar.lectores');
    Route::get('/prestamos/buscar/libros', [BibliotecaController::class, 'buscarLibros'])->name('prestamos.buscar.libros');
    Route::post('/prestamos', [BibliotecaController::class, 'prestamoGuardar'])->name('prestamos.store');
    Route::post('/prestamos/{loan}/devolver', [BibliotecaController::class, 'prestamoDevolver'])->name('prestamos.devolver');

    // Usuarios y roles
    Route::get('/usuarios', [BibliotecaController::class, 'usuarios'])->name('usuarios.index');
    Route::get('/usuarios/crear', [BibliotecaController::class, 'usuarioCrear'])->name('usuarios.create');
    Route::post('/usuarios', [BibliotecaController::class, 'usuarioGuardar'])->name('usuarios.store');
    Route::get('/usuarios/{user}/editar', [BibliotecaController::class, 'usuarioEditar'])->name('usuarios.edit');
    Route::put('/usuarios/{user}', [BibliotecaController::class, 'usuarioActualizar'])->name('usuarios.update');
    Route::patch('/usuarios/{user}/estado', [BibliotecaController::class, 'usuarioEstado'])->name('usuarios.estado');

    // Categorías
    Route::get('/categorias', [BibliotecaController::class, 'categorias'])->name('categorias.index');
    Route::post('/categorias', [BibliotecaController::class, 'categoriaGuardar'])->name('categorias.store');
    Route::get('/categorias/{category}/editar', [BibliotecaController::class, 'categoriaEditar'])->name('categorias.edit');
    Route::put('/categorias/{category}', [BibliotecaController::class, 'categoriaActualizar'])->name('categorias.update');
    Route::delete('/categorias/{category}', [BibliotecaController::class, 'categoriaEliminar'])->name('categorias.destroy');
});