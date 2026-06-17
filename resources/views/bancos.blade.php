@extends('layouts.master')
@section('title')
   Clientes
@endsection
@section('css')
    <style>
        .btn-soft-light:hover, .codclieseleted{
            background-color: #e0f2ff !important;
        }
        .nav-pills .nav-link {
            background: #eee !important;
            border-bottom-right-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        .nav-pills .nav-link.active  {
            background: #0072c5 !important;
            border-bottom-right-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        .nav-pills{
            border-bottom: 1px solid #0072c5;
        }
        .tdline{
            border:1px solid #0072c5 !important;
            font-size: 12px;
        }
        .tdlineff{
            border-left:1px solid #fff !important;
            font-size: 12px;
            color: white !important;
            background-color: #0072c5 !important;
        }
        .cajapequenacolor {
            margin: 5px;
            padding: 1px;
            float: left;
            width: 150px;
            height: 120px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            color: #fff;
            overflow: hidden;
            background-color: #e0f2ff !important;
        }
        .titulocaja {
            border-radius: 5px;
            min-height: 50px;
            font-size: 18px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
@endsection
@section('content')

    <div class="row">
        <div class="col-xxl-3">
            @php
                $sbs       = 0;
                $seuros    = 0;
                $spesos    = 0;
                $sdolares  = 0;
                $Tsbs      = 0;
                $Tseuros   = 0;
                $Tspesos   = 0;
                $Tsdolares = 0;
            @endphp
            @foreach($bancos as $banco)
               @php

                $numpadre = $banco->numero;
                $sqlbank = "SELECT  sum(a.sbs) as sbs, sum(a.sdolares) as sdolares, sum(a.seuros) as seuros, sum(a.spesos) as spesos
												       FROM  cwbancos a,  cwcuentas b
													   WHERE a.activo = 1
													   and a.fk_cuenta = b.id
													   and b.numpadre = '$numpadre'
													   ";
                $banks = \Illuminate\Support\Facades\DB::select($sqlbank);

                $sbs = $seuros = $spesos = $sdolares = 0;

                foreach($banks as $listbank){
                    $sbs      += $listbank->sbs;
                    $seuros   += $listbank->seuros;
                    $spesos   += $listbank->spesos;
                    $sdolares += $listbank->sdolares;

                    $Tsbs      += $listbank->sbs;
                    $Tseuros   += $listbank->seuros;
                    $Tspesos   += $listbank->spesos;
                    $Tsdolares += $listbank->sdolares;
                }

                @endphp
            <div class="   btn btn-soft-light mb-2  card-animate  " align="center" style="width: 100% !important; background: #eee"  >
                <a href="/bancos/padrebancos/{{$numpadre}}" style="width:100%;   text-align:center;"  class="" >
                    <span style="font-size:18px;">{{ $banco->descrip}}</span>

                    <table border="0" width="100%">
                        <tr>
                            <td width="77%" align="left">Bs</td>
                            <td width="23%" align="right">{{number_format($sbs,2,',','.')}}</td>
                        </tr>
                        <tr>
                            <td align="left">Dolares</td>
                            <td align="right">{{number_format($sdolares,2,',','.')}}</td>
                        </tr>

                        <tr>
                            <td align="left">Pesos</td>
                            <td align="right">{{number_format($spesos,2,',','.')}}</td>
                        </tr>
                    </table>

                </a>
            </div>
            @endforeach

            <script>
                $('.Tsbs')     .html('{{number_format($Tsbs,2,',','.')}}');
                $('.Tsdolares').html('{{number_format($Tsdolares,2,',','.')}}');
                $('.Tseuros')  .html('{{number_format($Tseuros,2,',','.')}}');
                $('.Tspesos')  .html('{{number_format($Tspesos,2,',','.')}}')
            </script>
        </div>
        <div class="col-xxl-9">
            @if($padrebancos != '')
                <div class="titulocaja tdline">
                    @php

					  $sqlpadre = "SELECT  b.descrip
					             FROM   cwcuentas b
					             WHERE  b.numero = '$padrebancos'
													   ";
                    $titlebanks = \Illuminate\Support\Facades\DB::select($sqlpadre);

                    echo (isset($titlebanks[0]->descrip) and $titlebanks[0]->descrip != '')? $titlebanks[0]->descrip  : 'BANCO/CAJA'

                    @endphp
                </div>
                 @php
                     $sqlsubpadre = "SELECT a.id, a.descrip, a.fk_cuenta, a.sbs, a.sdolares, a.seuros, a.spesos, a.cierrecaja, a.abrev
                                     FROM cwbancos a,  cwcuentas b
                                     WHERE a.activo  = 1 and a.web = 1
                                     and b.numpadre  = '$padrebancos'
                                     and a.fk_cuenta = b.id
                                     order by a.descrip, b.id ";

                    $subbanks = \Illuminate\Support\Facades\DB::select($sqlsubpadre);
                @endphp

                @foreach($subbanks as $subbank)
                    <div class="iniciobox cajapequenacolor m-3 color22" align="center" >
                        <a href="{{route('verbanco',['cdcd'=>0, 'fkbanco'=>$subbank->id])}}"
                           style="width:100%;   text-align:center;"  class="" >
                            <span style="font-size:15px;">
                                    @php
                                        $bankdescrip = str_replace("BANCO ","",strtoupper($subbank->descrip));
                                        $bankdescrip = substr($bankdescrip,0,20);
                                        echo $bankdescrip;
                                    @endphp
                            </span>
                            <table border="0" width="100%">
                                <tr>
                                    <td width="77%" align="left">Bs</td>
                                    <td width="23%" align="right">{{number_format($subbank->sbs,2,',','.')}}</td>
                                </tr>
                                <tr>
                                    <td align="left">$</td>
                                    <td align="right">{{number_format($subbank->sdolares,2,',','.')}}</td>
                                </tr>

                                <tr>
                                    <td align="left">Cop</td>
                                    <td align="right">{{number_format($subbank->spesos,2,',','.')}}</td>
                                </tr>
                            </table>
                        </a>
                    </div>
                @endforeach

            @endif

        </div>
    </div>

@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
