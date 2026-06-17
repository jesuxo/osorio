<div  style=" width:700px; max-height:600px; overflow:auto">

    <table width="100%" border="0"  >
        <tr>
            <td width="12%" height="29"  align="center" class="titulo tdline ">C&oacute;digo</td>
            <td width="59%" align="left" class="titulo tdline ">Tarjeta/Banco/Moneda
                <input type="hidden"  name="tab"    value="tab11">
            </td>
        </tr>
         @php

             $numerito=0;

             $data = " and bs=1";

             if($pesos){
                 $data = " and pesos=1";
             }
             if($dolares){
                 $data = " and dolares=1";
             }

             $sqltarj=" select codtarj, descrip
                        from satarj
                        where activo=1 and web = 1
                         $data
                        order by codtarj
                                                                                                              ";

           $lists = \Illuminate\Support\Facades\DB::select($sqltarj);
           foreach($lists as $list){

                @endphp
                    <tr @php if(($numerito%2) == 0){ @endphp bgcolor="#eee" @php } @endphp>
                        <td   align="center">{{$list->codtarj}}</td>
                        <td align="left">

                               @php if(!$dolares and !$pesos){
                                      echo $list->descrip;
                               @endphp
                                     <input type="hidden"  name="codtar[]"  id="codtar[]"  value="{{$list->codtarj}}">
                               @php }

                                    echo $list->descrip;

                              @endphp
                        </td>
                    </tr>

            @php $numerito++;
           }

           $asd = 0;
           if($asd == 1){
                $sqltarj="  select a.id as idtransf, a.numero, a.titular, a.monto, b.instpago
                                          from  cwtransferencia a,    cwbancos b
                                          where a.fk_banco = b.id
                                          $data
                                          and a.pend_usar = 1
                                          order by fecha";

           $lists = \Illuminate\Support\Facades\DB::select($sqltarj);

            foreach ($lists as $list){
                $idtransf = $list['idtransf'];

            @endphp
        <tr {{(($numerito%2) == 0)? 'bgcolor="#eee"' : ''  }} id="transfanticipo{{$list['idtransf']}}">
            <td height="32" align="center" class="titulo"> {{$list['instpago']}}  </td>
            <td align="left">
                    @php if(!$dolares and !$pesos){
                if(!$anticipos){
                        $url = session('urlsaoper')."&codtarget=".$list['instpago']."&desctarget=".$list['titular']."&transftarget=".$list['idtransf']."&montotarget=".$list['monto']."&tab=tab11&ultdol=".$ultdol;
                    @endphp
                        <a  href="javascript:;"  onClick="submit_pag('{{$url}}')">
                         {{$list['numero'].' '.$list['titular'].' '.number_format($list['monto'],2,',','.')}}
                        </a>

                <input type="hidden"  name="aidtransf{{$idtransf}}" id="aidtransf{{$idtransf}}"
                       value="{{$list['numero'].' '.$list['titular'].' '.number_format($list['monto'],2,',','.')}}">
                @php }else{
                         echo $list['numero'].' <br>'.$list['titular'];

                    }
                }

                if($dolares ){
                      if(!$anticipos){
                          $url = session('urlsaoper')."&codtardolget=".$list['instpago']."&transfdolget=".$list['idtransf']."&montotardolget=".$list['monto']."&desctardolget=".$list['titular']."&tab=tab11&ultdol=".$ultdol;
                         @endphp
                        <a href="javascript:;" onClick="submit_pag('{{$url}}')">
                                {{$list['numero'].' '.$list['titular']}}
                        </a>

                        <input type="hidden"  name="aidtransf{{$idtransf}}"
                               id="aidtransf{{$idtransf}}"
                               value="{{$list['numero'].' '.$list['titular'].' '.number_format($list['monto'],2,',','.')}}">
                        @php
                     }else{
                     echo $list['numero'].' <br>'.$list['titular'];
                    }
                }

                if($pesos){
                       if(!$anticipos){
                            $url = session('urlsaoper')."&codtarpesget=".$list['instpago']."&transfpesget=".$list['idtransf']."&montotarpesget=".$list['monto']."&desctarpesget=".$list['titular']."&tab=tab11&ultdol=".$ultdol;
                            @endphp
                            <a href="javascript:;"   onClick="submit_pag('{{$url}}')">
                                   {{$list['numero'].' '.$list['titular'].' '.number_format($list['monto'],2,',','.')}}
                            </a>

                             <input type="hidden"  name="aidtransf{{$idtransf}}" id="aidtransf{{$idtransf}}" value="{{$list['numero'].' '.$list['titular'].' '.number_format($list['monto'],2,',','.')}}">

                @php }else{
                           echo $list['numero'].' <br>'.$list['titular'];

                     }
                } @endphp
            </td>

        </tr>

        @php  }  } @endphp


    </table>

</div>
