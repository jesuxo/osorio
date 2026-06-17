<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Cwtransaccion extends Model
{
    use HasFactory;
    use Hashidable;

    protected $table    = 'cwtransaccion';
    protected $fillable = ['numero', 'fk_banco', 'monto', 'monto_bs',
                            'monto_dolar', 'monto_euro', 'monto_peso', 'saldoactual_dolar','fecha','cdcd',
                            'notas1', 'notas2', 'notas3', 'fk_transaccion','periodo','tipobene',
                            'descripbene', 'codbene', 'fbs', 'feuros','fpesos','descripcion',
                            'codoper', 'chequeado'
                           ];

    public function banco(){
        return $this->belongsTo(Cwbancos::class, 'fk_banco', 'id');
    }

}
