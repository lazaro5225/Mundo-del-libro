<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'titulo', 'autor', 'descripcion', 'imagen', 'anio', 'category_id',
        'stock_total', 'stock_disponible', 'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    // URL pública de la portada (o null si el libro no tiene imagen)
    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen ? asset('storage/portadas/' . $this->imagen) : null;
    }

    // Búsqueda por título, autor, año y categoría
    public function scopeBuscar($query, array $f)
    {
        return $query
            ->when(!empty($f['titulo']), fn ($q) => $q->where('titulo', 'like', '%' . $f['titulo'] . '%'))
            ->when(!empty($f['autor']), fn ($q) => $q->where('autor', 'like', '%' . $f['autor'] . '%'))
            ->when(!empty($f['anio']), fn ($q) => $q->where('anio', $f['anio']))
            ->when(!empty($f['category_id']), fn ($q) => $q->where('category_id', $f['category_id']));
    }
}