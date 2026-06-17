
@extends('layouts.masterprint')
@section('title')
   Financiamiento - {{$financiamiento->cliente->descrip}}
@endsection
@section('css')
<style>
    .tdline{
        border:1px solid #0072c5 !important;
        font-size: 12px;
    }
</style>
@endsection
@section('content')

                <table border="0" width="100%" >

                    <tr >
                        <td width="20%" >
                            <img src="{{ URL::asset('build/images/logo-dark.png') }}"  class="card-logo card-logo-dark" alt="logo dark" width="70px"></td>
                        <td width="50%" > CONTRATO DE COMPRAVENTA A CUOTAS CON RESERVA DE DOMINIO

                        </td>
                        <td width="30%" align="right">Fecha: <strong>{{$financiamiento->fechaformat}}</strong></td>
                    </tr>
                    <tr >
                        <td height="35"  colspan="3" class=" " align="justify">
                            Entre <b>RUBEN ALEJANDRO OSORIO ESPINOSA</b>, venezolano, mayor de edad, comerciante, titular de la Cédula de Identidad
                            N°.  <b>V-15.184.480</b>, domiciliado en La Fría, Municipio García de Hevia, del estado Táchira, civilmente hábil, quien para efectos de este
                            contrato de venta con reserva de
                            dominio, se denominará:  <b>EL VENDEDOR</b>, por una parte; y por la otra: <b>{{$financiamiento->cliente->descrip}}</b>

                            Venezolano(a), mayor de edad, comerciante, titular de la Cédula de Identidad Nro. <b>{{$financiamiento->cliente->id3}}</b>,
                            domiciliado(a) en <b>{{$financiamiento->cliente->direc1.' '.$financiamiento->cliente->direc2}}</b>, civilmente hábil,
                            quien para iguales efectos, se denominará:  <b>LA PARTE COMPRADORA</b>, se ha convenido en celebrar el presente contrato de venta con
                            reserva de dominio, que se regirá por las siguientes cláusulas:
                            <br><br>
                            <b>CLÁUSULA PRIMERA:</b> EL VENDEDOR en venta a cuotas con
                            reserva de dominio a LA PARTE COMPRADORA, un

                            @if(isset($financiamiento->marca) and $financiamiento->marca !='')
                             veh&iacute;culo
                            @endif

                            @if(isset($financiamiento->superficie) and $financiamiento->superficie !='')
                                terreno

                            @endif

                            de su propiedad, el cual presenta las siguientes características:

                            @if(isset($financiamiento->marca) and $financiamiento->marca !='')
                                    <br>    <br>
                                <b>PLACA: </b>{{$financiamiento->placa}} ;
                                <b>MARCA:</b> {{$financiamiento->marca}} ;
                                <b>MODELO:</b> {{$financiamiento->modelo}} ;
                                <b>SERIAL CARROCERIA: </b>{{$financiamiento->sc}} ;
                                <b>SERIAL MOTOR: </b>{{$financiamiento->sm}} ;
                                <b>USO: PARTICULAR;</b>
                                <b>SERVICIO: PRIVADO;</b>
                                <b>COLOR: </b>{{$financiamiento->color}} ;
                            @endif

                            @if(isset($financiamiento->superficie) and $financiamiento->superficie !='')
                                <br>    <br>
                                <b>SUPERFICIE: </b>{{$financiamiento->superficie}} ;
                                <b>DIRECCION:</b> {{$financiamiento->direccion}} ;
                                <b>CARACTERISTICAS:</b> {{$financiamiento->caracteristicas}} ;
                            @endif
                            <br>
                            <br>
                            Quedando dicho vehículo bajo la guarda y custodia de LA COMPRADORA, siendo la única responsable
                            de los daños personales y/o materiales que pudiere ocasionar.  <br><br><b>CLÁUSULA SEGUNDA</b>: <b>LA PARTE COMPRADORA</b>
                            declara que acepta la venta a cuotas con reserva de dominio que se le hace en los términos que se establecen
                            en el presente contrato y, en consecuencia, se obliga a: <b>1)</b> Pagar la totalidad del precio del bien adquirido mediante
                            el presente contrato en los términos establecidos más adelante; <b>2)</b> Contratar para el vehículo, una póliza de seguro
                            de casco, con cobertura amplia a satisfacción de  <b>EL VENDEDOR</b> durante el plazo total del financiamiento, cuyo
                            beneficiario será EL VENDEDOR, cuyo monto, no será inferior al monto del precio total del presente financiamiento;

                            En todo caso, una vez  <b>LA PARTE COMPRADORA</b>, contrate dicha póliza de seguro, deberá entregar una copia a
                            <b>EL VENDEDOR</b>. y en caso que  <b>LA PARTE COMPRADORA</b> no contrata a tiempo dicha póliza de seguros,
                            lo podrá hacer a su cargo y/o costo,  <b>EL VENDEDOR</b>.   <br><br>

                            <b> CLÁUSULA TERCERA </b>:  <b>EL VENDEDOR </b>
                            se reserva el dominio y propiedad sobre el bien dado en venta mediante el presente contrato, hasta que sea cancelada
                            la última cuota del financiamiento otorgado a  <b>LA PARTE COMPRADORA</b>, así como cualquier otro accesorio que se haya
                            causado en razón del mismo, como recargos o intereses moratorios.

                            <br><br>
                            <b>CLÁUSULA CUARTA</b>:  El precio de la presente
                            venta con reserva de dominio, es por la cantidad de:
                           <b> {{$financiamiento->valorfinancialetra}}
                            ({{number_format($financiamiento->valorfinancia,2,',','.')}} U.S.D). </b>
                             Que <b>LA PARTE COMPRADORA</b>, conviene y se obliga a pagar, de manera exclusiva y excluyente de cualquier otra moneda,
                            en DOLARES DE ESTADOS UNIDOS DE AMERICA; con estricta sujeción, al término, forma y condiciones que se detallan en el
                            presente documento y en tal sentido, estableciéndose como forma de pago lo siguiente:
                            UN PAGO INICIAL o inicial por la cantidad de:

                            <b>   {{$financiamiento->inicialfinancialetra}}
                            ({{number_format($financiamiento->inicialfinancia,2,',','.')}} U.S.D)</b>

                            Que serser&aacute; efectuado, para el d&iacute;a {{$financiamiento->fechaformat}};

                            Quedando un saldo deudor de:

                            <b>   {{$financiamiento->saldofinancialetra}}
                                ({{number_format($financiamiento->saldofinancia,2,',','.')}} U.S.D)</b>


                        <br><br>

                            Que serán pagadas semanalmente, los días sábados, comenzando con la primera cuota, el día:
                            {{$financiamiento->fechaformat}} y así sucesivamente, hasta finalizar con el pago de la última cuota;

                            <b>EL VENDEDOR</b>, por cada cuota pagada, así como, para la  inicial, entregará a
                            <b>LA PARTE COMPRADORA</b> un recibo o constancia de pago, siendo éste el único documento de
                            cancelación válido para demostrar dichos pagos y en el mismo orden de ideas,
                            los pagos serán efectuados única y exclusivamente en la dirección o sede de
                            <b>EL VENDEDOR</b>, señalada al final de este documento.

                            <br><br> <b>CLÁUSULA QUINTA</b>:   <b>LA PARTECOMPRADORA</b> declara que ha examinado el bien
                            @if(isset($financiamiento->marca) and $financiamiento->marca !='') (vehículo automotor), @endif
                            @if(isset($financiamiento->superficie) and $financiamiento->superficie !='') (terreno), @endif

                            que se le da en venta y que se describe en este documento y ha verificado que el mismo se encuentra

                            @if(isset($financiamiento->marca) and $financiamiento->marca !='')
                            nuevo, sin detalles y en total funcionamiento,
                            @endif

                            @if(isset($financiamiento->superficie) and $financiamiento->superficie !='')
                                en buenas condiciones
                            @endif

                                por lo que, declara que recibe

                            @if(isset($financiamiento->marca) and $financiamiento->marca !='')
                                el veh&iacute;culo
                            @endif

                            @if(isset($financiamiento->superficie) and $financiamiento->superficie !='')
                              el terreno
                            @endif

                           a su entera y cabal satisfacción.

                            <br><br> <b> CLÁUSULA SEXTA</b>: <b>LA PARTE COMPRADORA</b>, queda en posesión del

                            @if(isset($financiamiento->marca) and $financiamiento->marca !='')
                                el veh&iacute;culo    automotor
                            @endif

                            @if(isset($financiamiento->superficie) and $financiamiento->superficie !='')
                                el terreno
                            @endif



                            comprado, asumiendo los riesgos y/o daños personales y/o a terceros, que pudiera ocasionar

                            @if(isset($financiamiento->marca) and $financiamiento->marca !='')
                                con la conducción de dicho veh&iacute;culo,
                            @endif

                            @if(isset($financiamiento->superficie) and $financiamiento->superficie !='')
                                con el uso del terreno
                            @endif

                           del mismo modo,


                            @if(isset($financiamiento->marca) and $financiamiento->marca !='')
                                cualquier infracción a las leyes de tránsito terrestre, civil o penal, será
                            @endif

                            @if(isset($financiamiento->superficie) and $financiamiento->superficie !='')
                                cualquier infracción a las leyes urbanisticas,  civiles o penales, serán
                            @endif

                             de su responsabilidad y de igual manera, en caso de pérdida o deterioro por cualquier hecho o

                            circunstancia, deberá dar cumplimiento a lo acordado mediante el presente instrumento, así como,
                            cualquier impuesto, tasa o contribución presenta o a futuro, que recaiga o se aplique sobre
                            el


                            @if(isset($financiamiento->marca) and $financiamiento->marca !='')
                                veh&iacute;culo
                            @endif

                            @if(isset($financiamiento->superficie) and $financiamiento->superficie !='')
                               terreno
                            @endif

                            , será asumido por <b> LA PARTE COMPRADORA</b>.

                            <br><br>
                            <b>CLAUSULA SEPTIMA</b>: En caso de retardo en el pago de tres (03) cuotas consecutivaspor parte

                            de <b>LA PARTE COMPRADORA</b>, dar&aacute; derecho a <b>EL VENDEDOR</b> a considerar este contrato de plazo
                            vencido y por consiguiente a solicitar la entrega del
                            @if(isset($financiamiento->marca) and $financiamiento->marca !='')
                                veh&iacute;culoautomotor
                            @endif

                            @if(isset($financiamiento->superficie) and $financiamiento->superficie !='')
                                terreno
                            @endif
                            , con la correspondiente indemnización de daños y perjuicios a que hubiere lugar. En cuanto a los
                            intereses de mora, en la falta de pago al vencimiento de cada cuota, serán calculados a la tasa
                            que para la fecha haya fijado el Banco Central de Venezuela.

                            <br><br>
                            <b> CLAUSULA OCTAVA</b>: Para todos los efectos derivados y consecuencias del presente contrato,
                            las partes declaran someterse a la jurisdicción de los tribunales de Venezuela eligiendo la ciudad
                            de La fría, Municipio García de Hevia, del estado Táchira, como domicilio especial único y excluyente.

                            Lo no previsto en este contrato, será resuelto por las normas legales supletorias y pertinentes.

                            <br><br>
                            <b> CLÁUSULA NOVENA</b>: Todas las notificaciones entre las partes relativas al presente contrato,
                            se deberán realizar mediante correo electrónico con confirmación de entrega a las siguientes direcciones:
                            1. <b>EL VENDEDOR</b>, ubicado en la Avenida Aeropuerto, de La Fría, Municipio García de Hevia,
                            estado Táchira. Correo electrónico:  <b>rubenosorio23@hotmail.com</b>.

                            <b>LA PARTE COMPRADORA</b>, ubicado en la  {{$financiamiento->direc1.' '.$financiamiento->direc2}},
                             Correo electrónico:  {{$financiamiento->cliente->email}}.
                            <br><br>

                            Se hacen Dos (2)  Ejemplares de un mismo tenor y a idénticos efectos, en La Fría, a los

                            <b> {{$financiamiento->dayletra}} ({{$financiamiento->day}})</b> días del mes de
                            <b>  {{$financiamiento->monpagar}}</b>

                            del año <b> {{$financiamiento->yearletra}}.</b>

                            <br><br>
                            <br><br>
                        </td>
                    </tr>

                    <tr >
                        <td>  <b> EL VENDEDOR</b>: </td>
                        <td>  <b>RUBEN OSORIO</b> __________________________ </td>
                        <td> </td>
                    </tr>
                    <tr >
                        <td> <b>LA PARTE COMPRADORA </b></td>
                        <td> <b>{{$financiamiento->cliente->descrip}} </b>____________________</td>
                        <td> </td>
                    </tr>
                    <tr >
                        <td ></td>
                        <td>&nbsp;</td>
                        <td></td>
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
