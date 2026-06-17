
<style>
    .card-header {
        border-bottom: 1px solid #132659;
    }
</style>
<div class="row">
    <div class="col-lg-12">

        <div class="card-body mt-2">

            <table width="100%" border="0" cellpadding="0" cellspacing="0"   >
                <tr >
                    <td width="13%" height="36" align="center" class="tdline" > Cod </td>
                    <td width="64%" align="center" class="tdline">Nombre Cliente </td>
                    <td width="14%" align="center" class="tdline"> RIF/Ced</td>
                </tr>
                @php $n =0;@endphp
                @foreach($clientes as $cliente)
                @php $n++;@endphp
                <tr  @php if(($n%2)==0) echo 'bgcolor=#eee' @endphp>
                    <td width="" height="35px"align="left" valign="middle">
                        <a href="{{route('verbanco',['fkbanco'=> $fkbanco, 'cdcd' =>$cdcd, 'cambiarclie' => $cliente->codclie])}}" style="font-size:13px;">
                            {{$cliente->codclie}}
                        </a>
                    </td>
                    <td width="" align="left" valign="middle">
                            <a href="{{route('verbanco',['fkbanco'=> $fkbanco, 'cdcd' =>$cdcd, 'cambiarclie' => $cliente->codclie])}}" style="font-size:13px;">
                                {{$cliente->descrip}}
                            </a>
                    </td>

                    <td width="" align="center" valign="middle">
                        <a href="{{route('verbanco',['fkbanco'=> $fkbanco, 'cdcd' =>$cdcd, 'cambiarclie' => $cliente->codclie])}}" style="font-size:13px;">
                            {{$cliente->id3}}
                        </a>
                    </td>

                </tr>
                @endforeach
            </table>

        </div>
    </div>
</div>
