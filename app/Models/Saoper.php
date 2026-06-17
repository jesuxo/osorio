<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saoper extends Model
{
    use HasFactory;
    protected $table    = 'saoper';
    protected $fillable = [
        'codoper', 'descrip', 'comercial', 'activo', 'orden', 'repvta'
    ];


}
