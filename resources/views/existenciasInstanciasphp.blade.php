


            @if(isset($instanciaselected)    and isset($instanciaselected->descrip))
                <div class="card overflow-hidden">
                    <div class="accordion accordion-flush filter-accordion">
                        <div class="card-body border-bottom">
                            <div>

                                <ul class="list-unstyled mb-0 filter-list">
                                    <li  style="color: black; font-weight: bold" class="mb-2">
                                        <div class="d-flex  align-items-center">
                                            <div class="flex-grow-1" style="width: 73px">
                                                <div class="mb-0 listname" >{{$instanciaselected->descrip}}</div>
                                            </div>
                                            <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                                <div class="mb-0 listname" style="width: 239px; text-align: center;  ">  UNDS</div>
                                            </div>
                                            <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                                <div class="mb-0 listname" style="width: 121px; text-align: right; "> COSTO</div>
                                            </div>
                                        </div>
                                    </li>
                                    @php
                                        $nn        = 1;
                                        $tunidades = 0;
                                        $tcostopro = 0;
                                        $inspadre  = 0;
                                    @endphp

                                    @foreach($sucursales as $index => $sucursal)

                                        @php
                                            $fk_sucursal = $sucursal->id;
                                            $codalte     = $instanciaselected->codalte;
                                            $len         = strlen($codalte);
                                            $sqlcostoinv = "
                                                        SELECT c.fk_sucursal,  LEFT(b.codalte, $len) as prodalte ,
                                                        SUM(c.Existen) AS existen,
                                                        SUM(a.preciod * c.Existen) AS preciod
                                                    FROM
                                                        saprod a
                                                        INNER JOIN sainsta b ON
                                                            a.CodInst = b.CodInst AND
                                                            b.tipoins = 0 AND
                                                            b.comercial = $comercial AND
                                                            LEFT(b.codalte, $len) = '$codalte'
                                                        INNER JOIN newsaexis c ON
                                                            a.codprod = c.codprod AND
                                                            c.fk_sucursal = $fk_sucursal
                                                    WHERE
                                                        a.comercial = $comercial
                                                    group by c.fk_sucursal, LEFT(b.codalte, $len)

                                                               ";

                                        $costoinven = \Illuminate\Support\Facades\DB::select($sqlcostoinv);

                                        @endphp

                                            @if(isset($costoinven[0]) and $costoinven[0]->existen !=0)
                                            @php  $nn++;@endphp
                                            <li>
                                                <div class="d-flex  align-items-center"
                                                     style="@if(($nn%2)==0) background: #eee; @endif padding:5px " >

                                                    @php

                                                            $inspadre   = $codinst;
                                                            $tunidades += $costoinven[0]->existen;
                                                            $tcostopro += $costoinven[0]->preciod;


                                                    @endphp

                                                    <div class="flex-grow-1" style="width: 173px">
                                                        {{$sucursal->descrip}}
                                                    </div>

                                                    <div class="flex-grow-1" class="" style="width: 73px;text-align: right;">
                                                        <div class="mb-0 listname" style="width: 73px; text-align: right; font-size: 12px;  ">
                                                            {{($costoinven[0]->existen != 0 and $insPadre == 0)?  $costoinven[0]->existen.'  ' : ''}}

                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                                        <div class="mb-0 listname" style="width: 90px; text-align: right; font-size: 12px;  ">
                                                            {{($costoinven[0]->preciod != 0 and $insPadre == 0)? '$'.number_format($costoinven[0]->preciod,2,',','.'):''}}
                                                        </div>
                                                    </div>

                                                </div>
                                            </li>
                                            @endif
                                    @endforeach
                                    <li  style="color: black; font-weight: bold" class="mb-2">
                                        <div class="d-flex  align-items-center">
                                            <div class="flex-grow-1" style="width: 173px">
                                                <div class="mb-0 listname" > &nbsp;</div>
                                            </div>
                                            <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                                <div class="mb-0 listname" style="width: 125px; text-align: center;  ">
                                                    {{($tunidades != 0)?  $tunidades.'  ' : ''}}
                                                </div>
                                            </div>

                                            <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                                <div class="mb-0 listname" style="width: 90px; text-align: right; ">
                                                    {{($tcostopro != 0)? '$'.number_format($tcostopro ,2,',','.'):''}}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Listado de {{(isset($instanciaselected) and isset($instanciaselected->descrip))?$instanciaselected->descrip:''}}</h4>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                                <thead class="text-muted table-light">
                                <tr>
                                    <th width="10%"  scope="col">CODIGO</th>
                                    <th width="70%" scope="col">PRODUCTO</th>
                                    <th width="10%"  scope="col">COSTO</th>
                                    <th width="10%"  scope="col">EXIST</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $vectorprods = [];

                                @endphp
                                @if(isset($instanciaselected->productosexistencias) and count($instanciaselected->productosexistencias) > 0)

                                    @php
                                         foreach($instanciaselected->productosexistencias as $producto){
                                             if(!isset($vectorprods[$producto->descrip.$producto->codprod]))
                                                    $vectorprods[$producto->descrip.$producto->codprod] = [];

                                             $vectorprods[$producto->descrip.$producto->codprod]['id']         = $producto->id;
                                             $vectorprods[$producto->descrip.$producto->codprod]['codprod']    = $producto->codprod;
                                             $vectorprods[$producto->descrip.$producto->codprod]['descrip']    = $producto->descrip;
                                             $vectorprods[$producto->descrip.$producto->codprod]['preciod'] = $producto->preciod;
                                             $vectorprods[$producto->descrip.$producto->codprod]['existen']    = $producto->existen;

                                         }
                                         ksort($vectorprods);
                                    @endphp
                                    @if(isset($vectorprods) and count($vectorprods)>0)
                                        @foreach($vectorprods as $indexpr => $producto)
                                            <tr>
                                                <td>
                                                    <a href="{{route('productos.edit',$producto['id'])}}" class="fw-medium link-primary">
                                                        {{(isset($producto) and isset($producto['codprod']))?$producto['codprod']: ''}}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{route('productos.edit', $producto['id'])}}" class="fw-medium link-primary">
                                                        {{(isset($producto['descrip']))?$producto['descrip'] : ''}}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{route('productos.edit',$producto['id'])}}" class="fw-medium link-primary">
                                                        {{(isset($producto) and isset($producto['preciod']))? number_format($producto['preciod'],2,',','.') : ''}}
                                                    </a>
                                                </td>
                                                <td align="center" style="border:1px dashed #ccc">
                                                    {{(isset($producto) and isset($producto['existen']))?  ($producto['existen'] +0) : ''}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

