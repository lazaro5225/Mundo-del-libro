<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        foreach (['administrador', 'bibliotecario', 'lector'] as $nombre) {
            Role::firstOrCreate(['nombre' => $nombre]);
        }
        $roles = Role::pluck('id', 'nombre');

        // Usuarios de prueba (la contraseña se encripta sola con el cast "hashed")
        $usuarios = [
            ['Administrador', 'admin@biblioteca.test', 'admin12345', 'administrador'],
            ['Bibliotecario Demo', 'bibliotecario@biblioteca.test', 'biblio12345', 'bibliotecario'],
            ['Lector Demo', 'lector@biblioteca.test', 'lector12345', 'lector'],
        ];
        foreach ($usuarios as [$nombre, $email, $clave, $rol]) {
            User::updateOrCreate(
                ['email' => $email],
                ['name' => $nombre, 'password' => $clave, 'role_id' => $roles[$rol], 'activo' => true]
            );
        }

        // Categorías
        foreach (['Novela', 'Romance', 'Terror', 'Ciencia ficción', 'Historia', 'Técnico', 'Infantil'] as $cat) {
            Category::firstOrCreate(['nombre' => $cat]);
        }
        $categorias = Category::pluck('id', 'nombre');

        // Libros de ejemplo: [titulo, autor, año, categoría, ejemplares]
        $libros = [
            ['Cien años de soledad', 'Gabriel García Márquez', 1967, 'Novela', 3],
            ['Drácula', 'Bram Stoker', 1897, 'Terror', 2],
            ['Orgullo y prejuicio', 'Jane Austen', 1813, 'Romance', 2],
            ['Fundación', 'Isaac Asimov', 1951, 'Ciencia ficción', 2],
            ['Clean Code', 'Robert C. Martin', 2008, 'Técnico', 1],
            ['El Principito', 'Antoine de Saint-Exupéry', 1943, 'Infantil', 3],
        ];
        foreach ($libros as [$titulo, $autor, $anio, $cat, $stock]) {
            Book::firstOrCreate(
                ['titulo' => $titulo, 'autor' => $autor],
                [
                    'anio' => $anio,
                    'category_id' => $categorias[$cat],
                    'stock_total' => $stock,
                    'stock_disponible' => $stock,
                    'activo' => true,
                ]
            );
        }
    }
}
