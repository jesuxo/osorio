<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saprov extends Model
{
    use HasFactory;
    protected $table    = 'saprov';
    protected $fillable = ['codprov', 'direc1', 'direc2', 'id3', 'descrip', 'tipoprv', 'represent',
        'clase', 'telef', 'movil', 'email',   'TipoID3', 'activo'];

}
