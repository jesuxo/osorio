@extends('layouts.master')
@section('title')
    Reporte CxC
@endsection
@section('css')
    <style>
        #clearall{
            text-decoration: none !important;
        }
    </style>
    <link rel="stylesheet" href="{{ URL::asset('build/libs/nouislider/nouislider.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/libs/gridjs/mermaid.min.css') }}">
@endsection
@section('content')
    <style>
        .tdline{
            border:1px solid #0072c5 !important;

        }
        .tdlineff{
            border-left:1px solid #fff !important;

            color: white !important;
            background-color: #0072c5 !important;
        }
    </style>
    <x-breadcrumb title="Reporte CxC" pagetitle="CxC" />

    <div class="row">
        <div class="col-xl-4 ">
            <div class="card overflow-hidden">
                <div class="accordion accordion-flush filter-accordion">
                    <div class="card-body border-bottom">
                        <div class="table-responsive table-card ">
                            <table width="100%" border="0"    class="table table-borderless table-centered align-middle table-nowrap mb-0 ">
                                <tr bgcolor="#fff">
                                    <td width="60%" height="30"align="left" class="tdline" >SUCURSAL</td>
                                    <td width="20%" align="center" class="tdlineff" > CANT</td>
                                    <td width="20%" align="center" class="tdlineff" > MONTO</td>
                                </tr>
                                @php
                                    $nn     = 0;
                                    $tcanti = 0;
                                    $tmonto = 0;
                                    $tabona = 0;
                                @endphp

                                @foreach($sucursales as $index => $sucu)

                                    @php

                                        $sql = "
                                                SELECT
                                                COUNT(*) AS cant,
                                                SUM(c.SaldoOrg / c.tasadolar) AS credito,
                                                SUM(c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                                SUM(c.saldo / c.tasadolar) AS saldo
                                            FROM
                                                saclie AS a
                                            JOIN
                                                saacxc AS c
                                                ON c.codclie = a.codclie
                                            WHERE
                                                c.Saldo > 10
                                                AND c.tipocxc IN (20, 10)
                                                AND c.tasadolar > 0
                                                AND c.fk_sucursal =  ".$sucu->id;

                                    $saldocxc = \Illuminate\Support\Facades\DB::select($sql);

                                    if($saldocxc[0]->saldo != 0){
                                         $nn++;
                                         $tcanti += $saldocxc[0]->cant;
                                         $tmonto += $saldocxc[0]->saldo;
                                    @endphp

                                        <tr @php if(($nn%2)==0){echo 'bgcolor="#eee"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                            <td  height="30"align="left" class="tdline" >
                                                <a href="/cxc/{{$sucu->id}}"  class="mb-0 listname" style=" font-size: 12px;  ">
                                                    {{$sucu->descrip}}
                                                </a>
                                            </td>
                                            <td align="center" class="tdline">  {{($saldocxc[0]->cant  != 0 )?  number_format( $saldocxc[0]->cant ,0,',','.').'  ' : ''}}</td>
                                            <td align="right"  class="tdline">  {{($saldocxc[0]->saldo != 0 )?  number_format($saldocxc[0]->saldo,2,',','.'):''}}</td>
                                        </tr>
                                    @php  } @endphp

                                @endforeach
                                <tr >
                                    <td height="30"align="left"></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                </tr>
                                <tr >
                                    <td height="30"align="left" class="tdline">TOTALES </td>
                                    <td align="center" class="tdline" >{{($tcanti != 0)? number_format( $tcanti ,0,',','.') : ''}} </td>
                                    <td align="center" class="tdline" >{{($tmonto != 0)? number_format($tmonto ,2,',','.'):''}} </td>
                                </tr>


                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            @if(isset($sucursalselected)    and isset($sucursalselected->descrip))

                <div class="card overflow-hidden">
                    <div class="accordion accordion-flush filter-accordion">
                        <div class="card-body border-bottom">
                            <div class="table-responsive table-card ">
                                <table width="100%" border="0" class="table table-borderless table-centered align-middle table-nowrap mb-0 ">
                                    <tr bgcolor="#fff">
                                        <td width="56%" height="30"align="left" class="tdlineff" >CLIENTES - {{$sucursalselected->descrip}}</td>
                                        <td width="11%" align="center" class="tdlineff" > CANT</td>
                                        <td width="11%" align="center" class="tdlineff" > MONTO</td>
                                        <td width="11%" align="center" class="tdlineff" > ABONADO</td>
                                        <td width="11%" align="center" class="tdlineff" > SALDO</td>
                                    </tr>

                                    @php
                                        $nn     = 1;
                                        $tmonto = 0;
                                        $tabona = 0;
                                        $tsaldo = 0;

                                            $fk_sucursal = $sucursalselected->id;

                                                  $sqlcostoinv = "
                                                       SELECT
                                                        COUNT(*) AS deudas,
                                                        a.descrip AS cliente,
                                                        SUM(c.SaldoOrg / c.tasadolar) AS credito,
                                                        SUM(c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                                        a.codclie,
                                                        SUM(c.saldo / c.tasadolar) AS saldo
                                                    FROM
                                                        saclie AS a
                                                    JOIN
                                                        saacxc AS c
                                                        ON c.codclie = a.codclie
                                                    WHERE
                                                        c.Saldo > 10
                                                        AND c.tipocxc IN (20, 10)
                                                        AND c.tasadolar > 0
                                                        AND c.fk_sucursal = ".$sucursalselected->id."
                                                    GROUP BY
                                                        a.descrip, a.codclie;

                                                          ";

                                        $saldocxc = \Illuminate\Support\Facades\DB::select($sqlcostoinv);

                                        @endphp
                                            @foreach($saldocxc as $index => $cxc)
                                                @php
                                                 $nn++;
                                                 $tmonto += $cxc->credito;
                                                 $tabona += $cxc->abonado;
                                                 $tsaldo += $cxc->saldo;
                                                @endphp
                                                <tr @php if(($nn%2)==0){echo 'bgcolor="#eee"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                                    <td  height="30"align="left" class="tdline" >  {{$cxc->cliente}}  </td>
                                                    <td align="right" class="tdline" >
                                                        <button type="button" class="btn btn-outline-primary cxcmodal" data-codclie="{{$cxc->codclie}}"
                                                                onclick="$('#titulolistado').html('FACTURAS A CREDITO DE {{$cxc->cliente}}')"
                                                                data-bs-toggle="modal" data-bs-target="#cxcmodal">
                                                            <i class="bx bx-menu"></i> <span class="badge bg-success ms-1">{{$cxc->deudas}}</span>
                                                        </button>

                                                    </td>
                                                    <td align="right" class="tdline" > {{($cxc->credito != 0 )? number_format( $cxc->credito ,2,',','.').'  ' : ''}}</td>
                                                    <td align="right" class="tdline" > {{($cxc->abonado != 0 )? number_format( $cxc->abonado ,2,',','.').'  ' : ''}}</td>
                                                    <td align="right" class="tdline" > {{($cxc->saldo   != 0 )? number_format($cxc->saldo,2,',','.'):''}}</td>
                                                </tr>

                                            @endforeach

                                    <tr >
                                        <td height="30"align="left"  > </td>
                                        <td  align="center"></td>
                                        <td  align="center"></td>
                                        <td  align="center"></td>
                                    </tr>
                                    <tr >
                                        <td height="30"align="left" class="tdline " >TOTALES </td>
                                        <td align="right" class="tdline" >  </td>
                                        <td align="right" class="tdline" >  {{($tmonto != 0)? number_format($tmonto ,2,',','.')  : ''}} </td>
                                        <td align="right" class="tdline" >  {{($tabona != 0)? number_format($tabona ,2,',','.')  : ''}} </td>
                                        <td align="right" class="tdline" >  {{($tsaldo != 0)? number_format($tsaldo ,2,',','.')  : ''}} </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="cxcmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="titulolistado"> LISTADO FACTURAS A CREDITO</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                </button>
                            </div>
                            <div class="modal-body" id="contentcxcreport">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
                            </div>
                        </div>
                    </div>
                </div>

            @endif
        </div>
    </div>
@endsection
@section('scripts')
    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        $('.cxcmodal').unbind('click').bind('click',function () {
            var codclie   = $(this).attr('data-codclie');
            $('#contentcxcreport').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');
            $.ajax({
                type:'post',
                data:{codclie: (codclie)? codclie : '' },
                url:'/cxclist',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(response) {
                    $('#contentcxcreport').html(response);
                }
            });
        });
    </script>
@endsection
