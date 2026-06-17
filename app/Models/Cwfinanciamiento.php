<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Luecano\NumeroALetras\NumeroALetras;

class Cwfinanciamiento extends Model
{
    use HasFactory;
    protected $table    = 'cwfinanciamiento';
    protected $fillable = ['valorfinancia', 'costobien', 'inicialfinancia', 'porcinicial', 'saldofinancia',
        'costofinancia', 'porccostofi', 'cantcuotas', 'cuota', 'codclie', 'fecha', 'codclieavalista'];

    protected $appends = [ 'yearletra', 'dayletra', 'day', 'monpagar', 'fechaformat', 'status', 'valorfinancialetra', 'saldofinancialetra', 'inicialfinancialetra'];

    public function sucursal  (){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function cliente  (){
        return $this->belongsTo(Saclie::class, 'codclie', 'codclie');
    }

    public function avalista  (){
        return $this->belongsTo(Saclie::class, 'codclieavalista', 'codclie');
    }

    public function getStatusAttribute(){
        $id = $this->id;

        if(isset($id) and $id>0 ){
            $consulta = Saacxc::selectRaw("count(*) as tantos")
                ->whereRaw("
                     fkfinanciamiento = $id and saldo > 0
                ")->first();
            return $consulta->tantos;
        }else{
            return 0;
        }

    }

    public function getFechaformatAttribute(){
        $date = $this->Fecha;
        if(isset($date)){
            list($fecha,$hora) = explode(' ',$date);
            list($y,$m,$d) = explode('-',$fecha);
            return "$d/$m/$y";
        }
    }

    public function getDayAttribute(){
        $date = $this->Fecha;
        if(isset($date)){
            list($fecha,$hora) = explode(' ',$date);
            list($y,$m,$d) = explode('-',$fecha);
            return "$d";
        }
    }

    public function getDayletraAttribute(){
        $date = $this->Fecha;
        if(isset($date)){
            list($fecha,$hora) = explode(' ',$date);
            list($y,$m,$d) = explode('-',$fecha);


            $obj = new NumeroALetras();
            return $obj->toWords($d);
        }
    }

    public function getYearletraAttribute(){
        $date = $this->Fecha;
        if(isset($date)){
            list($fecha,$hora) = explode(' ',$date);
            list($y,$m,$d) = explode('-',$fecha);


            $obj = new NumeroALetras();
            return $obj->toWords($y);
        }
    }

    public function getValorfinancialetraAttribute(){
        $monto = $this->valorfinancia;
        $obj = new NumeroALetras();
        return $obj->toWords($monto).' DOLARES DE ESTADOS UNIDOS DE AMERICA';
    }

    public function getInicialfinancialetraAttribute(){
        $monto = $this->inicialfinancia;
        $obj = new NumeroALetras();
        return $obj->toWords($monto).' DOLARES DE ESTADOS UNIDOS DE AMERICA';
    }

    public function getSaldofinancialetraAttribute(){
        $monto = $this->saldofinancia;
        $obj = new NumeroALetras();
        return $obj->toWords($monto).' DOLARES DE ESTADOS UNIDOS DE AMERICA';
    }


    public function getMonpagarAttribute(){
        list($y,$i,$d) = explode('-',$this->Fecha);
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




}
