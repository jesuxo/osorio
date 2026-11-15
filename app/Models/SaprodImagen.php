<?php
// app/Models/SaprodImagen.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaprodImagen extends Model
{
    protected $table = 'saprod_imagenes';

    protected $fillable = [
        'fk_saprod', 'codprod',
        'imagen', 'imagen_thumb', 'imagen_original',
        'ancho', 'alto', 'orden', 'principal',
    ];

    protected $casts = [
        'principal' => 'boolean',
        'orden'     => 'integer',
    ];

    public function producto()
    {
        return $this->belongsTo(Saprod::class, 'fk_saprod');
    }

    public function getImagenUrlAttribute(): string
    {
        return $this->imagen && file_exists(public_path($this->imagen))
            ? asset($this->imagen) . '?v=' . ($this->updated_at?->timestamp ?? 0)
            : asset('build/images/noimagen.jpg');
    }

    public function getImagenThumbUrlAttribute(): string
    {
        if ($this->imagen_thumb) {
            return asset($this->imagen_thumb) . '?v=' . ($this->updated_at?->timestamp ?? 0);
        }
        if ($this->imagen) {
            return asset($this->imagen) . '?v=' . ($this->updated_at?->timestamp ?? 0);
        }
        return asset('build/images/noimagen.jpg');
    }
}
