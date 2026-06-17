
<style>
    .card-header {
        border-bottom: 1px solid #132659;
    }

    .tdline{
        border:1px solid #0072c5 !important;
    }
    .tdlineff{
        border-left:1px solid #fff !important;
        color: white !important;
        background-color: #0072c5 !important;
        box-shadow: unset !important;
    }
    .tddd{
        font-size: 11px;
        padding: 2px !important;
    }
</style>
    <div class="card-body  ">
            <div class="row ">
                <div class="col-lg-12">
                    <table width="100%" border="0">
                        <tr>
                            <td>
                                <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">  PROVEEDOR:  {{$documento->descrip}}</p>
                                <h5 class="fs-15 mb-0"> <a id="invoice-no" href="/compra/{{$documento->id}}"><i class="bi bi-link"></i> {{$numerod}}</a></h5>
                            </td>
                            <td>
                                <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">FECHA</p>
                                <h5 class="fs-15 mb-0">
                                    <span id="invoice-date">{{$documento->fechaformat}}</span>
                                </h5>
                            </td>
                            <td>
                                <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Estatus</p>
                                @php
                                    if($documento->status == 0) echo '<span class="badge badge-soft-success" id="payment-status">Cerrada</span>';
                                    if($documento->status == 2) echo '<span class="badge badge-soft-danger"  id="payment-status">Pendiente</span>';
                                    if($documento->status == 1) echo '<span class="badge badge-soft-primary" id="payment-status">Abierta</span>';
                                @endphp
                            </td>
                            <td>
                                <p class="  mb-1" style="color: #132659 !important;"><b>NOTAS1:</b> {{$documento->notas1}} </p>
                                <p class="  mb-1" style="color: #132659 !important;"><b>NOTAS2:</b> {{$documento->notas2}} </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-lg-12">
                    <div class="card-body  ">
                        <div class="table-responsive">
                            <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                <thead>
                                <tr class="table-active">
                                    <th width="1%" class="tdline" >#</th>
                                    <th width="10%" class="text-start tdline">Detalle Producto </th>
                                    @foreach($sucursalesventa as $index =>$sucu)
                                        <th width="15%" class="text-center tdlineff"> {{ $sucu }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody id="products-list">
                                    @foreach($documento->seriales as $index => $item)
                                        @if($item->fk_sucursal == $documento->fk_sucursal)
                                            <tr @if(($index%2)!=0) bgcolor="#f5f8fb" @endif>
                                                <td scope="row" class="tdline tddd" valign="top">{{$index+1}}</td>
                                                <td class="text-start tdline tddd">
                                                    <div style="position: relative">
                                                    <span class="fw-medium">{{  $item->nroserial }}</span>
                                                    <p class="text-muted mb-0">
                                                        {{(isset($item->producto))?  $item->producto->descrip : ''}}
                                                    </p>
                                                    <a href="/operaciones/{{$item->producto->codprod}}/{{urlencode($item->nroserial )}}" style=" position: absolute; right: 0; top: 0"><i class="bi bi-clock-history" style="font-size: 18px;" ></i></a>
                                                    </div>
                                                </td>
                                                @foreach($sucursalesventa as $index =>$sucu)
                                                    <td class="text-center tddd tdline">
                                                        @if(isset($arraysucursales) and isset($arraysucursales[$item->nroserial][$index]) )
                                                        <a href="/doc/Z/{{ $arraysucursales[$item->nroserial][$index] }}/{{$index}}" target="_blank">
                                                            Fac:{{ $arraysucursales[$item->nroserial][$index] }}
                                                        </a>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                            <a href="javascript:window.print()" class="btn btn-success">
                                <i class="ri-printer-line align-bottom me-1"></i> Print
                            </a>
                        </div>

                    </div>
                </div>
            </div>
    </div>

</div>
