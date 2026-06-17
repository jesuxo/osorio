
    <div class="row">
        <div class="col-xl-12">
                <div class="card overflow-hidden">
                    <div class="accordion accordion-flush filter-accordion">
                        <div class="card-body border-bottom">
                            <div class="table-responsive table-card ">
                                <table width="100%" border="0" class="table table-borderless table-centered align-middle table-nowrap mb-0 ">
                                    <tr bgcolor="#fff">
                                        <td width="56%" height="30"align="left" class="tdlineff" >CLIENTE - {{$codclie}}</td>
                                        <td width="11%" align="center" class="tdlineff" > TIPO</td>
                                        <td width="11%" align="center" class="tdlineff" > FECHA</td>
                                        <td width="11%" align="center" class="tdlineff" > NUMERO</td>
                                        <td width="11%" align="center" class="tdlineff" > MONTO</td>
                                        <td width="11%" align="center" class="tdlineff" > ABONADO</td>
                                        <td width="11%" align="center" class="tdlineff" > SALDO</td>
                                    </tr>

                                    @php
                                        $nn     = 1;
                                        $tmonto = 0;
                                        $tabona = 0;
                                        $tsaldo = 0;



                                                  $sqlcostoinv = "
                                                       SELECT c.fechat,
                                                        'FACT' AS tipo,
                                                        c.numerod as numero,
                                                        a.descrip AS cliente,
                                                        date_format(c.fechat,'%d/%m/%Y') fecha,
                                                         (c.SaldoOrg / c.tasadolar) AS credito,
                                                         (c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                                        a.codclie,
                                                         (c.saldo / c.tasadolar) AS saldo
                                                    FROM
                                                        saclie AS a
                                                    JOIN
                                                        saacxcw AS c
                                                        ON c.codclie = a.codclie
                                                    WHERE
                                                        c.Saldo > 10
                                                        AND c.tipocxc IN (10)
                                                        AND c.codoper not in ('9999','32','33','8888')
                                                        AND c.tasadolar > 0
                                                        AND c.codclie = '$codclie'


                                                        UNION

                                                        SELECT c.fechat,
                                                        'N.DEB' AS tipo,
                                                        c.numerod as numero,
                                                        a.descrip AS cliente,
                                                        date_format(c.fechat,'%d/%m/%Y') fecha,
                                                         (c.SaldoOrg / c.tasadolar) AS credito,
                                                         (c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                                        a.codclie,
                                                         (c.saldo / c.tasadolar) AS saldo
                                                    FROM
                                                        saclie AS a
                                                    JOIN
                                                        saacxcw AS c
                                                        ON c.codclie = a.codclie
                                                    WHERE
                                                        c.Saldo > 10
                                                        AND c.codoper not in ('9999','32','33','8888')
                                                        AND c.tipocxc IN (20)
                                                        AND c.tasadolar > 0
                                                        AND c.codclie = '$codclie'

                                                    ORDER BY  1

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
                                                    <td align="right" class="tdline" > {{$cxc->tipo}}</td>
                                                    <td align="right" class="tdline" > {{$cxc->fecha}}</td>
                                                    <td align="right" class="tdline" > {{$cxc->numero}}</td>
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
                                        <td  align="center"></td>
                                        <td  align="center"></td>
                                        <td  align="center"></td>
                                    </tr>
                                    <tr >
                                        <td height="30"align="left" class="tdline " >TOTALES </td>
                                        <td align="right" class="tdline" >  </td>
                                        <td align="right" class="tdline" >  </td>
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
        </div>
    </div>


