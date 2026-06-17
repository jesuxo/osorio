<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cwebancos extends Model
{
    use HasFactory;
    protected $table    = 'cwebancos';
    protected $fillable = [ 'fk_banco', 'periodo', 'saldo_bs', 'saldo_dolares', 'saldo_euros', 'saldo_pesos'];



}
