<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    protected $fillable = [
        'user_id', 'book_id', 'fecha_prestamo',
        'fecha_limite', 'fecha_devolucion', 'estado',
    ];

    protected $casts = [
        'fecha_prestamo' => 'date',
        'fecha_limite' => 'date',
        'fecha_devolucion' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    // Préstamos aún no devueltos
    public function scopeActivos($query)
    {
        return $query->whereNull('fecha_devolucion');
    }

    // No devueltos y con fecha límite ya pasada
    public function scopeVencidos($query)
    {
        return $query->whereNull('fecha_devolucion')
                     ->whereDate('fecha_limite', '<', today());
    }

    public function estadoTexto(): string
    {
        if ($this->fecha_devolucion) {
            return 'Devuelto';
        }

        return $this->fecha_limite->lt(today()) ? 'VENCIDO' : 'Prestado';
    }
}
