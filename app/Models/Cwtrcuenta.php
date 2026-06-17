<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Cwtrcuenta extends Model
{
    use HasFactory;
    use Hashidable;

    protected $table    = 'cwtrcuenta';
    protected $fillable = ['descrip', 'monto','signo','fk_cuenta','periodo','fk_transaccion'];

    public function transaccion(){
        return $this->belongsTo(Cwtransaccion::class, 'fk_transaccion', 'id');
    }

}
