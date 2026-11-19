<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BibliotecaController extends Controller
{
    private function autorizar(string ...$roles): void
    {
        $user = auth()->user();

        if (!$user->activo) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            throw new HttpResponseException(
                redirect()->route('login')->withErrors(['email' => 'Tu cuenta está desactivada.'])
            );
        }

        if ($roles && !$user->hasRole(...$roles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    private function autorizarCatalogo(): void
    {
        $this->autorizar(...config('biblioteca.roles_catalogo', ['administrador']));
    }

    public function loginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('panel');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credenciales + ['activo' => true], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('panel'));
        }

        return back()
            ->withErrors(['email' => 'Credenciales incorrectas o usuario inactivo.'])
            ->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function panel(): RedirectResponse
    {
        $this->autorizar();

        return match (auth()->user()->role?->nombre) {
            'administrador' => redirect()->route('panel.administrador'),
            'bibliotecario' => redirect()->route('panel.bibliotecario'),
            default => redirect()->route('panel.lector'),
        };
    }

    public function panelAdministrador(): View
    {
        $this->autorizar('administrador');

        $stats = [
            'libros' => Book::count(),
            'usuarios' => User::count(),
            'prestamos_total' => Loan::count(),
            'prestamos_activos' => Loan::activos()->count(),
            'prestamos_vencidos' => Loan::vencidos()->count(),
        ];

        $masPrestados = Book::withCount('loans')
            ->orderByDesc('loans_count')
            ->take(5)
            ->get();

        return view('panel.administrador', compact('stats', 'masPrestados'));
    }

    public function panelBibliotecario(): View
    {
        $this->autorizar('bibliotecario');

        $activos = Loan::activos()->count();
        $vencidos = Loan::vencidos()->count();

        return view('panel.bibliotecario', compact('activos', 'vencidos'));
    }

    public function panelLector(): View
    {
        $this->autorizar('lector');

        $prestamos = auth()->user()->loans()
            ->with('book')
            ->activos()
            ->orderBy('fecha_limite')
            ->get();

        return view('panel.lector', compact('prestamos'));
    }

    public function libros(Request $request): View
    {
        $this->autorizar();

        $query = Book::with('category');

        $user = auth()->user();

        $puedeGestionar = method_exists($user, 'puedeGestionarCatalogo')
            ? $user->puedeGestionarCatalogo()
            : $user->hasRole('administrador', 'bibliotecario');

        // Los lectores (y quien no gestiona el catálogo) solo ven libros activos
        if (!$puedeGestionar) {
            $query->where('activo', true);
        }

        // Buscador general: título, autor o nombre de categoría
        if ($request->filled('q')) {
            $q = addcslashes(trim($request->q), '%_\\');

            $query->where(function ($w) use ($q) {
                $w->where('titulo', 'like', "%{$q}%")
                  ->orWhere('autor', 'like', "%{$q}%")
                  ->orWhereHas('category', fn ($c) => $c->where('nombre', 'like', "%{$q}%"));
            });
        }

        // Filtros específicos
        if ($request->filled('titulo')) {
            $query->where('titulo', 'like', '%' . addcslashes(trim($request->titulo), '%_\\') . '%');
        }

        if ($request->filled('autor')) {
            $query->where('autor', 'like', '%' . addcslashes(trim($request->autor), '%_\\') . '%');
        }

        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Disponibilidad (solo administrador y bibliotecario): disponibles o agotados
        if ($user->hasRole('administrador', 'bibliotecario')) {
            if ($request->query('disponibilidad') === 'disponibles') {
                $query->where('stock_disponible', '>', 0);
            } elseif ($request->query('disponibilidad') === 'agotados') {
                $query->where('stock_disponible', 0);
            }
        }

        // Estado (solo quien gestiona el catálogo): activo o dado de baja
        if ($puedeGestionar && in_array($request->query('estado'), ['activo', 'baja'], true)) {
            $query->where('activo', $request->query('estado') === 'activo');
        }

        $libros = $query->orderBy('titulo')->paginate(10)->withQueryString();
        $categorias = Category::orderBy('nombre')->get();

        return view('libros.index', compact('libros', 'categorias'));
    }

    // Detalle de un libro: portada a la izquierda e información a la derecha
    public function libroVer(Book $book): View
    {
        $this->autorizar();

        $user = auth()->user();

        $puedeGestionar = method_exists($user, 'puedeGestionarCatalogo')
            ? $user->puedeGestionarCatalogo()
            : $user->hasRole('administrador', 'bibliotecario');

        // Un lector no puede ver libros dados de baja
        if (!$book->activo && !$puedeGestionar) {
            abort(404);
        }

        $book->load('category');
        $libro = $book;

        return view('libros.show', compact('libro'));
    }

    public function libroCrear(): View
    {
        $this->autorizarCatalogo();

        $categorias = Category::all();

        return view('libros.create', compact('categorias'));
    }

    public function libroGuardar(Request $request): RedirectResponse
    {
        $this->autorizarCatalogo();

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:2000',
            'anio' => 'nullable|integer|min:1000|max:' . date('Y'),
            'category_id' => 'required|exists:categories,id',
            'stock_total' => 'required|integer|min:1|max:1000',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Se quita el UploadedFile y se guarda solo el nombre del archivo
        unset($validated['imagen']);

        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');
            $nombre = Str::uuid() . '.' . $archivo->extension();

            $archivo->storeAs('portadas', $nombre, 'public');

            $validated['imagen'] = $nombre;
        }

        $validated['stock_disponible'] = $validated['stock_total'];
        $validated['activo'] = true;

        Book::create($validated);

        return redirect()->route('libros.index')->with('success', 'Libro registrado exitosamente.');
    }

    public function libroEditar(Book $book): View
    {
        $this->autorizarCatalogo();

        $libro = $book;
        $categorias = Category::all();

        return view('libros.edit', compact('libro', 'categorias'));
    }

    public function libroActualizar(Request $request, Book $book): RedirectResponse
    {
        $this->autorizarCatalogo();

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:2000',
            'anio' => 'nullable|integer|min:1000|max:' . date('Y'),
            'category_id' => 'required|exists:categories,id',
            'stock_total' => 'required|integer|min:1|max:1000',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'quitar_imagen' => 'nullable|boolean',
        ]);

        $prestados = $book->stock_total - $book->stock_disponible;

        if ($validated['stock_total'] < $prestados) {
            return back()->withInput()->withErrors([
                'stock_total' => "No puede ser menor a los ejemplares prestados actualmente ($prestados).",
            ]);
        }

        $validated['stock_disponible'] = $validated['stock_total'] - $prestados;

        unset($validated['imagen'], $validated['quitar_imagen']);

        if ($request->hasFile('imagen')) {
            // Se sube una nueva portada: se borra la anterior
            if ($book->imagen) {
                Storage::disk('public')->delete('portadas/' . $book->imagen);
            }

            $archivo = $request->file('imagen');
            $nombre = Str::uuid() . '.' . $archivo->extension();

            $archivo->storeAs('portadas', $nombre, 'public');

            $validated['imagen'] = $nombre;
        } elseif ($request->boolean('quitar_imagen') && $book->imagen) {
            // Solo se quiere quitar la portada actual
            Storage::disk('public')->delete('portadas/' . $book->imagen);
            $validated['imagen'] = null;
        }

        $book->update($validated);

        return redirect()->route('libros.index')->with('success', 'Libro actualizado exitosamente.');
    }

    public function libroEstado(Book $book): RedirectResponse
    {
        $this->autorizarCatalogo();

        $book->update(['activo' => !$book->activo]);

        return back()->with('success', $book->activo ? 'Libro reactivado.' : 'Libro dado de baja.');
    }

    public function libroEliminar(Book $book): RedirectResponse
    {
        $this->autorizarCatalogo();

        if ($book->loans()->exists()) {
            return back()->withErrors([
                'error' => 'No se puede eliminar el libro porque tiene registros de préstamos asociados. Se recomienda darlo de baja.'
            ]);
        }

        $imagen = $book->imagen;

        $book->delete();

        // Se borra el archivo de la carpeta después de eliminar el registro
        if ($imagen) {
            Storage::disk('public')->delete('portadas/' . $imagen);
        }

        return redirect()->route('libros.index')->with('success', 'Libro eliminado exitosamente.');
    }

    public function prestamos(Request $request): View
    {
        $this->autorizar('administrador', 'bibliotecario');

        $query = Loan::with(['user', 'book']);
        $estado = $request->query('estado', 'todos');

        if ($estado == 'activos') {
            $query->activos();
        } elseif ($estado == 'vencidos') {
            $query->vencidos();
        } elseif ($estado == 'devueltos') {
            $query->whereNotNull('fecha_devolucion');
        }

        $prestamos = $query->latest('id')->paginate(15)->withQueryString();

        return view('prestamos.index', compact('prestamos', 'estado'));
    }

    public function prestamosMios(): View
    {
        $this->autorizar();

        $prestamos = auth()->user()->loans()
            ->with('book')
            ->latest('id')
            ->paginate(15);

        return view('prestamos.mios', compact('prestamos'));
    }

    public function prestamoCrear(): View
    {
        $this->autorizar('administrador', 'bibliotecario');

        // Los lectores y libros se buscan por AJAX (buscarLectores / buscarLibros)
        return view('prestamos.create');
    }

    // Buscador de lectores (JSON) para el formulario de préstamo
    public function buscarLectores(Request $request): JsonResponse
    {
        $this->autorizar('administrador', 'bibliotecario');

        $q = addcslashes(trim((string) $request->query('q', '')), '%_\\');

        $lectores = User::whereHas('role', fn ($r) => $r->where('nombre', 'lector'))
            ->where('activo', true)
            ->withExists(['loans as tiene_vencidos' => fn ($l) => $l->vencidos()])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->limit(15)
            ->get(['id', 'name', 'email']);

        return response()->json($lectores->map(fn ($u) => [
            'id' => $u->id,
            'texto' => "{$u->name} ({$u->email})",
            'bloqueado' => (bool) $u->tiene_vencidos,
        ])->values());
    }

    // Buscador de libros disponibles (JSON) para el formulario de préstamo
    public function buscarLibros(Request $request): JsonResponse
    {
        $this->autorizar('administrador', 'bibliotecario');

        $q = addcslashes(trim((string) $request->query('q', '')), '%_\\');

        $libros = Book::where('activo', true)
            ->where('stock_disponible', '>', 0)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('titulo', 'like', "%{$q}%")
                      ->orWhere('autor', 'like', "%{$q}%");
                });
            })
            ->orderBy('titulo')
            ->limit(15)
            ->get(['id', 'titulo', 'autor', 'stock_disponible']);

        return response()->json($libros->map(fn ($b) => [
            'id' => $b->id,
            'texto' => "{$b->titulo} — {$b->autor} ({$b->stock_disponible} disp.)",
            'bloqueado' => false,
        ])->values());
    }

    // Registrar préstamo con fechas seleccionadas en el calendario
    public function prestamoGuardar(Request $request): RedirectResponse
    {
        $this->autorizar('administrador', 'bibliotecario');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',

            'fecha_prestamo' => 'required|date_format:Y-m-d',
            'fecha_limite' => 'required|date_format:Y-m-d|after:fecha_prestamo',
        ], [
            'fecha_prestamo.required' => 'Debes seleccionar la fecha del préstamo.',
            'fecha_prestamo.date_format' => 'La fecha del préstamo no es válida.',

            'fecha_limite.required' => 'Debes seleccionar la fecha de devolución.',
            'fecha_limite.date_format' => 'La fecha de devolución no es válida.',
            'fecha_limite.after' => 'La fecha de devolución debe ser posterior a la fecha del préstamo.',
        ]);

        DB::transaction(function () use ($validated) {
            $libro = Book::whereKey($validated['book_id'])
                ->lockForUpdate()
                ->first();

            $lector = User::with('role')->findOrFail($validated['user_id']);

            if (!$lector->activo || !$lector->hasRole('lector')) {
                throw ValidationException::withMessages([
                    'user_id' => 'El usuario debe ser un lector activo.',
                ]);
            }

            if (Loan::where('user_id', $lector->id)->vencidos()->exists()) {
                throw ValidationException::withMessages([
                    'user_id' => 'El lector tiene préstamos vencidos. Debe devolverlos primero.',
                ]);
            }

            if (!$libro->activo || $libro->stock_disponible < 1) {
                throw ValidationException::withMessages([
                    'book_id' => 'No hay ejemplares disponibles de este libro.',
                ]);
            }

            Loan::create([
                'user_id' => $lector->id,
                'book_id' => $libro->id,
                'fecha_prestamo' => $validated['fecha_prestamo'],
                'fecha_limite' => $validated['fecha_limite'],
                'estado' => 'prestado',
            ]);

            $libro->decrement('stock_disponible');
        });

        return redirect()->route('prestamos.index')->with('success', 'Préstamo registrado exitosamente.');
    }

    public function prestamoDevolver(Loan $loan): RedirectResponse
    {
        $this->autorizar('administrador', 'bibliotecario');

        DB::transaction(function () use ($loan) {
            $loan = Loan::whereKey($loan->id)->lockForUpdate()->first();

            if ($loan->fecha_devolucion) {
                return;
            }

            $loan->update([
                'fecha_devolucion' => today(),
                'estado' => 'devuelto',
            ]);

            $loan->book()->increment('stock_disponible');
        });

        return back()->with('success', 'Devolución registrada exitosamente.');
    }

    public function usuarios(): View
    {
        $this->autorizar('administrador');

        $usuarios = User::with('role')->orderBy('name')->paginate(15);

        return view('usuarios.index', compact('usuarios'));
    }

    public function usuarioCrear(): View
    {
        $this->autorizar('administrador');

        $usuario = new User();
        $roles = Role::all();

        return view('usuarios.form', compact('usuario', 'roles'));
    }

    public function usuarioGuardar(Request $request): RedirectResponse
    {
        $this->autorizar('administrador');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $validated['activo'] = true;

        User::create($validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado exitosamente.');
    }

    public function usuarioEditar(User $user): View
    {
        $this->autorizar('administrador');

        $usuario = $user;
        $roles = Role::all();

        return view('usuarios.form', compact('usuario', 'roles'));
    }

    public function usuarioActualizar(Request $request, User $user): RedirectResponse
    {
        $this->autorizar('administrador');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($user->id === auth()->id() && (int) $validated['role_id'] !== $user->role_id) {
            return back()->withInput()->withErrors([
                'role_id' => 'No puedes cambiar tu propio rol.',
            ]);
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function usuarioEstado(User $user): RedirectResponse
    {
        $this->autorizar('administrador');

        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'usuario' => 'No puedes desactivar tu propia cuenta.'
            ]);
        }

        $user->update(['activo' => !$user->activo]);

        return back()->with('success', $user->activo ? 'Usuario activado.' : 'Usuario desactivado.');
    }

    public function categorias(): View
    {
        $this->autorizar('administrador');

        $categorias = Category::withCount('books')->orderBy('nombre')->get();

        return view('categorias.index', compact('categorias'));
    }

    public function categoriaGuardar(Request $request): RedirectResponse
    {
        $this->autorizar('administrador');

        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categories,nombre',
        ]);

        Category::create($validated);

        return back()->with('success', 'Categoría registrada exitosamente.');
    }

    public function categoriaEditar(Category $category): View
    {
        $this->autorizar('administrador');

        return view('categorias.form', compact('category'));
    }

    public function categoriaActualizar(Request $request, Category $category): RedirectResponse
    {
        $this->autorizar('administrador');

        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categories,nombre,' . $category->id,
        ]);

        $category->update($validated);

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    public function categoriaEliminar(Category $category): RedirectResponse
    {
        $this->autorizar('administrador');

        if ($category->books()->exists()) {
            return back()->withErrors([
                'nombre' => 'No se puede eliminar: tiene libros asociados.'
            ]);
        }

        $category->delete();

        return back()->with('success', 'Categoría eliminada exitosamente.');
    }
}
