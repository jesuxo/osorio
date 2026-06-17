
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
                    <td width="15%" height="36" align="center" class="tdline" > Cod </td>
                    <td width="75%" align="center" class="tdline">Cuenta </td>
                </tr>
                @php $n =0;@endphp
                @foreach($cuentas as $cuenta)
                @php $n++;@endphp
                <tr  @php if(($n%2)==0) echo 'bgcolor=#eee' @endphp>
                    <td width="" height="35px"align="left" valign="middle">
                        <a href="javascript:;" onclick="$('#form1').attr('action', '{{route('verbanco',['fkcuentacambiar' => $cuenta->id, 'iii' =>$iii])}}'); $('#form1').submit()" style="font-size:13px;">
                            {{$cuenta->numero}}
                        </a>
                    </td>
                    <td width="" align="left" valign="middle">
                        <a href="javascript:;" onclick="$('#form1').attr('action', '{{route('verbanco',['fkcuentacambiar' => $cuenta->id, 'iii' =>$iii])}}'); $('#form1').submit()" style="font-size:13px;">
                                {{$cuenta->descrip}}
                         </a>
                    </td>
                </tr>
                @endforeach
            </table>

        </div>
    </div>
</div>
