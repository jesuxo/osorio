<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saexis extends Model
{
    use HasFactory;
    protected $table    = 'saexis';
    protected $fillable = ['codubic', 'codprod', 'existen', 'fk_sucursal'];
}
