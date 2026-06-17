@extends('layouts.masterprint')
@section('title')
   Pagare - {{$pagare->cliente->descrip}}
@endsection
@section('css')

@endsection
@section('content')
    <table width="773" border="0" align="center" style="font-size:16px;">

        <tr>
            <td width="767" height="81" align="center" valign="top">    <img src="{{ URL::asset('build/images/logo-dark.png') }}"
                                                                             class="card-logo card-logo-dark"
                                                                             alt="logo dark" width="100px">
                <br>
                <h3>   PAGARE </h3>
            </td>
        </tr>

        <tr>
            <td height="278" valign="top" style="text-align:justify" >
                Yo: <strong>{{$pagare->cliente->descrip}}</strong> , venezolano(a), mayor de edad, soltero(a),
                titular de la c&eacute;dula de identidad Nº  <strong>{{$pagare->cliente->id3}}</strong>, domiciliado(a) en
                <strong>{{$pagare->cliente->direc1}}</strong>  y h&aacute;bil, por medio del presente documento. <strong>
                DECLARO: </strong> Que debo y pagar&eacute;, sin aviso y sin protesto, a la orden de:
                <strong> RUBEN ALEJANDRO OSORIO ESPINOSA</strong>, venezolano,
                mayor de edad, titular de la c&eacute;dula de identidad No.<strong> V-15.184.480</strong>,
                domiciliado(a) en <strong>la Fr&iacute;a, Municipio Garc&iacute;a de Hevia del Estado T&aacute;chira</strong> , y civilmente h&aacute;bil,
                la Cantidad de <strong> {{strtoupper($pagare->montoletra)}}</strong> DOLARES DE ESTADOS UNIDOS DE NORTEAMERICA
                <strong>(USD {{number_format($pagare->monto,2,',','.')}})</strong>, estableci&eacute;ndose de manera exclusiva y
                excluyente de cualquier otra moneda, en d&oacute;lares de los Estados Unidos de Norteam&eacute;rica, los cuales ser&aacute;n pagados
                para el d&iacute;a

                <strong>{{$pagare->diapagar}}</strong>
                de
                <strong>{{$pagare->monpagar}} </strong>
                del año {{$pagare->yearpagar}}</strong>, por valor recibido, en un &uacute;nico pago de:
                <strong>  {{strtoupper($pagare->montoletra)}}</strong> DOLARES DE ESTADOS UNIDOS DE NORTEAMERICA
                <strong>(USD {{number_format($pagare->monto,2,',','.')}})</strong>.
                As&iacute; mismo en caso de cobro judicial de este pagar&eacute;, el bien  embargado en caso de remate, se har&aacute;
                mediante la publicaci&oacute;n de un &uacute;nico cartel de remate y el Aval&uacute;o lo har&aacute; un solo Perito designado por
                el Tribunal de la causa.

                @if(isset($pagare->nombreavalista) and $pagare->nombreavalista != '' and strlen($pagare->nombreavalista)>6)

                Y yo, <strong>{{$pagare->nombreavalista}}</strong>, venezolano(a), mayor de edad, soltero(a),
                titular de la c&eacute;dula de identidad No. <strong>{{$pagare->cedulaavalista}} </strong>, domiciliado(a)

                    {{$pagare->domicilioavalista}}  , y civilmente h&aacute;bil, me constituyo en <strong>AVALISTA</strong>
                del principal pagador, de la Cantidad de
                <strong> {{ $pagare->montoletra}}</strong>
                DOLARES DE ESTADOS UNIDOS DE NORTEAMERICA <strong>(USD {{number_format($pagare->monto,2,',','.')}})
                </strong>.
                @endif

                Por &uacute;ltimo, para todos los efectos y sus derivados del presente contrato las partes eligen como domicilio
                especial y exclusivo <strong>la Fr&iacute;a, Municipio Garc&iacute;a de Hevia del Estado T&aacute;chira</strong>.
                As&iacute; lo decimos y firmamos por v&iacute;a privada en la ciudad de
                <strong>la Fr&iacute;a, Municipio Garc&iacute;a de Hevia del Estado T&aacute;chira</strong> a los
                <strong> {{ $pagare->diapagar}} </strong>  d&iacute;as del mes de
                <strong> {{ $pagare->monpagar}} </strong>   del
                <strong> {{ $pagare->yearpagar}}</strong>.<br>
                <br>
                <br>
                <div style="text-align:center; width:100%" >
                    <p><br>
                    <table width="100%" border="0">
                        <tr>
                            <td align="center">_______________________________<br>
                                <strong>LIBRADO</strong><br></td>
                            @if(isset($pagare->nombreavalista) and $pagare->nombreavalista != '' and strlen($pagare->nombreavalista)>6)
                            <td  align="center">_______________________________<br>
                                <strong>AVALISTA</strong><br></td>
                            @endif
                        </tr>
                    </table>
                    <p><br>
                        <br>
                        <strong> RUBEN ALEJANDRO OSORIO ESPINOSA</strong>
                        <br>
                        _______________________________<br>
                        <strong>BENEFICIARIO</strong><br>
                </div>

            </td>
        </tr>

    </table>
    <div class="hstack gap-2 justify-content-end d-print-none mt-5">
        <a href="javascript:window.print()" class="btn btn-success"><i
                class="ri-printer-line align-bottom me-1"></i> Print</a>
    </div>
@endsection
@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
