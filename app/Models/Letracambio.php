<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Luecano\NumeroALetras\NumeroALetras;

class Letracambio extends Model
{
    use HasFactory;
    protected $table    = 'letracambio';
    protected $fillable = [
        'fecha', 'codclie', 'fechapagar', 'nombreavalista', 'cedulaavalista',
        'domicilioavalista', 'ciudadpago', 'fk_usuario', 'monto'
    ];

    protected $appends = ['fechapagarformat','fechaformat', 'montoletra', 'dialetrapagar', 'monpagar', 'yearpagar'];

    public function cliente  (){
        return $this->belongsTo(Saclie::class, 'codclie', 'codclie');
    }

    public function getFechaformatAttribute(){
        $date = $this->fecha;
        if(isset($date)){
            list($y,$m,$d) = explode('-',$date);
            return "$d/$m/$y";
        }
    }

    public function getFechapagarformatAttribute(){
        $date = $this->fechapagar;
        if(isset($date)){
            list($y,$m,$d) = explode('-',$date);
            return "$d/$m/$y";
        }
    }

    public function getMontoletraAttribute(){
        $monto = $this->monto;
        $obj = new NumeroALetras();
       return $obj->toWords($monto);
    }

    public function getMonpagarAttribute(){
        list($y,$i,$d) = explode('-',$this->fechapagar);
        if($i==1){
            return "ENERO";
        }
        if($i==2){
            return "FEBRERO";
        }
        if($i==3){
            return  "MARZO";
        }
        if($i==4){
            return  "ABRIL";
        }
        if($i==5){
            return  "MAYO";
        }
        if($i==6){
            return  "JUNIO";
        }
        if($i==7){
            return  "JULIO";
        }
        if($i==8){
            return  "AGOSTO";
        }
        if($i==9){
            return  "SEPTIEMBRE";
        }
        if($i==10){
            return  "OCTUBRE";
        }
        if($i==11){
            return  "NOVIEMBRE";
        }
        if($i==12){
            return  "DICIEMBRE";
        }
    }

    public function getDialetrapagarAttribute(){
        list($y,$i,$d) = explode('-',$this->fechapagar);
        $obj = new NumeroALetras();
        return $obj->toWords($d);
    }

    public function getYearpagarAttribute(){
        list($y,$i,$d) = explode('-',$this->fechapagar);
        return $y;
    }


}
